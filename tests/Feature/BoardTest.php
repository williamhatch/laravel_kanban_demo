<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for all tests in this class
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_create_a_board(): void
    {
        $boardData = ['name' => 'New Project', 'description' => 'A board for my new project'];

        $response = $this->actingAs($this->user)->post(route('boards.store'), $boardData);

        // Assert the board was created in the database
        $this->assertDatabaseHas('boards', [
            'name' => 'New Project',
            'user_id' => $this->user->id,
        ]);

        // Assert the user is redirected to the new board's page
        $board = Board::first();
        $response->assertRedirect(route('boards.show', $board));
    }

    public function test_authenticated_user_can_view_their_own_board(): void
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('boards.show', $board));

        $response->assertStatus(200);
        $response->assertSee($board->name);
    }

    public function test_authenticated_user_cannot_view_another_users_board(): void
    {
        // Create a board owned by another user
        $otherUser = User::factory()->create();
        $otherUsersBoard = Board::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('boards.show', $otherUsersBoard));

        // Policy should deny access, resulting in a 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_their_own_board(): void
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);
        $updatedData = ['name' => 'Updated Board Name', 'description' => 'Updated description.'];

        $response = $this->actingAs($this->user)->put(route('boards.update', $board), $updatedData);

        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'name' => 'Updated Board Name',
        ]);
        $response->assertRedirect(route('boards.show', $board));
    }

    public function test_authenticated_user_cannot_update_another_users_board(): void
    {
        $otherUser = User::factory()->create();
        $otherUsersBoard = Board::factory()->create(['user_id' => $otherUser->id]);
        $updatedData = ['name' => 'Attempted Update'];

        $response = $this->actingAs($this->user)->put(route('boards.update', $otherUsersBoard), $updatedData);

        $response->assertStatus(403);
    }
    
    public function test_authenticated_user_can_delete_their_own_board(): void
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('boards.destroy', $board));

        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
        $response->assertRedirect(route('boards.index'));
    }

    public function test_board_name_is_required_on_create(): void
    {
        $response = $this->actingAs($this->user)->post(route('boards.store'), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }
}
