<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The member first-sign-in setup: codename, track and coder-bot color.
 */
class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    /** A member who changed their password but hasn't done the welcome setup. */
    private function freshMember(): User
    {
        return User::factory()->newMember()->create(['must_change_password' => false]);
    }

    public function test_a_member_without_a_profile_is_sent_to_welcome(): void
    {
        $this->actingAs($this->freshMember())->get('/')->assertRedirect('/welcome');
    }

    public function test_the_welcome_page_loads(): void
    {
        $this->actingAs($this->freshMember())->get('/welcome')
            ->assertOk()
            ->assertSee('Codename')
            ->assertSee('Python')
            ->assertSee('C++');
    }

    public function test_the_adviser_never_sees_the_welcome_page(): void
    {
        $adviser = User::factory()->create(['role' => 'adviser', 'codename' => null]);

        $this->actingAs($adviser)->get('/adviser')->assertOk();
        $this->actingAs($adviser)->get('/welcome')->assertRedirect('/adviser');
    }

    public function test_saving_the_profile_opens_the_hub(): void
    {
        $member = $this->freshMember();

        $this->actingAs($member)->put('/welcome', [
            'codename' => 'ByteBayani',
            'track' => 'cpp',
            'color' => 280,
        ])->assertRedirect('/');

        $member->refresh();
        $this->assertSame('ByteBayani', $member->codename);
        $this->assertSame('cpp', $member->track);
        $this->assertSame(280, $member->avatarColor());

        $this->get('/')->assertOk()->assertSee('ByteBayani');
    }

    public function test_a_taken_codename_is_refused_even_with_different_capitals(): void
    {
        User::factory()->create(['codename' => 'ByteBayani']);

        $this->actingAs($this->freshMember())->put('/welcome', [
            'codename' => 'bytebayani',
            'track' => 'py',
            'color' => 200,
        ])->assertSessionHasErrors('codename');
    }

    public function test_a_codename_must_be_3_to_18_characters(): void
    {
        $member = $this->freshMember();

        $this->actingAs($member)->put('/welcome', ['codename' => 'ab', 'track' => 'py', 'color' => 200])
            ->assertSessionHasErrors('codename');

        $this->actingAs($member)->put('/welcome', ['codename' => str_repeat('a', 19), 'track' => 'py', 'color' => 200])
            ->assertSessionHasErrors('codename');
    }

    public function test_an_unknown_track_or_color_is_refused(): void
    {
        $this->actingAs($this->freshMember())->put('/welcome', [
            'codename' => 'ByteBayani',
            'track' => 'java',
            'color' => 999,
        ])->assertSessionHasErrors(['track', 'color']);
    }

    public function test_a_member_must_change_the_default_password_before_welcome(): void
    {
        $member = User::factory()->newMember()->create();

        $this->actingAs($member)->get('/welcome')->assertRedirect('/password');
    }
}
