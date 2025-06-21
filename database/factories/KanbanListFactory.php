<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\KanbanList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KanbanList>
 */
class KanbanListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'position' => fake()->numberBetween(1, 10),
            'board_id' => Board::factory(),
        ];
    }

    /**
     * Indicate that the list is a "To Do" list.
     */
    public function todo(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'To Do',
            'position' => 1,
        ]);
    }

    /**
     * Indicate that the list is an "In Progress" list.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'In Progress',
            'position' => 2,
        ]);
    }

    /**
     * Indicate that the list is a "Done" list.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Done',
            'position' => 3,
        ]);
    }
} 