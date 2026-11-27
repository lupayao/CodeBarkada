<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CodeBarkadaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The adviser dashboard: stat tiles, sign-up progress, tracks, grades,
 * needs attention and recently added.
 */
class AdviserDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function adviser(): User
    {
        return User::factory()->create(['role' => 'adviser', 'codename' => null]);
    }

    /** 3 active, 2 needing setup, 1 needing a password change = 6 members. */
    private function makeClub(): void
    {
        User::factory()->create(['track' => 'py', 'grade' => '9']);
        User::factory()->create(['track' => 'py', 'grade' => '9']);
        User::factory()->create(['track' => 'cpp', 'grade' => '10']);
        User::factory()->newMember()->count(2)->create(['must_change_password' => false, 'grade' => '7']);
        User::factory()->newMember()->create(['name' => 'Fresh Ana']);   // no grade
    }

    public function test_only_the_adviser_sees_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())->get('/adviser')->assertRedirect('/');

        $this->actingAs($this->adviser())->get('/adviser')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_the_numbers_are_counted_correctly(): void
    {
        $this->makeClub();
        $stats = app(CodeBarkadaService::class)->dashboard(User::where('role', 'member')->get());

        $this->assertSame(6, $stats['total']);
        $this->assertSame([3, 2, 1], array_column($stats['statuses'], 'count'));
        $this->assertSame([50, 33, 17], array_column($stats['statuses'], 'percent'));

        // Tracks: only the 3 who finished setup.
        $this->assertSame(3, $stats['tracksTotal']);
        $this->assertSame([2, 1], array_column($stats['tracks'], 'count'));

        // Grades 7, 8, 9, 10, then no grade.
        $this->assertSame([2, 0, 2, 1, 1], array_column($stats['grades'], 'count'));

        $this->assertSame(3, $stats['needsAttentionCount']);
    }

    public function test_the_page_shows_the_tiles_and_lists(): void
    {
        $this->makeClub();

        $this->actingAs($this->adviser())->get('/adviser')
            ->assertOk()
            ->assertSeeInOrder(['Total members', '6'])
            ->assertSeeInOrder(['Active', '3'])
            ->assertSeeInOrder(['Needs setup', '2'])
            ->assertSeeInOrder(['Needs password change', '1'])
            ->assertSee('Needs attention (3)')
            ->assertSee('Fresh Ana')
            ->assertSee('Recently added');
    }

    public function test_needs_attention_lists_only_unfinished_members_with_manage_links(): void
    {
        $active = User::factory()->create(['name' => 'Done Dana']);
        $fresh = User::factory()->newMember()->create(['name' => 'Fresh Ana']);

        $stats = app(CodeBarkadaService::class)->dashboard(User::where('role', 'member')->get());
        $this->assertSame([$fresh->id], $stats['needsAttention']->pluck('member.id')->all());

        $this->actingAs($this->adviser())->get('/adviser')
            ->assertSee(route('adviser.members.show', $fresh))
            ->assertDontSee(route('adviser.members.show', $active));
    }

    public function test_recently_added_shows_the_newest_five_first(): void
    {
        foreach (range(1, 7) as $day) {
            User::factory()->create(['name' => "Member {$day}", 'created_at' => now()->subDays(10 - $day)]);
        }

        $stats = app(CodeBarkadaService::class)->dashboard(User::where('role', 'member')->get());

        $this->assertSame(
            ['Member 7', 'Member 6', 'Member 5', 'Member 4', 'Member 3'],
            $stats['recent']->pluck('name')->all()
        );
    }

    public function test_an_empty_club_shows_a_starting_message(): void
    {
        $this->actingAs($this->adviser())->get('/adviser')
            ->assertOk()
            ->assertSee('No members yet')
            ->assertDontSee('Sign-up progress');
    }

    public function test_the_number_of_queries_does_not_grow_with_the_club(): void
    {
        $adviser = $this->adviser();

        User::factory()->count(3)->create();
        $few = $this->countQueries(fn () => $this->actingAs($adviser)->get('/adviser')->assertOk());

        User::factory()->count(30)->create();
        $many = $this->countQueries(fn () => $this->actingAs($adviser)->get('/adviser')->assertOk());

        $this->assertSame($few, $many);
    }

    private function countQueries(callable $request): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $request();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }
}
