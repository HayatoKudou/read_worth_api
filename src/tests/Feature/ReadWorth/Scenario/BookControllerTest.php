<?php

namespace Tests\Feature\ReadWorth\Scenario;

 use Tests\TestCase;
 use ReadWorth\Infrastructure\EloquentModel\Role;
 use ReadWorth\Infrastructure\EloquentModel\User;
 use ReadWorth\Infrastructure\EloquentModel\Belonging;
 use ReadWorth\Infrastructure\EloquentModel\Workspace;
 use ReadWorth\Infrastructure\EloquentModel\BookCategory;

 class BookControllerTest extends TestCase
 {
     private Workspace $workspace;

     protected function setUp(): void
     {
         parent::setUp();

         $this->workspace = Workspace::factory()->create();
         $user = User::factory()->create(['api_token' => 'aaaaaaa']);
         $role = Role::factory()->create();
         BookCategory::factory()->create([
            'workspace_id' => $this->workspace->id,
            'name' => 'マネジメント', ]
         );
         Belonging::factory()->create([
            'user_id' => $user->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => $role->id,
        ]);
     }

     /** @test */
     public function 書籍登録できること(): void
     {
         $response = $this->json('POST', '/api/' . $this->workspace->id . '/book', [
            'category' => 'マネジメント',
            'title' => 'すごい本',
            'description' => 'すごい本だよ',
            'image' => '',
            'url' => '',
        ], [
            'Authorization' => 'Bearer aaaaaaa',
        ]);
         $response->assertStatus(201);
     }
 }
