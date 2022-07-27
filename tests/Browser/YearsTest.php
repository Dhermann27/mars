<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


/**
 * @group Home
 * @group Year
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
                ->assertDontSee('Registration')
                ->assertSee('be able to register soon');

            $browser->visit('/dashboard')->assertPathIs('/home');
        });
    }
}
