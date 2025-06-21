<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\KanbanList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->list->board->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, KanbanList $list): bool
    {
        return $user->id === $list->board->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Card $card): bool
    {
        return $user->id === $card->user_id || $user->id === $card->list->board->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Card $card): bool
    {
        return $user->id === $card->user_id || $user->id === $card->list->board->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Card $card): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Card $card): bool
    {
        return false;
    }

    /**
     * Determine whether the user can move the card.
     */
    public function move(User $user, Card $card): bool
    {
        return $user->id === $card->list->board->user_id;
    }
}
