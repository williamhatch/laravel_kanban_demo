<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Card;
use App\Models\KanbanList;
use App\Policies\BoardPolicy;
use App\Policies\CardPolicy;
use App\Policies\KanbanListPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
        Card::class => CardPolicy::class,
        KanbanList::class => KanbanListPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
} 