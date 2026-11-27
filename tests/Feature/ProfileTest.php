<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The member Profile page and the member menu.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_members_with_a_finished_setup_can_open_the_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');

        $adviser = User::factory()->create(['role' => 'adviser', 'codename' => null]);
        $this->actingAs($adviser)->get('/profile')->assertRedirect('/adviser');

        $fresh = User::factory()->newMember()->create(['must_change_password' => false]);
        $this->actingAs($fresh)->get('/profile')->assertRedirect('/welcome');
    }

    public function test_the_profile_shows_the_current_choices(): void
    {
        $member = User::factory()->create([
            'name' => 'Ana Reyes', 'codename' => 'ByteBayani', 'track' => 'cpp', 'avatar' => 'bot-280',
            'grade' => '9', 'section' => 'Ruby',
        ]);

        $this->actingAs($member)->get('/profile')
            ->assertOk()
            ->assertSee('Ana Reyes')
            ->assertSee('9-Ruby')
            ->assertSee('value="ByteBayani"', false)
            ->assertSee('id="track-cpp" value="cpp" required'."\n".'                           checked', false)
            ->assertSee('id="color-280" value="280" required'."\n".'                           checked', false);
    }

    public function test_saving_new_choices(): void
    {
        $member = User::factory()->create(['codename' => 'OldName', 'track' => 'py', 'avatar' => 'bot-200']);

        $this->actingAs($member)->put('/profile', [
            'codename' => 'NewName',
            'track' => 'cpp',
            'color' => 330,
        ])->assertRedirect('/profile')->assertSessionHas('status', 'Profile saved.');

        $member->refresh();
        $this->assertSame('NewName', $member->codename);
        $this->assertSame('cpp', $member->track);
        $this->assertSame(330, $member->avatarColor());
    }

    public function test_keeping_the_same_codename_is_allowed(): void
    {
        $member = User::factory()->create(['codename' => 'ByteBayani']);

        $this->actingAs($member)->put('/profile', [
            'codename' => 'ByteBayani',
            'track' => 'py',
            'color' => 45,
        ])->assertSessionHasNoErrors();
    }

    public function test_a_codename_taken_by_someone_else_is_refused(): void
    {
        User::factory()->create(['codename' => 'ByteBayani']);
        $member = User::factory()->create(['codename' => 'Mine']);

        $this->actingAs($member)->put('/profile', [
            'codename' => 'BYTEBAYANI',
            'track' => 'py',
            'color' => 45,
        ])->assertSessionHasErrors('codename');

        $this->assertSame('Mine', $member->fresh()->codename);
    }

    public function test_members_see_the_member_menu_and_the_adviser_does_not(): void
    {
        $member = User::factory()->create();
        $this->actingAs($member)->get('/')
            ->assertSee('Member menu')
            ->assertSee(route('profile.edit'));

        $adviser = User::factory()->create(['role' => 'adviser', 'codename' => null]);
        $this->actingAs($adviser)->get('/adviser')
            ->assertSee('Adviser menu')
            ->assertDontSee('Member menu');
    }
}
