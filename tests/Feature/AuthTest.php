<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Sign-in, sign-out, the forced password change, and who may open which page.
 * Runs on the separate CodeBarkada_Club_test database (see phpunit.xml),
 * which RefreshDatabase empties before each test.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_are_sent_to_the_sign_in_page(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/adviser')->assertRedirect('/login');
    }

    public function test_the_sign_in_page_loads(): void
    {
        $this->get('/login')->assertOk()->assertSee('School email');
    }

    public function test_a_non_school_email_is_refused(): void
    {
        $this->post('/login', ['email' => 'ana@gmail.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_wrong_password_is_refused(): void
    {
        $member = User::factory()->create();

        $this->post('/login', ['email' => $member->email, 'password' => 'not-the-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_member_signs_in_and_lands_on_the_member_home(): void
    {
        $member = User::factory()->create();

        $this->post('/login', ['email' => $member->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($member);
    }

    public function test_a_user_with_a_default_password_must_change_it_first(): void
    {
        $adviser = User::factory()->create(['role' => 'adviser', 'must_change_password' => true]);

        $this->actingAs($adviser)->get('/adviser')->assertRedirect('/password');
    }

    public function test_changing_the_password_clears_the_flag(): void
    {
        $adviser = User::factory()->create(['role' => 'adviser', 'must_change_password' => true]);

        $this->actingAs($adviser)->put('/password', [
            'current_password' => 'password',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect('/adviser');

        $adviser->refresh();
        $this->assertFalse($adviser->must_change_password);
        $this->assertTrue(Hash::check('NewPassword123', $adviser->password));

        // This device stays signed in and can now open the adviser page.
        $this->get('/adviser')->assertOk();
        $this->assertAuthenticatedAs($adviser);
    }

    public function test_a_password_change_signs_out_other_devices(): void
    {
        $member = User::factory()->create();

        // This "other device" is signed in and has opened a page.
        $this->actingAs($member)->get('/')->assertOk();

        // Meanwhile the password is changed somewhere else.
        $member->update(['password' => 'ChangedElsewhere1']);

        // The other device's next click sends it back to sign-in.
        $this->get('/')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_the_new_password_cannot_be_the_old_one(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->put('/password', [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');
    }

    public function test_members_cannot_open_adviser_pages(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get('/adviser')->assertRedirect('/');
    }

    public function test_the_adviser_is_sent_to_the_adviser_home(): void
    {
        $adviser = User::factory()->create(['role' => 'adviser']);

        $this->actingAs($adviser)->get('/')->assertRedirect('/adviser');
        $this->actingAs($adviser)->get('/adviser')->assertOk()->assertSee('Adviser panel');
    }

    public function test_signing_out_works(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }
}
