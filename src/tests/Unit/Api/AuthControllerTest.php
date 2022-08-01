<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\BookCategory;
use Illuminate\Support\Facades\Auth;
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
    public function サインインができること(): void
    {
        $this->json('POST', '/api/signIn', [
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

    /** @test */
    public function サインインバリデーションが機能していること(): void
    {
        $this->json('POST', '/api/signIn')
            ->assertStatus(422)
            ->assertJson([
            'errors' => [
                'email' => ['メールアドレスは必ず指定してください。'],
                'password' => ['パスワードは必ず指定してください。'],
            ],
        ]);
    }

    /** @test */
    public function サインインメール認証チェックが機能していること(): void
    {
        $this->user->update(['email_verified_at' => null]);
        $this->json('POST', '/api/signIn', [
            'email' => 'aaabbbccc@test.com',
            'password' => 'pass',
        ])
            ->assertStatus(403)
            ->assertJson(['errors' => ['custom' => 'メール認証を完了させてください']]);
    }

    /** @test */
    public function サインインカスタムエラーが機能していること(): void
    {
        $this->json('POST', '/api/signIn', [
            'email' => 'hogehoge',
            'password' => 'hogehoge',
        ])
            ->assertStatus(401)
            ->assertJson([
            'errors' => [
                'custom' => 'メールアドレスもしくはパスワードが一致しません',
            ],
        ]);
    }

    /** @test */
    public function サインアップができること(): void
    {
        $this->json('POST', '/api/signUp', [
            'name' => '検証 太郎',
            'email' => 'aaa@test.com',
            'password' => 'password',
            'client_name' => '株式会社検証',
            'plan' => 'premier',
        ])
            ->assertStatus(200)
            ->assertJson([
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
        $this->json('POST', '/api/signUp')
            ->assertStatus(422)
            ->assertJson([
            'errors' => [
                'email' => ['メールアドレスは必ず指定してください。'],
                'password' => ['パスワードは必ず指定してください。'],
                'client_name' => ['組織名は必ず指定してください。'],
                'plan' => ['プランは必ず指定してください。'],
            ],
        ]);
    }

    /** @test */
    public function パスワード設定ができること(): void
    {
//        Auth::shouldReceive('id')->andReturn($this->user->id);
//         $this->json('POST', '/api/password-setting', [
//            'password' => 'repass',
//            'password_confirmation' => 'repass'
//        ]);
//        $this->assertSame(Hash::make('repass'), $this->user->password);
    }
}
