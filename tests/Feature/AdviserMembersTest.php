<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The adviser's Members page: adding members by pasting a list, and the member table.
 */
class AdviserMembersTest extends TestCase
{
    use RefreshDatabase;

    private function adviser(): User
    {
        return User::factory()->create(['role' => 'adviser', 'codename' => null]);
    }

    public function test_members_cannot_open_the_members_page(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get('/adviser/members')->assertRedirect('/');
        $this->actingAs($member)->post('/adviser/members', [])->assertRedirect('/');
    }

    public function test_the_adviser_sees_the_members_page(): void
    {
        $this->actingAs($this->adviser())->get('/adviser/members')
            ->assertOk()
            ->assertSee('Add members');
    }

    public function test_pasted_lines_with_commas_or_tabs_are_added(): void
    {
        $list = "ana@crc.pshs.edu.ph, Ana, 9, Ruby\n"
            ."ben@crc.pshs.edu.ph\tBen\t10\tEmerald\n"
            ."Cara@CRC.pshs.edu.ph";

        $this->actingAs($this->adviser())->post('/adviser/members', [
            'members' => $list,
            'default_password' => 'Barkada2026',
        ])->assertRedirect('/adviser/members')
            ->assertSessionHas('status', 'Added 3 members.');

        $ana = User::where('email', 'ana@crc.pshs.edu.ph')->first();
        $this->assertSame('Ana', $ana->name);
        $this->assertSame('9', $ana->grade);
        $this->assertSame('Ruby', $ana->section);
        $this->assertTrue($ana->isMember());
        $this->assertTrue($ana->must_change_password);

        $this->assertSame('10', User::where('email', 'ben@crc.pshs.edu.ph')->value('grade'));

        // No first name: the name is the part before the @. Capitals are lowered.
        $this->assertSame('cara', User::where('email', 'cara@crc.pshs.edu.ph')->value('name'));
    }

    public function test_non_school_and_repeated_emails_are_not_added(): void
    {
        User::factory()->create(['email' => 'ana@crc.pshs.edu.ph']);

        $list = "ana@crc.pshs.edu.ph\nsomeone@gmail.com\nben@crc.pshs.edu.ph\nben@crc.pshs.edu.ph";

        $this->actingAs($this->adviser())->post('/adviser/members', [
            'members' => $list,
            'default_password' => 'Barkada2026',
        ])->assertSessionHas('status',
            'Added 1 member. Already added: ben@crc.pshs.edu.ph, ana@crc.pshs.edu.ph. Refused (not a @crc.pshs.edu.ph email): someone@gmail.com.');

        $this->assertSame(1, User::where('email', 'ben@crc.pshs.edu.ph')->count());
        $this->assertSame(1, User::where('email', 'ana@crc.pshs.edu.ph')->count());
        $this->assertFalse(User::where('email', 'someone@gmail.com')->exists());
    }

    public function test_a_grade_outside_7_to_10_is_left_empty(): void
    {
        $this->actingAs($this->adviser())->post('/adviser/members', [
            'members' => 'dan@crc.pshs.edu.ph, Dan, 12, Topaz',
            'default_password' => 'Barkada2026',
        ]);

        $this->assertNull(User::where('email', 'dan@crc.pshs.edu.ph')->value('grade'));
    }

    public function test_a_short_default_password_is_refused(): void
    {
        $this->actingAs($this->adviser())->post('/adviser/members', [
            'members' => 'ana@crc.pshs.edu.ph',
            'default_password' => 'short',
        ])->assertSessionHasErrors('default_password');

        $this->assertFalse(User::where('email', 'ana@crc.pshs.edu.ph')->exists());
    }

    public function test_a_new_member_signs_in_with_the_default_password_and_must_change_it(): void
    {
        $this->actingAs($this->adviser())->post('/adviser/members', [
            'members' => 'ana@crc.pshs.edu.ph, Ana',
            'default_password' => 'Barkada2026',
        ]);
        $this->post('/logout');

        $this->post('/login', ['email' => 'ana@crc.pshs.edu.ph', 'password' => 'Barkada2026'])
            ->assertRedirect('/');
        $this->get('/')->assertRedirect('/password');
    }

    public function test_the_table_shows_each_members_status(): void
    {
        User::factory()->newMember()->create(['name' => 'New Ana']);
        User::factory()->newMember()->create(['name' => 'Setup Ben', 'must_change_password' => false]);
        User::factory()->create(['name' => 'Active Cara']);

        $this->actingAs($this->adviser())->get('/adviser/members')
            ->assertOk()
            ->assertSeeInOrder(['New Ana', 'Needs password change'])
            ->assertSeeInOrder(['Setup Ben', 'Needs setup'])
            ->assertSeeInOrder(['Active Cara', 'Active'])
            ->assertSee('All members (3)');
    }
}
