<?php

// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    
    
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update', function ($user, Team $team) {
            return $user->id === $team->owner_id;
        });
        Gate::define('delete-team', [TeamPolicy::class, 'delete']);

    }



}

