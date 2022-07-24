<?php

namespace Tests\Browser;

use AppModels\Camper;
use AppModels\Family;
use AppModels\User;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;
use function array_push;
use function count;
use function factory;
use function rand;

/**
 * @group RegisterNow
 */
class RegisterNowTest extends DuskTestCase
{
    /**
     * @group Abraham
     * @throws Throwable
     */
    public function testAbraham()
    {
        $user = User::factory()->make();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()->visit('/')->click('@register_now')->waitFor('div#modal-register')
                ->assertSee('Get Registered for ' . self::$year->year)->waitFor('input#email_create')
                ->type('input#email_create', $user->email)
                ->type('input#password_create', 'password')
                ->type('input#confirm_create', 'password')
                ->pause(self::WAIT)->click('button#begin_reg')->waitForLocation('/campers')->assertSee($user->email);
        });
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    /**
     * @group Deb
     * @throws Throwable
     */
    public function testDeb()
    {
        $user = User::factory()->make();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()->visit('/')->click('@register_now')->waitFor('div#modal-register')
                ->assertSee('Get Registered for ' . self::$year->year)->waitFor('input#email_create')
                ->type('input#email_create', $user->email)
                ->type('input#password_create', 'password')
                ->type('input#confirm_create', 'password');
            $count = rand(1, 6);
            for ($i = 0; $i < $count; $i++) {
                $browser->click('button[data-dir="up"]');
            }
            $browser->pause(self::WAIT)->click('button#begin_reg')->waitForLocation('/campers')
                ->assertSee($user->email);
            $this->assertCount($count+1, $browser->elements('form#camperinfo a.nav-link:not(.btn-secondary)'));
        });
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    /**
     * @group Beto
     * @throws Throwable
     */
    public function testBeto()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);

        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->logout()->visit('/')->click('@register_now')->waitFor('div#modal-register')
                ->assertSee('Get Registered for ' . self::$year->year)->waitFor('input#email_login')
                ->type('input#email_login', $user->email)
                ->type('input#password_login', 'password')
                ->waitFor('div#login-found')
                ->assertSee($camper->firstname . ' ' . $camper->lastname)
                ->pause(self::WAIT)->click('button#begin_reg')->waitForLocation('/campers')->assertSee($user->email);
        });
    }

    /**
     * @group Evra
     * @throws Throwable
     */
    public function testEvra()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Evra',  'email' => $user->email]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);

        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->logout()->visit('/')->click('@register_now')->waitFor('div#modal-register')
                ->assertSee('Get Registered for ' . self::$year->year)->waitFor('input#email_login')
                ->type('input#email_login', $user->email)
                ->type('input#password_login', 'password')
                ->waitFor('div#login-found')
                ->assertSee($campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertSee($campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->pause(self::WAIT)->click('button#begin_reg')->waitForLocation('/campers')->assertSee($user->email);
        });
    }

    /**
     * @group Trent
     * @throws Throwable
     */
    public function testTrentSome()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(3)->create(['family_id' => $head->family_id]);

        $this->browse(function (Browser $browser) use ($user, $head, $campers) {
            $browser->logout()->visit('/')->click('@register_now')->waitFor('div#modal-register')
                ->assertSee('Get Registered for ' . self::$year->year)->waitFor('input#email_login')
                ->type('input#email_login', $user->email)
                ->type('input#password_login', 'password')
                ->waitFor('div#login-found')
                ->assertSee($head->firstname . ' ' . $head->lastname);
            $yas = array();
            foreach ($campers as $camper) {
                $browser->assertSee($camper->firstname . ' ' . $camper->lastname);
                $camper->coming = rand(0, 1) * 6;
                array_push($yas, $camper->coming);
                $browser->script('$(\'option[value="' . $camper->id . '"]\').prop(\'selected\', ' . $camper->coming . ');');
            }
            $browser->pause(self::WAIT)->click('button#begin_reg')->waitForLocation('/campers');
//                ->assertSee($user->email);

            foreach ($campers as $camper) {
                $browser->clickLink($camper->firstname)->pause(self::WAIT)
                    ->assertInputValue('form#camperinfo div.tab-content div.active input[name="email[]"]', $camper->email)
                    ->assertSelected('form#camperinfo div.tab-content div.active select[name="days[]"]', $camper->coming);
            }
        });
    }
}
