<?php

namespace Tests\Feature\E2E;

use Tests\TestCase;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookCategoryControllerTest extends TestCase
{
    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        $user = User::factory()->create([
            'api_token' => 'aaaaaaa',
        ]);
        $role = Role::factory()->create();
        Belonging::factory()->create([
            'user_id' => $user->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => $role->id,
        ]);
    }

    /** @test */
    public function 書籍カテゴリの登録ができること(): void
    {
        $response = $this->json('POST', '/api/' . $this->workspace->id . '/bookCategory', [
            'name' => 'マネジメント',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    public function 書籍カテゴリの削除ができること(): void
    {
        BookCategory::factory()->create([
            'workspace_id' => $this->workspace->id,
            'name' => 'マネジメント',
        ]);
        $response = $this->json('DELETE', '/api/' . $this->workspace->id . '/bookCategory', [
            'name' => 'マネジメント',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }
}
