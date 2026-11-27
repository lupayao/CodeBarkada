<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The adviser's Manage page: reset a member's password, delete a member.
 */
class AdviserManageMemberTest extends TestCase
{
    use RefreshDatabase;

    private function adviser(): User
    {
        return User::factory()->create(['role' => 'adviser', 'codename' => null]);
    }

    /** Pretend the member is signed in somewhere: one saved session row. */
    private function giveSession(User $user): void
    {
        DB::table('sessions')->insert([
            'id' => 'session-'.$user->id,
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => time(),
        ]);
    }

    public function test_the_adviser_sees_the_manage_page(): void
    {
        $member = User::factory()->create(['name' => 'Ana Reyes']);

        $this->actingAs($this->adviser())->get("/adviser/members/{$member->id}")
            ->assertOk()
            ->assertSee('Ana Reyes')
            ->assertSee('Reset password')
            ->assertSee('Delete member');
    }

    public function test_the_members_list_links_to_manage(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->adviser())->get('/adviser/members')
            ->assertSee(route('adviser.members.show', $member));
    }

    public function test_members_cannot_manage_anyone(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($member)->get("/adviser/members/{$other->id}")->assertRedirect('/');
        $this->actingAs($member)->put("/adviser/members/{$other->id}/password", ['default_password' => 'Barkada2026'])->assertRedirect('/');
        $this->actingAs($member)->delete("/adviser/members/{$other->id}", ['confirm' => '1'])->assertRedirect('/');

        $this->assertModelExists($other);
    }

    public function test_the_adviser_account_and_unknown_ids_are_not_found(): void
    {
        $adviser = $this->adviser();

        $this->actingAs($adviser)->get("/adviser/members/{$adviser->id}")->assertNotFound();
        $this->actingAs($adviser)->delete("/adviser/members/{$adviser->id}", ['confirm' => '1'])->assertNotFound();
        $this->actingAs($adviser)->get('/adviser/members/99999')->assertNotFound();

        $this->assertModelExists($adviser);
    }

    public function test_resetting_a_password(): void
    {
        $member = User::factory()->create(['remember_token' => 'old-token']);
        $this->giveSession($member);

        $this->actingAs($this->adviser())->put("/adviser/members/{$member->id}/password", [
            'default_password' => 'Barkada2026',
        ])->assertRedirect('/adviser/members')->assertSessionHas('status');

        $member->refresh();
        $this->assertTrue(Hash::check('Barkada2026', $member->password));
        $this->assertTrue($member->must_change_password);
        $this->assertNull($member->remember_token);
        // Signed out everywhere: their saved sessions are gone.
        $this->assertSame(0, DB::table('sessions')->where('user_id', $member->id)->count());
    }

    public function test_after_a_reset_the_member_signs_in_and_must_change_it(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->adviser())->put("/adviser/members/{$member->id}/password", [
            'default_password' => 'Barkada2026',
        ]);
        $this->post('/logout');

        $this->post('/login', ['email' => $member->email, 'password' => 'Barkada2026'])->assertRedirect('/');
        $this->get('/')->assertRedirect('/password');
    }

    public function test_a_short_reset_password_is_refused(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->adviser())->put("/adviser/members/{$member->id}/password", [
            'default_password' => 'short',
        ])->assertSessionHasErrors('default_password');

        $this->assertFalse($member->fresh()->must_change_password);
    }

    public function test_delete_needs_the_confirm_box(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->adviser())->delete("/adviser/members/{$member->id}")
            ->assertSessionHasErrors('confirm');

        $this->assertModelExists($member);
    }

    public function test_deleting_a_member(): void
    {
        $member = User::factory()->create(['name' => 'Ana Reyes']);
        $this->giveSession($member);

        $this->actingAs($this->adviser())->delete("/adviser/members/{$member->id}", ['confirm' => '1'])
            ->assertRedirect('/adviser/members')
            ->assertSessionHas('status', 'Ana Reyes was deleted.');

        $this->assertModelMissing($member);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $member->id)->count());
    }
}
