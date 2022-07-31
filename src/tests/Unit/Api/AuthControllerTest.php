<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\BookCategory;

class AuthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Plan::factory()->create(['name' => 'premier']);
    }

    /** @test */
    public function サインアップができること(): void
    {
        $response = $this->json('POST', '/api/signUp', [
            'name' => '検証 太郎',
            'email' => 'aaa@test.com',
            'password' => 'password',
            'client_name' => '株式会社検証',
            'plan' => 'premier',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'me' => [
                'name' => '検証 太郎',
                'email' => 'aaa@test.com',
                'role' => [
                    'is_account_manager' => true,
                    'is_book_manager' => true,
                    'is_client_manager' => true,
                ],
            ],
        ]);

        $client = Client::where('name', '株式会社検証')->first();
        $user = User::where('email', 'aaa@test.com')->first();
        $role = Role::where('user_id', $user->id)->first();
        $bookCategory = BookCategory::where('client_id', $client->id)->first();

        $this->assertNotNull($client);
        $this->assertNotNull($client);
        $this->assertNotNull($role);
        $this->assertNotNull($bookCategory);
    }

    /** @test */
    public function サインアップバリデーションが機能していること(): void
    {
        $response = $this->json('POST', '/api/signUp');
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'email' => ['メールアドレスは必ず指定してください。'],
                'password' => ['パスワードは必ず指定してください。'],
                'client_name' => ['組織名は必ず指定してください。'],
                'plan' => ['プランは必ず指定してください。'],
            ],
        ]);
    }
}
