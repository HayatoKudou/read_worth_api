<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::factory()->create(['name' => 'premier']);
        $client = Client::factory()->create([
            'name' => '株式会社かぶかぶ',
            'plan_id' => $plan->id,
        ]);
        $this->user = User::factory()->create([
            'client_id' => $client->id,
            'name' => '佐藤 太郎',
            'email' => 'aaabbbccc@test.com',
            'password' => Hash::make('pass'),
        ]);
        Role::factory()->create([
            'user_id' => $this->user->id,
            'is_account_manager' => 1,
            'is_book_manager' => 1,
            'is_client_manager' => 1,
        ]);
    }

    /** @test */
    public function Googleコールバック時にデータが作成されること(): void
    {
        $this->json('POST', '/connect/google-callback', [
            'email' => 'aaabbbccc@test.com',
            'password' => 'pass',
        ])->assertStatus(200)
            ->assertJson([
            'me' => [
                'name' => '佐藤 太郎',
                'email' => 'aaabbbccc@test.com',
                'role' => [
                    'is_account_manager' => true,
                    'is_book_manager' => true,
                    'is_client_manager' => true,
                ],
            ],
        ]);
    }
}
