<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Year;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    private $year;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect(Gate::allows('is-council') ? '/admin' : '/dashboard');
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
//        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
//        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

//        RateLimiter::for('two-factor', function (Request $request) {
//            return Limit::perMinute(5)->by($request->session()->get('login.id'));
//        });


        $this->year = Schema::hasTable('years') ? Year::where('is_current', '1')->first() : date('Y');
        Fortify::loginView(function () {
            return view('auth.login', ['year' => $this->year]);
        });
        Fortify::registerView(function () {
            return view('auth.register', ['year' => $this->year]);
        });
        Fortify::verifyEmailView(function () {
            return view('auth.verify', ['year' => $this->year]);
        });
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.passwords.email', ['year' => $this->year]);
        });
        Fortify::resetPasswordView(function ($request) {
            return view('auth.passwords.reset', ['request' => $request, 'year' => $this->year]);
        });
    }
}
