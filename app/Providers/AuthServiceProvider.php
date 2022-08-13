<?php

namespace App\Providers;

use App\Enums\Chargetypename;
use App\Enums\Usertype;
use App\Models\ThisyearCharge;
use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('has-paid', function (User $user) {
            $paid = 1;
            $scholar = 0;
            if (isset($user->camper) && isset($user->camper->family_id)) {
                $paid = ThisyearCharge::where('family_id', $user->camper->family_id)
                    ->where(function ($query) {
                        $query->where('chargetype_id', Chargetypename::Deposit)->orWhere('amount', '<', 0);
                    })->get()->sum('amount');
                $scholar = $user->camper->family->is_scholar;
            }
            return $paid <= 0 || $scholar;
        });

        Gate::define('register', function (User $user, Year $year) {
            return $year->can_register == 1;
        });

        Gate::define('accept-paypal', function (User $user, Year $year) {
            return $year->can_accept_paypal == 1;
        });

        Gate::define('select-workshops', function (User $user, Year $year) {
            return $year->can_workshop_select == 1;
        });

        Gate::define('select-room', function (User $user, Year $year) {
            return $year->can_room_select == 1;
        });

        Gate::define('is-council', function (User $user) {
            return $user->usertype > Usertype::Camper;
        });

        Gate::define('is-super', function (User $user) {
            return $user->usertype > Usertype::Pc;
        });

        Gate::define('readonly', function (User $user) {
            return request()->route()->hasParameter('id') && $user->usertype == Usertype::Pc;
        });
    }
}
