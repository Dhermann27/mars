<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\Rate;
use App\Models\Room;
use App\Models\User;
use App\Models\Year;
use App\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;
use function rand;


/**
 * @group Register
 * @group Payment
 */
class PaymentTest extends DuskTestCase
{

    private const ROUTE = 'payment.index';
    private const WAIT = 250;
    private const ACTIVETAB = 'form#muusapayment div.tab-content div.active';

    public function testNewVisitor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route(self::ROUTE))->pause(self::WAIT)->assertSee('You need to be logged in');
        });
    }

    public function testAccountButNoCamper()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->assertSee('No charges present');
        });
    }

    public function testNewCamper()
    {
        $user = User::factory()->create();

        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 200.0)
                ->assertValue('input#payment', 200.0);
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
//    public function testCharlieCheck()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//
//        $charge = Charge::factory()->make(['chargetype_id' => Chargetypename::CheckPayment,
//            'camper_id' => $camper->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $charge) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor('form#muusapayment')
//                ->select('chargetype_id', $charge->chargetype_id)->type('amount', $charge->amount)
//                ->type('timestamp', $charge->timestamp)->type('memo', $charge->memo)
//                ->click('button[type="submit"]')->waitFor('div.alert')
//                ->assertVisible('div.alert-success')->logout();
//        });
//
//        $this->assertDatabaseHas('charges', ['year_id' => self::$year->id, 'chargetype_id' => Chargetypename::CheckPayment]);
//
//        foreach (static::$browsers as $browser) {
//            $browser->driver->manage()->deleteAllCookies();
//        }
//
//        $this->browse(function (Browser $browser) use ($cuser, $camper, $charge) {
//            $browser->loginAs($cuser->id)->visitRoute(self::ROUTE)
//                ->waitFor(self::ACTIVETAB)
//                ->assertSee(Chargetype::findOrFail(Chargetypename::CheckPayment)->name)->assertSee($charge->amount)
//                ->assertSeeIn('#amountNow', '0.00')->assertSee('Registration')
//                ->assertDontSee('Register Now');
//        });
//
//    }
//
//    /**
//     * @group Charlie
//     * @throws Throwable
//     */
//    public function testCharlieRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//
//        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CheckPayment,
//            'camper_id' => $camper->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $charge) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->assertSee(Chargetype::findOrFail(Chargetypename::CheckPayment)->name)
//                ->assertSee($charge->amount)->assertSeeIn('#amountArrival', '0.00')
//                ->assertMissing('button[type="submit"]');
//        });
//
//
//    }


    /**
     * @group Evra
     * @throws Throwable
     */
    public function testReturningCoupleDonation()
    {
        $user = User::factory()->create();

        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $bigdonation = rand(1000, 9999);
        $lildonation = rand(0, 1000);
        $this->browse(function (Browser $browser) use ($user, $bigdonation, $lildonation) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 400.0)
                ->assertValue('input#payment', 400.0)
                ->type('input#donation', $bigdonation)->click('input#payment')->pause(self::WAIT)
//                ->assertValue('input#payment', 400.0 + $donation) TODO: Why???
                ->script('window.scrollTo(9999,9999)');
            $browser->press('Donate')->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.muusa-invalid-feedback');
            $browser->clear('donation')->type('input#donation', $lildonation)
                ->click('input#payment')->pause(self::WAIT)
//                ->assertValue('input#payment', 400.0 + $donation) TODO: Why???
                ->script('window.scrollTo(9999,9999)');
            $browser->press('Donate')->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });
        $this->assertDatabaseHas('charges', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Donation, 'amount' => $lildonation, 'timestamp' => date("Y-m-d")]);

    }

    /**
     * @group Franklin
     * @throws Throwable
     */
//    public function testFranklinMultipleYears()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
//        Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
//        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
//        Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//
//        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CreditCardPayment,
//            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);
//        $newcharge = Charge::factory()->create(['chargetype_id' => Chargetypename::CheckPayment,
//            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$lastyear->id]);
//
//        foreach (self::$years as $year) {
//            $charges = array();
//            foreach ($campers as $camper) {
//                Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => $year->id]);
//                array_push($charges, Charge::factory()->create([
//                    'chargetype_id' => Chargetypename::CreditCardPayment, 'camper_id' => $camper->id,
//                    'amount' => rand(-20000, -100000) / 100, 'year_id' => $year->id]));
//            }
//            $year->charges = $charges;
//            GenerateCharges::dispatchSync($year->id);
//        }
//
//        $this->browse(function (Browser $browser) use ($user, $campers, $charge, $newcharge) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $campers[0]->id])
//                ->waitFor(self::ACTIVETAB)
//                ->clickLink(self::$year->year)->pause(self::WAIT)
//                ->assertSeeIn(self::ACTIVETAB, $charge->amount);
//            foreach (self::$years as $year) {
//                $browser->clickLink($year->year)->pause(self::WAIT)->assertSelected('year_id', $year->id);
//                foreach ($year->charges as $charge) {
//                    $browser->assertSeeIn(self::ACTIVETAB, $charge->amount);
//                }
//            }
//            $browser->clickLink(self::$lastyear->year)->pause(self::WAIT)
//                ->select('chargetype_id', $newcharge->chargetype_id)
//                ->type('amount', $newcharge->amount)->type('timestamp', $newcharge->timestamp)
//                ->type('memo', $newcharge->memo)->click('button[type="submit"]')
//                ->waitFor('div.alert')->assertVisible('div.alert-success');
//        });
//
//        $this->assertDatabaseHas('charges', ['year_id' => $newcharge->year_id,
//            'chargetype_id' => $newcharge->chargetype_id, 'amount' => $newcharge->amount,
//            'timestamp' => $newcharge->timestamp, 'memo' => $newcharge->memo]);
//
//    }
//
//    /**
//     * @group Franklin
//     * @throws Throwable
//     */
//    public function testFranklinRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
//        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
//        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
//        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//
//        $charge = Charge::factory()->create(['chargetype_id' => Chargetypename::CreditCardPayment,
//            'camper_id' => $campers[0]->id, 'amount' => rand(-20000, -100000) / 100, 'year_id' => self::$year->id]);
//
//        foreach (self::$years as $year) {
//            $charges = array();
//            foreach ($campers as $camper) {
//                Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => $year->id]);
//                array_push($charges, Charge::factory()->create([
//                    'chargetype_id' => Chargetypename::CreditCardPayment, 'camper_id' => $camper->id,
//                    'amount' => rand(-20000, -100000) / 100, 'year_id' => $year->id]));
//            }
//            $year->charges = $charges;
//            GenerateCharges::dispatchSync($year->id);
//        }
//
//        $this->browse(function (Browser $browser) use ($user, $campers, $charge) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $campers[0]->id])
//                ->waitFor(self::ACTIVETAB)
//                ->clickLink(self::$year->year)->pause(self::WAIT)
//                ->assertSeeIn(self::ACTIVETAB, $charge->amount);
//            foreach (self::$years as $year) {
//                $browser->clickLink($year->year)->pause(self::WAIT);
//                foreach ($year->charges as $charge) {
//                    $browser->assertSeeIn(self::ACTIVETAB, $charge->amount);
//                }
//            }
//            $browser->assertDontSee('Save Changes');
//        });
//    }

    public function testReturningYANoPaypal()
    {
        $year = Year::where('is_current', '1')->first();
        $year->is_accept_paypal = 0;
        $year->save();

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertDontSee('Pay via PayPal')
                ->assertSee('Please bring payment to checkin');
        });

        $year->is_accept_paypal = 1;
        $year->save();
    }

    /**
     * @group Ingrid
     * @throws Throwable
     */
//    public function testIngridDelete()
//    {
//        $birth = Carbon::now();
//        $birth->year = self::$year->year - 20;
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
//            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//        $charges = Charge::factory()->count(rand(2, 10))->create(['camper_id' => $camper->id,
//            'chargetype_id' => Chargetypename::PayPalPayment, 'year_id' => self::$year->id]);
//        $this->browse(function (Browser $browser) use ($user, $camper, $charges) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB);
//            foreach ($charges as $charge) {
//                $browser->check('delete-' . $charge->id);
//            }
//            $browser->click('button[type="submit"]')->waitFor('div.alert')
//                ->assertVisible('div.alert-success')->assertSee('Deposit');
//        });
//
//        foreach ($charges as $charge) {
//            $this->assertDatabaseMissing('charges', ['id' => $charge->id]);
//        }
//
//    }
//
//    /**
//     * @group Ingrid
//     * @throws Throwable
//     */
//    public function testIngridRO()
//    {
//        $birth = Carbon::now();
//        $birth->year = self::$year->year - 20;
//
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
//            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//        Charge::factory()->count(rand(2, 10))->create(['camper_id' => $camper->id,
//            'chargetype_id' => Chargetypename::PayPalPayment, 'year_id' => self::$year->id]);
//        $this->browse(function (Browser $browser) use ($user, $camper) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->assertMissing('button[type="submit"]')->assertDontSee('Delete');
////                ->assertMissing('form#muusapayment input[type="checkbox"]');
//        });
//
//    }

    public function testReturningFamilyNoDeposit()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $campers = Camper::factory()->count(3)->create(['family_id' => $head->family_id,
            'roommate' => __FUNCTION__]);
        foreach ($campers as $camper) {
            Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        }
        GenerateCharges::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $head) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 400.0)
                ->assertValue('input#payment', 400.0)
                ->assertDontSee('Amount Due Upon Arrival');

            $charge = Charge::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id,
                'chargetype_id' => Chargetypename::CheckPayment, 'amount' => -600]);
            GenerateCharges::dispatchSync(self::$year->id);

            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertSee('Deposit for ' . self::$year->year)
                ->assertSeeIn('span#amountNow', 0.00)
                ->assertValue('input#payment', 0.00)
                ->assertDontSee('Amount Due upon Arrival');
        });
    }

    public function testReturningSeniorWithRoom()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $room = Room::factory()->create();
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => $room->id]);
        $rate = Rate::factory()->create(['building_id' => $room->building_id, 'program_id' => $ya->program_id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $rate, $camper) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertDontSee('Deposit for ' . self::$year->year)
                ->assertSee('MUUSA Fees')
//                ->assertSeeIn('span#amountNow', 200.00) TODO: Probably bad
                ->assertSeeIn('span#amountArrival', $rate->rate * 6)
                ->assertValue('input#payment', $rate->rate * 6);

            $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
                'chargetype_id' => Chargetypename::CheckPayment, 'amount' => -300]);
            GenerateCharges::dispatchSync(self::$year->id);

            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertDontSee('Deposit for ' . self::$year->year)
                ->assertSee('MUUSA Fees')
                ->assertSeeIn('span#amountNow', 0.00)
                ->assertSeeIn('span#amountArrival', $rate->rate * 6 + $charge->amount)
                ->assertValue('input#payment', $rate->rate * 6 + $charge->amount);
        });
    }
}
