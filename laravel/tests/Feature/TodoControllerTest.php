w<?php

    namespace Tests\Feature\Http\Controllers;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Tests\TestCase;
    use App\Models\Todo;
    use App\Models\User;

    class TodoControllerTest extends TestCase
    {

        private User $user;

        public function setUp(): void
        {
            parent::setUp();
            // ここに各テストの前に実行したいセットアップコードを追加できます
            $this->user = User::factory()->create();
            $this->actingAs($this->user, 'sanctum');
        }
        /**
         * A basic feature test example.
         */
        public function test_example(): void
        {
            $response = $this->get('/');

            $response->assertStatus(200);
        }

        public function test_show(): void
        {
            // テスト用のTodoを作成
            $todo = Todo::factory()->create(
                ['user_id' => $this->user->id]

            );

            $response = $this->get("/todos/{$todo->id}");

            $response->assertStatus(200);
            $response->assertJson($todo->toArray())->dump();
        }


        public function todo_delete(): void
        {
            $todo = Todo::factory()->create(
                ['user_id' => $this->user->id]
            );

            $response = $this->delete("/todos/{$todo->id}");

            //dbにデータが存在しないこと
            $response->assertDatabaseMissing('todos', ['id' => $todo->id]);
        }

        public function test_index(): void
        {
            // テスト用のTodoを作成
            $todos = Todo::factory()->count(3)->create(
                [
                    ['id' => "42"],
                    [['id' => "43"]]
                ]
            );

            $response = $this->get("/todos");

            $response->assertStatus(200);
            foreach ($todos as $todo) {
                $response->assertJsonFragment($todo->toArray());
            }
        }

        public function test_store(): void
        {
            $todoData = [
                'title' => 'Test Todo',
                'is_completed' => false,
            ];

            $response = $this->json("POST", "/todos", $todoData)
                ->assertStatus(201)
                ->assertJsonPath('data.title', 'Test Todo');

            $response->assertStatus(201);
            $this->assertDatabaseHas('todos', [
                'title' => 'Test Todo',
                'is_completed' => false,
                'user_id' => $this->user->id,
            ]);
        }

        public function test_update(): void {
            $todo = Todo::factory()->create(
                ['user_id' => $this->user->id]
            );

            $updateData = [
                'title' => 'Updated Title',
                'is_completed' => true,
            ];

            $response = $this->json("PUT", "/todos/{$todo->id}", $updateData)
                ->assertStatus(200)
                ->assertJsonPath('data.title', 'Updated Title')
                ->assertJsonPath('data.is_completed', true);

            $this->assertDatabaseHas('todos', [
                'id' => $todo->id,
                'title' => 'Updated Title',
                'is_completed' => true,
            ]);
        }

        
    }
