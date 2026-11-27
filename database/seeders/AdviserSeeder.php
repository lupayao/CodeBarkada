<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\CodeBarkadaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates the one club adviser account from the ADVISER_* lines in .env.
 * Run with: php artisan db:seed --class=AdviserSeeder
 */
class AdviserSeeder extends Seeder
{
    public function run(CodeBarkadaService $codeBarkada): void
    {
        $adviser = config('codebarkada.adviser');

        // Stop early with a clear message if .env is missing the lines.
        if (empty($adviser['email']) || empty($adviser['password'])) {
            $this->command->error('Add ADVISER_EMAIL and ADVISER_PASSWORD to .env first.');

            return;
        }

        // School rule: the adviser needs a school email too.
        if (! $codeBarkada->isSchoolEmail($adviser['email'])) {
            $this->command->error('ADVISER_EMAIL must end with '.config('codebarkada.email_domain'));

            return;
        }

        // firstOrCreate: running this twice will not make a second adviser
        // or overwrite a password the adviser already changed.
        $user = User::firstOrCreate(
            ['email' => Str::lower($adviser['email'])],
            [
                'name' => $adviser['name'],
                'password' => $adviser['password'],
                'role' => User::ROLE_ADVISER,
                'must_change_password' => true,
            ],
        );

        $this->command->info(
            $user->wasRecentlyCreated
                ? "Adviser account created: {$user->email}"
                : "Adviser account already exists: {$user->email}"
        );
    }
}
