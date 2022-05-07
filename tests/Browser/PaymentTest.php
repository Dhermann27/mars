<?php

namespace Tests\Browser;

use app\Models\Camper;
use app\Models\Charge;
use app\Models\Chargetype;
use App\Enums\Chargetypename;
use App\Enums\Usertype;
use App\Jobs\GenerateCharges;
use app\Models\User;
use app\Models\Year;
use app\Models\Yearattending;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;
use function array_push;
use function factory;
use function rand;

/**
 * @group Payment
 */
class PaymentTest extends DuskTestCase
{

    /**
     * @group Abraham
     * @throws Throwable
     */
    public function testAbraham()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->assertSee('Please fill out your camper information to continue');
        });
    }

    /**
     * @group Beto
     * @throws Throwable
     */
    public function testBeto()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Beto', 'email' => $user->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 200.0)
                ->assertValue('input#amount', 200.0);
//                ->waitFor('div.paypal-button-env-sandbox')
//                ->waitFor('iframe.component-frame');
//            $browser->driver->switchTo()->frame($browser->driver->findElement(WebDriverBy::className('component-frame'))->getAttribute('name'));
//            $browser->waitFor('[role="button"]')->click('[role="button"]');
//            $window = collect($browser->driver->getWindowHandles())->last();
//            $browser->driver->switchTo()->window($window);
//            $browser->waitFor('input#email')
//                ->type('input#email', env('PAYPAL_LOGIN'))
//                ->type('input#password', env('PAYPAL_PASSWORD'))
//                ->click('button.actionContinue')->waitFor('button#payment-submit-btn:not([disabled])')
//                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });
//        $this->assertDatabaseHas('charges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'chargetype_id' => Chargetypename::PayPalPayment, 'amount' => 200.0, 'timestamp' => date("Y-m-d")]);
    }

    /**
     * @group Charlie
     * @throws Throwable
     */
    public function testCharlieCheck()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $charge = Charge::factory()->make(['chargetype_id' => Chargetypename::CheckPayment,
            'camper_id' => $camper->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $charge) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $camper->id])
                ->waitFor('form#muusapayment')
                ->select('chargetype_id', $charge->chargetype_id)->type('amount', $charge->amount)
                ->type('timestamp', $charge->timestamp)->type('memo', $charge->memo)
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')->logout();
        });

        $this->assertDatabaseHas('charges', ['year_id' => self::$year->id, 'chargetype_id' => Chargetypename::CheckPayment]);

        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }

        $this->browse(function (Browser $browser) use ($cuser, $camper, $charge) {
            $browser->loginAs($cuser->id)->visitRoute('payment.index')
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertSee(Chargetype::findOrFail(Chargetypename::CheckPayment)->name)->assertSee($charge->amount)
                ->assertSeeIn('#amountNow', '0.00')->assertSee('Registration')
                ->assertDontSee('Register Now');
        });

    }

    /**
     * @group Charlie
     * @throws Throwable
     */
    public function testCharlieRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CheckPayment,
            'camper_id' => $camper->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $charge) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $camper->id])
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertSee(Chargetype::findOrFail(Chargetypename::CheckPayment)->name)
                ->assertSee($charge->amount)->assertSeeIn('#amountArrival', '0.00')
                ->assertMissing('button[type="submit"]');
        });


    }

    /**
     * @group Deb
     * @throws Throwable
     */
    public function testDeb()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Deb', 'email' => $user->email]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->assertSee('Please choose which campers are attending this year');
        });
    }

    /**
     * @group Evra
     * @throws Throwable
     */
    public function testEvraDonation()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Evra', 'email' => $user->email]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $donation = rand(0, 9999) / 100;
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 400.0)
                ->assertValue('input#amount', 400.0)
                ->type('input#donation', $donation)->click('h1')
                ->assertValue('input#amount', 400.0 + $donation);
//                ->waitFor('div.paypal-button-env-sandbox')
//                ->waitFor('iframe.component-frame');
//            $browser->driver->switchTo()->frame($browser->driver->findElement(WebDriverBy::className('component-frame'))->getAttribute('name'));
//            $browser->waitFor('[role="button"]')->click('[role="button"]');
//            $window = collect($browser->driver->getWindowHandles())->last();
//            $browser->driver->switchTo()->window($window);
//            $browser->waitFor('input#email')
//                ->type('input#email', env('PAYPAL_LOGIN'))
//                ->type('input#password', env('PAYPAL_PASSWORD'))
//                ->click('button.actionContinue')->waitFor('button#payment-submit-btn:not([disabled])')
//                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });
//        $this->assertDatabaseHas('charges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'chargetype_id' => Chargetypename::PayPalPayment, 'amount' => 200.0, 'timestamp' => date("Y-m-d")]);

    }

    /**
     * @group Franklin
     * @throws Throwable
     */
    public function testFranklinMultipleYears()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
        Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CreditCardPayment,
            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);
        $newcharge = Charge::factory()->create(['chargetype_id' => Chargetypename::CheckPayment,
            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$lastyear->id]);

        foreach (self::$years as $year) {
            $charges = array();
            foreach ($campers as $camper) {
                Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => $year->id]);
                array_push($charges, Charge::factory()->create([
                    'chargetype_id' => Chargetypename::CreditCardPayment, 'camper_id' => $camper->id,
                    'amount' => rand(-20000, -100000) / 100, 'year_id' => $year->id]));
            }
            $year->charges = $charges;
            GenerateCharges::dispatchNow($year->id);
        }

        $this->browse(function (Browser $browser) use ($user, $campers, $charge, $newcharge) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $campers[0]->id])
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->clickLink(self::$year->year)->pause(250)
                ->assertSeeIn('form#muusapayment div.tab-content div.active', $charge->amount);
            foreach (self::$years as $year) {
                $browser->clickLink($year->year)->pause(250)->assertSelected('year_id', $year->id);
                foreach ($year->charges as $charge) {
                    $browser->assertSeeIn('form#muusapayment div.tab-content div.active', $charge->amount);
                }
            }
            $browser->clickLink(self::$lastyear->year)->pause(250)
                ->select('chargetype_id', $newcharge->chargetype_id)
                ->type('amount', $newcharge->amount)->type('timestamp', $newcharge->timestamp)
                ->type('memo', $newcharge->memo)->click('button[type="submit"]')
                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('charges', ['year_id' => $newcharge->year_id,
            'chargetype_id' => $newcharge->chargetype_id, 'amount' => $newcharge->amount,
            'timestamp' => $newcharge->timestamp, 'memo' => $newcharge->memo]);

    }

    /**
     * @group Franklin
     * @throws Throwable
     */
    public function testFranklinRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CreditCardPayment,
            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);

        foreach (self::$years as $year) {
            $charges = array();
            foreach ($campers as $camper) {
                Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => $year->id]);
                array_push($charges, Charge::factory()->create([
                    'chargetype_id' => Chargetypename::CreditCardPayment, 'camper_id' => $camper->id,
                    'amount' => rand(-20000, -100000) / 100, 'year_id' => $year->id]));
            }
            $year->charges = $charges;
            GenerateCharges::dispatchNow($year->id);
        }

        $this->browse(function (Browser $browser) use ($user, $campers, $charge) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $campers[0]->id])
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->clickLink(self::$year->year)->pause(250)
                ->assertSeeIn('form#muusapayment div.tab-content div.active', $charge->amount);
            foreach (self::$years as $year) {
                $browser->clickLink($year->year)->pause(250);
                foreach ($year->charges as $charge) {
                    $browser->assertSeeIn('form#muusapayment div.tab-content div.active', $charge->amount);
                }
            }
            $browser->assertDontSee('Save Changes');
        });
    }

    /**
     * @group Geoff
     * @throws Throwable
     */
    public function testGeoffNoPaypal()
    {
        $year = Year::where('is_current', '1')->first();
        $year->is_accept_paypal = 0;
        $year->save();

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Geoff', 'email' => $user->email]);
        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertDontSee('Amount Due Now')
                ->assertSee('Please bring payment to the first day of camp');
        });

        $year->is_accept_paypal = 1;
        $year->save();
    }

    /**
     * @group Ingrid
     * @throws Throwable
     */
    public function testIngridDelete()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);
        $charges = factory(Charge::class, rand(2, 10))->create(['camper_id' => $camper->id,
            'chargetype_id' => Chargetypename::PayPalPayment, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user, $camper, $charges) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $camper->id])
                ->waitFor('form#muusapayment div.tab-content div.active');
            foreach ($charges as $charge) {
                $browser->check('delete-' . $charge->id);
            }
            $browser->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')->assertSee('Deposit');
        });

        foreach ($charges as $charge) {
            $this->assertDatabaseMissing('charges', ['id' => $charge->id]);
        }

    }

    /**
     * @group Ingrid
     * @throws Throwable
     */
    public function testIngridRO()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);
        factory(Charge::class, rand(2, 10))->create(['camper_id' => $camper->id,
            'chargetype_id' => Chargetypename::PayPalPayment, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->loginAs($user->id)->visitRoute('payment.index', ['id' => $camper->id])
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertMissing('button[type="submit"]')->assertDontSee('Delete');
//                ->assertMissing('form#muusapayment input[type="checkbox"]');
        });

    }

    /**
     * @group Trent
     * @throws Throwable
     */
    public function testTrentAddThree()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['firstname' => 'Trent', 'email' => $user->email]);
        Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $campers = factory(Camper::class, 3)->create(['family_id' => $head->family_id]);
        foreach ($campers as $camper) {
            Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        }
        GenerateCharges::dispatchNow(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('payment.index')
                ->waitFor('form#muusapayment div.tab-content div.active')
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 400.0)
                ->assertValue('input#amount', 400.0)
                ->check('addthree');
//                ->waitFor('div.paypal-button-env-sandbox')
//                ->waitFor('iframe.component-frame');
//            $browser->driver->switchTo()->frame($browser->driver->findElement(WebDriverBy::className('component-frame'))->getAttribute('name'));
//            $browser->waitFor('[role="button"]')->click('[role="button"]');
//            $window = collect($browser->driver->getWindowHandles())->last();
//            $browser->driver->switchTo()->window($window);
//            $browser->waitFor('input#email')
//                ->type('input#email', env('PAYPAL_LOGIN'))
//                ->type('input#password', env('PAYPAL_PASSWORD'))
//                ->click('button.actionContinue')->waitFor('button#payment-submit-btn:not([disabled])')
//                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });
//        $this->assertDatabaseHas('charges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'chargetype_id' => Chargetypename::PayPalPayment, 'amount' => 200.0, 'timestamp' => date("Y-m-d")]);
    }
}
