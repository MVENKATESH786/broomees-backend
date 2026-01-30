<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\TokenRepository;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $repo = new TokenRepository();
        $this->token = $repo->createToken();
    }

    public function test_optimistic_locking_conflict()
    {
        $user = User::create(['username' => 'test', 'age' => 20, 'version' => 1]);
        $versionA = $user->version;

        // User B updates
        $this->withToken($this->token)->putJson("/api/users/{$user->id}", [
            'age' => 21,
            'version' => 1
        ])->assertStatus(200);

        // User A tries to update with old version
        $this->withToken($this->token)->putJson("/api/users/{$user->id}", [
            'age' => 22,
            'version' => $versionA
        ])->assertStatus(409);
    }

    public function test_rate_limiting_breach()
    {
        for ($i = 0; $i < 30; $i++) {
            $this->withToken($this->token)->postJson('/api/users', ['username' => "u{$i}", 'age' => 20]);
        }
        $this->withToken($this->token)
            ->postJson('/api/users', ['username' => 'fail', 'age' => 20])
            ->assertStatus(429);
    }
}
