<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hobby;
use App\Services\ReputationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class ReputationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reputation_calculation_formula()
    {
        // 1. Setup User (Age 90 days = 3 points)
        $user = User::factory()->create(['created_at' => Carbon::now()->subDays(90)]);

        // 2. Setup Friends (2 friends)
        $friend1 = User::factory()->create();
        $friend2 = User::factory()->create();

        $user->friends()->attach($friend1->id);
        $user->friends()->attach($friend2->id);

        // 3. Setup Shared Hobbies (1 shared hobby)
        $hobby = Hobby::create(['name' => 'Coding']);
        $user->hobbies()->attach($hobby->id);
        $friend1->hobbies()->attach($hobby->id); // Shared
        // friend2 has no hobbies

        // 4. Run Service
        $service = new ReputationService();
        $service->calculateAndSave($user);

        // 5. Assertions
        // Friends: 2
        // Shared Hobbies: 1 * 0.5 = 0.5
        // Age: 90/30 = 3
        // Blocked: 0
        // Total: 2 + 0.5 + 3 = 5.5

        $this->assertEquals(5.5, $user->fresh()->reputation_score);
    }
}
