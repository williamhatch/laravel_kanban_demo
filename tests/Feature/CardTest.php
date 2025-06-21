<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Card;
use App\Models\KanbanList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $board;
    protected $list;
    protected $card;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['user_id' => $this->user->id]);
        $this->list = KanbanList::factory()->create([
            'board_id' => $this->board->id,
            'name' => 'Test List'
        ]);
        $this->card = Card::factory()->create([
            'list_id' => $this->list->id,
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
            'priority' => 'medium'
        ]);
    }

    public function test_user_can_update_their_own_card()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$this->card->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'priority' => 'high',
                'due_date' => '2024-12-31'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'priority' => 'high'
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $this->card->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'priority' => 'high'
        ]);
    }

    public function test_user_can_update_card_with_minimal_data()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$this->card->id}", [
                'title' => 'Minimal Title'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Minimal Title'
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $this->card->id,
            'title' => 'Minimal Title'
        ]);
    }

    public function test_user_cannot_update_card_without_title()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$this->card->id}", [
                'description' => 'No title provided'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_user_cannot_update_card_with_invalid_priority()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$this->card->id}", [
                'title' => 'Valid Title',
                'priority' => 'invalid_priority'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_user_cannot_update_card_with_invalid_due_date()
    {
        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$this->card->id}", [
                'title' => 'Valid Title',
                'due_date' => 'invalid-date'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_user_cannot_update_another_users_card()
    {
        $otherUser = User::factory()->create();
        $otherBoard = Board::factory()->create(['user_id' => $otherUser->id]);
        $otherList = KanbanList::factory()->create(['board_id' => $otherBoard->id]);
        $otherCard = Card::factory()->create([
            'list_id' => $otherList->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$otherCard->id}", [
                'title' => 'Unauthorized Update'
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_update_card_in_their_board_even_if_not_owner()
    {
        // Create a card in user's board but owned by another user
        $otherUser = User::factory()->create();
        $cardInUserBoard = Card::factory()->create([
            'list_id' => $this->list->id,
            'user_id' => $otherUser->id,
            'title' => 'Card in user board'
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/cards/{$cardInUserBoard->id}", [
                'title' => 'Updated by board owner'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated by board owner'
            ]);
    }

    public function test_unauthenticated_user_cannot_update_card()
    {
        $response = $this->putJson("/cards/{$this->card->id}", [
            'title' => 'Unauthenticated Update'
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_view_card_details()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/cards/{$this->card->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $this->card->id,
                'title' => 'Original Title',
                'description' => 'Original Description',
                'priority' => 'medium'
            ]);
    }

    public function test_user_can_delete_their_own_card()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson("/cards/{$this->card->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cards', ['id' => $this->card->id]);
    }

    public function test_user_can_move_card_to_different_list()
    {
        $newList = KanbanList::factory()->create([
            'board_id' => $this->board->id,
            'name' => 'New List'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/cards/{$this->card->id}/move", [
                'list_id' => $newList->id,
                'position' => 1
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cards', [
            'id' => $this->card->id,
            'list_id' => $newList->id,
            'position' => 1
        ]);
    }

    /**
     * Test the relationship that a card belongs to a list.
     *
     * @return void
     */
    public function test_card_belongs_to_list()
    {
        // Assert that the 'list' relationship is an instance of KanbanList
        $this->assertInstanceOf(KanbanList::class, $this->card->list);

        // Assert that the foreign key is correct
        $this->assertEquals($this->list->id, $this->card->list->id);
    }

    /**
     * Test the relationship that a card belongs to a user.
     *
     * @return void
     */
    public function test_card_belongs_to_user()
    {
        // Assert that the 'user' relationship is an instance of User
        $this->assertInstanceOf(User::class, $this->card->user);
        
        // Assert that the foreign key is correct
        $this->assertEquals($this->user->id, $this->card->user->id);
    }

    /**
     * Test the relationship that a card has one board through its list.
     *
     * @return void
     */
    public function test_card_has_one_board_through_list()
    {
        // Assert that the 'board' relationship is an instance of Board
        $this->assertInstanceOf(Board::class, $this->card->board);

        // Assert that the board's id is correct
        $this->assertEquals($this->board->id, $this->card->board->id);
    }
} 