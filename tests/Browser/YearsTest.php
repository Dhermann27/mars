<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


/**
 * @group Home
 * @group Year
 * @group Years
 */
class YearsTest extends DuskTestCase
{
    public function testRegisterOff()
    {
        self::$year->can_register = 0;
        self::$year->is_brochure = 0;
        self::$year->save();


        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit('/')
                ->assertSee('Midwest Unitarian Universalist Summer Assembly')
                ->assertDontSee('REGISTRATION')
                ->assertSee('be able to register soon');

            $browser->visit('/dashboard')->assertPathIs('/home');
        });

        self::$year->can_register = 1;
        self::$year->save();

    }


    public function testBrochureOff()
    {

        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit('/')
                ->assertSee('Midwest Unitarian Universalist Summer Assembly')
                ->assertSee('REGISTRATION')
                ->assertSee('you can register right now');

            $browser->visit('/dashboard')->assertPathIs('/dashboard');
        });

        self::$year->can_register = 1;
        self::$year->save();

    }
}
