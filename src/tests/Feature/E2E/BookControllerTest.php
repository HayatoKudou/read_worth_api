<?php

namespace Tests\Feature\E2E;

use Tests\TestCase;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class BookControllerTest extends TestCase
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
    public function 書籍一覧が取得できること(): void
    {
        $response = $this->json('GET', '/api/' . $this->workspace->id . '/books', [], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function 書籍追加ができること(): void
    {
        $response = $this->json('POST', '/api/' . $this->workspace->id . '/book', [
            'category' => 'IT',
            'title' => 'すごい本',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    public function 書籍削除ができること(): void
    {
        $response = $this->json('DELETE', '/api/' . $this->workspace->id . '/book', [
            'bookIds' => [1],
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function 書籍編集ができること(): void
    {
        $book = Book::factory()->create([
            'workspace_id' => $this->workspace,
        ]);
        $response = $this->json('PUT', '/api/' . $this->workspace->id . '/book', [
            'id' => $book->id,
            'title' => 'やばい本',
            'description' => 'やばばばば',
            'category' => 'IT',
            'status' => '1',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
        $response->assertStatus(200);
    }
}
