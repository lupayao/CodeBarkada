<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * All CodeBarkada school rules live here: typing speed scores,
 * quiz scoring, skill profiles, rankings.
 * Controllers ask this class; views only show the answers.
 */
class CodeBarkadaService
{
    /**
     * The club's one-line description, shown on the home page.
     */
    public function tagline(): string
    {
        return 'Practice typing, sharpen your coding logic, and get ready for CS competitions.';
    }

    /**
     * School rule: every account must use a school email (@crc.pshs.edu.ph).
     * Capital letters don't matter, so "Ana@CRC.pshs.edu.ph" is fine too.
     */
    public function isSchoolEmail(string $email): bool
    {
        $email = Str::lower(trim($email));
        $domain = config('codebarkada.email_domain');

        // Something must come before the @, e.g. "ana@crc.pshs.edu.ph".
        return Str::endsWith($email, $domain) && Str::length($email) > Str::length($domain);
    }

    /**
     * Where each kind of user lands after signing in (a route name).
     */
    public function homeRouteFor(User $user): string
    {
        return $user->isAdviser() ? 'adviser.home' : 'home';
    }

    /**
     * Rules for a new password: at least 8 characters, typed twice
     * (the second box is "password_confirmation"), and not the same as the old one.
     */
    public function newPasswordRules(): array
    {
        return ['required', 'confirmed', 'different:current_password', Password::min(8)];
    }

    /**
     * The learning tracks a member can choose.
     */
    public function tracks(): array
    {
        return [
            'py' => ['name' => 'Python', 'about' => 'Friendly syntax, great for ICSC conceptual + coding problems.'],
            'cpp' => ['name' => 'C++', 'about' => 'The speed language of NOI.PH and olympiad judges.'],
        ];
    }

    /**
     * The coder-bot colors (hue numbers on the color wheel, 0–360).
     */
    public function botColors(): array
    {
        return [45, 12, 200, 160, 280, 330, 100, 230];
    }

    /**
     * How a bot avatar is saved in the "avatar" column, e.g. "bot-200".
     */
    public function botAvatar(int $color): string
    {
        return 'bot-'.$color;
    }

    /**
     * A member must finish the welcome setup (pick a codename) before using the hub.
     */
    public function needsProfile(User $user): bool
    {
        return $user->isMember() && blank($user->codename);
    }

    /**
     * Rules for the welcome form.
     * The database compares codenames without caring about capital letters,
     * so "ByteBayani" and "bytebayani" count as the same codename.
     */
    public function profileRules(User $user): array
    {
        return [
            'codename' => ['required', 'string', 'min:3', 'max:18', Rule::unique('users', 'codename')->ignore($user->id)],
            'track' => ['required', Rule::in(array_keys($this->tracks()))],
            'color' => ['required', 'integer', Rule::in($this->botColors())],
        ];
    }

    /**
     * Rules for the default password the adviser gives new members.
     */
    public function defaultPasswordRules(): array
    {
        return ['required', 'string', Password::min(8)];
    }

    /**
     * Read the adviser's pasted member list.
     * One member per line: email, first name, grade, section
     * (separated by commas, tabs or semicolons; only the email is required).
     *
     * Gives back three groups:
     *   toAdd    - new members, ready to save
     *   existing - emails that already have an account (or appear twice in the paste)
     *   refused  - lines that are not a school email
     */
    public function parseMemberList(string $text): array
    {
        $toAdd = [];
        $existing = [];
        $refused = [];

        $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text)));

        foreach ($lines as $line) {
            $parts = array_map('trim', preg_split('/\t|,|;/', $line));
            $email = Str::lower($parts[0]);

            if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $this->isSchoolEmail($email)) {
                $refused[] = $parts[0];

                continue;
            }

            if (isset($toAdd[$email])) {
                $existing[] = $email;   // the same email twice in one paste

                continue;
            }

            $grade = $parts[2] ?? '';

            $toAdd[$email] = [
                'email' => $email,
                // No first name given? Use the part before the @ (e.g. "ana").
                'name' => ($parts[1] ?? '') !== '' ? $parts[1] : Str::before($email, '@'),
                // Only Grades 7 to 10 are accepted; anything else is left empty.
                'grade' => in_array($grade, ['7', '8', '9', '10'], true) ? $grade : null,
                'section' => ($parts[3] ?? '') !== '' ? $parts[3] : null,
            ];
        }

        // One database query for all the emails, not one per line.
        $taken = User::whereIn('email', array_keys($toAdd))->pluck('email')->all();

        foreach ($taken as $email) {
            $existing[] = $email;
            unset($toAdd[$email]);
        }

        return [
            'toAdd' => array_values($toAdd),
            'existing' => $existing,
            'refused' => $refused,
        ];
    }

    /**
     * Add the pasted members with the default password.
     * Every new member must change the password at first sign-in.
     * Gives back a short summary for the adviser.
     */
    public function addMembers(string $text, string $defaultPassword): string
    {
        $list = $this->parseMemberList($text);

        // All or nothing: if one save fails, none are saved.
        DB::transaction(function () use ($list, $defaultPassword) {
            foreach ($list['toAdd'] as $member) {
                User::create($member + [
                    'password' => $defaultPassword,
                    'role' => User::ROLE_MEMBER,
                    'must_change_password' => true,
                ]);
            }
        });

        $count = count($list['toAdd']);
        $summary = [$count === 1 ? 'Added 1 member.' : "Added {$count} members."];

        if ($list['existing']) {
            $summary[] = 'Already added: '.implode(', ', $list['existing']).'.';
        }
        if ($list['refused']) {
            $summary[] = 'Refused (not a '.config('codebarkada.email_domain').' email): '.implode(', ', $list['refused']).'.';
        }

        return implode(' ', $summary);
    }

    /**
     * Where a member is in the sign-up process, for the adviser's table.
     * "tone" picks the pill color in the CSS.
     */
    public function memberStatus(User $member): array
    {
        if ($member->must_change_password) {
            return ['label' => 'Needs password change', 'tone' => 'coral'];
        }

        if ($this->needsProfile($member)) {
            return ['label' => 'Needs setup', 'tone' => 'sun'];
        }

        return ['label' => 'Active', 'tone' => 'good'];
    }
}
