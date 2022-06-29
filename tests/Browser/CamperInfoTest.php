<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Enums\Programname;
use App\Models\Camper;
use App\Models\Family;
use App\Models\User;
use App\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\CamperInfo;
use Tests\DuskTestCase;


/**
 * @group Register
 * @group CamperInfo
 */
class CamperInfoTest extends DuskTestCase
{
    private const WAIT = 400;
    private const ROUTE = 'camperinfo.index';
    private const ACTIVETAB = 'form#camperinfo div.tab-content div.active';

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
            $browser->loginAs($user)->visit(route(self::ROUTE))->assertSee('update your address');
        });
        $this->assertDatabaseHas('campers', ['email' => $user->email]);
    }

    public function testNewCamperChangeLogin()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['roommate' => __FUNCTION__]);
        $cya = Yearattending::factory()->make();
        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                });
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

//    /**
//     * @group Charlie
//     * @throws Throwable
//     */
//    public function testCharlie()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'firstname' => 'Charlie']);
//        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB);
//            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
//            $browser->press('Save Changes')->waitFor('div.alert')
//                ->assertVisible('div.alert-success');
//        });
//
//        $this->assertDatabaseHas('users', ['email' => $changes->email]);
//        $this->adh($changes);
//        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
//    }
//
//    /**
//     * @group Charlie
//     * @throws Throwable
//     */
//    public function testCharlieRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB);
//            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
//                $browser->viewCamper($camper, $ya);
//            })->assertMissing('button[type="submit"]');
//        });
//
//
//    }
//
    public function testReturningCoupleDistinctEmails()
    {
        $users = User::factory()->count(2)->create();

        // Need $campers[0] to be in tab 0
        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $users[0]->email, 'roommate' => __FUNCTION__, 'birthdate' => self::$year->year - 35 . '-01-01']);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => self::$year->year - 30 . '-01-01', 'email' => $users[1]->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->count(2)->make(['roommate' => __FUNCTION__]);
        $cyas[0] = Yearattending::factory()->make(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $cyas[1] = Yearattending::factory()->make(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($users, $campers, $yas, $changes, $cyas) {
            $otheremail = $changes[1]->email;
            $changes[1]->email = $changes[0]->email;

            $browser->loginAs($users[0]->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $yas, $changes, $cyas) {
                        $browser->changeCamper([$campers[$i], $yas[$i]], [$changes[$i], $cyas[$i]]);
                    });
            }
            $this->submitError($browser, self::WAIT);
            $changes[0]->email = $otheremail;
            $browser->type('email[]', $otheremail);
            $this->submitSuccess($browser, self::WAIT);

        });

        $this->assertDatabaseMissing('users', ['email' => $users[0]->email]);
        $this->assertDatabaseMissing('users', ['email' => $users[1]->email]);
        foreach ($changes as $change) $this->adh($change);
        $this->assertDatabaseHas('users', ['email' => $changes[0]->email]);
        $this->assertDatabaseHas('users', ['email' => $changes[1]->email]);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $ya->camper_id,
                'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'amount' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }
//
//    /**
//     * @group Franklin
//     * @throws Throwable
//     */
//    public function testFranklinDistinct()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $cuser = User::factory()->create();
//        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
//        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
//        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
//        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
//
//        $changes = Camper::factory()->count(2)->make(['family_id' => $campers[0]->family_id]);
//        $changes[0]->firstname = "Franklin";
//        $changes[1]->email = $changes[0]->email;
//        $cyas = Yearattending::factory()->count(2)->make(['year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $campers[0]->id])
//                ->waitFor(self::ACTIVETAB);
//            for ($i = 0; $i < count($campers); $i++) {
//                $browser->script('window.scrollTo(0,0)');
//                $browser->pause(self::WAIT)->clickLink($campers[$i]->firstname)->pause(self::WAIT);
//                $this->changeCamper($browser, $campers[$i], $yas[$i], $changes[$i], $cyas[$i]);
//            }
//            $browser->press('Save Changes')->waitFor('div.alert')
//                ->assertVisible('div.alert-danger')->assertPresent('span.muusa-invalid-feedback');
//            $changes[1]->email = 'franklin@email.org';
//            $browser->script('window.scrollTo(0,0)');
//            $browser->pause(self::WAIT)->clickLink($changes[1]->firstname)->pause(self::WAIT)
//                ->type('form#camperinfo div.tab-content div.active input[name="email[]"]', $changes[1]->email);
//            $browser->press('Save Changes')->waitFor('div.alert')
//                ->assertVisible('div.alert-success');
//        });
//
//        foreach ($changes as $camper) $this->adh($camper);
//        foreach ($cyas as $ya) {
//            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
//        }
//    }
//
//    /**
//     * @group Franklin
//     * @throws Throwable
//     */
//    public function testFranklinRO()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $cuser = User::factory()->create();
//        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
//        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
//        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
//        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $campers, $yas) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $campers[0]->id])
//                ->waitFor(self::ACTIVETAB);
//            for ($i = 0; $i < count($campers); $i++) {
//                $browser->script('window.scrollTo(0,0)');
//                $browser->pause(self::WAIT)->clickLink($campers[$i]->firstname)->pause(self::WAIT);
//                $browser->within(new CamperForm, function (Browser $browser) use ($i, $campers, $yas) {
//                    $browser->viewCamper($campers[$i], $yas[$i]);
//                });
//            }
//            $browser->assertMissing('button[type="submit"]');
//        });
//    }
//

    public function testReturningYAUniqueEmailUnder20()
    {

        $user = User::factory()->create();

        $camper = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getYABirthdate()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdult]);

        $snowflake = Camper::factory()->create(['roommate' => __FUNCTION__]);
        $changes = Camper::factory()->make(['roommate' => __FUNCTION__, 'birthdate' => $this->getYABirthdate()]);
        $oldemail = $changes->email;
        $changes->email = $snowflake->email;
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdult]);
        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya, $oldemail) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                });
            $this->submitError($browser, self::WAIT);

            $changes->email = $oldemail;
            $browser->type('email[]', $oldemail);
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->adh($changes);
        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $cya->camper_id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdultUnderAge, 'days' => $cya->days]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'amount' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    public function testReturningSeniorUniqueUser()
    {

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email,
            'birthdate' => $this->getChildBirthdate(), 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $snowflake = User::factory()->create();
        $changes = Camper::factory()->make(['birthdate' => $this->getChildBirthdate(), 'roommate' => __FUNCTION__]);
        $oldemail = $changes->email;
        $changes->email = $snowflake->email;
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya, $oldemail) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                });
            $this->submitError($browser, self::WAIT);

            $changes->email = $oldemail;
            $browser->type('email[]', $oldemail);
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->adh($changes);
        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $cya->camper_id, 'year_id' => self::$year->id,
            'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'amount' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }
//
//    /**
//     * @group Ingrid
//     * @throws Throwable
//     */
//    public function testIngridUniqueCamper()
//    {
//        $birth = Carbon::now();
//        $birth->year = self::$year->year - 20;
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
//            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $snowflake = Camper::factory()->create();
//        $changes = Camper::factory()->make(['firstname' => 'Ingrid', 'email' => $snowflake->email,
//            'family_id' => $camper->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB);
//            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
//            $browser->press('Save Changes')->waitFor('div.alert')
//                ->assertVisible('div.alert-danger')->assertPresent('span.muusa-invalid-feedback');
//        });
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
//
//        $cuser = User::factory()->create();
//        $family = Family::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'family_id' => $family->id, 'email' => $cuser->email, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB);
//            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
//                $browser->viewCamper($camper, $ya);
//            })->assertMissing('button[type="submit"]');
//        });
//
//    }
//
//
    public function testReturningFamilyOneKidCantComeClickTwice()
    {
        $user = User::factory()->create();

        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => $this->getChildBirthdate()]);
        $campers[3] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => $this->getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $yas[3] = Yearattending::factory()->create(['camper_id' => $campers[3]->id, 'year_id' => self::$lastyear->id]);

        $changes = Camper::factory()->count(4)->make(['roommate' => __FUNCTION__]);
        $cyas[0] = Yearattending::factory()->make(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $cyas[1] = Yearattending::factory()->make(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $cyas[2] = Yearattending::factory()->make(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $cyas[3] = Yearattending::factory()->make(['camper_id' => $campers[3]->id, 'year_id' => self::$lastyear->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $yas, $changes, $cyas) {
                        $browser->changeCamper([$campers[$i], $yas[$i]], [$changes[$i], $cyas[$i]]);
                    });
            }
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        foreach ($changes as $change) $this->adh($change);
        $this->assertDatabaseHas('users', ['email' => $changes[0]->email]);
        foreach (array_slice($cyas, 0, 3) as $ya) {
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $ya->camper_id,
                'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $cyas[3]->camper_id,
            'year_id' => self::$year->id]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'amount' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }
//
//    /**
//     * @group Lucy
//     * @throws Throwable
//     */
//    public function testLucyAdultNotComing()
//    {
//        $birth = Carbon::now();
//        $birth->year = self::$year->year - rand(1, 17);
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $adult = Camper::factory()->create(['email' => $user->email]);
//        $camper = Camper::factory()->create(['firstname' => 'Lucy', 'sponsor' => 'Ingrid Illia',
//            'family_id' => $adult->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $yas[0] = Yearattending::factory()->create(['camper_id' => $adult->id, 'year_id' => self::$year->id]);
//        $yas[1] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $changes = Camper::factory()->make(['firstname' => 'Lucy', 'family_id' => $camper->family_id,
//            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $adult, $camper, $yas, $changes, $cya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->clickLink($camper->firstname)->pause(self::WAIT);
//            $this->changeCamper($browser, $camper, $yas[1], $changes, $cya);
//            $browser->clickLink($adult->firstname)->pause(self::WAIT)
//                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', 0)
//                ->press('Save Changes')->waitFor('div.alert')
//                ->assertVisible('div.alert-success');
//        });
//
//        $this->adh($adult);
//        $this->adh($changes);
//        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $adult->id, 'year_id' => self::$year->id]);
//        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);;
//    }
//
//    /**
//     * @group Lucy
//     * @throws Throwable
//     */
//    public function testLucyRO()
//    {
//        $birth = Carbon::now();
//        $birth->year = self::$year->year - rand(1, 17);
//
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $adult = Camper::factory()->create(['email' => $user->email]);
//        $camper = Camper::factory()->create(['firstname' => 'Lucy', 'sponsor' => 'Ingrid Illia',
//            'family_id' => $adult->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $adult, $camper, $ya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->clickLink($camper->firstname)->pause(self::WAIT);
//            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
//                $browser->viewCamper($camper, $ya);
//            })->assertMissing('button[type="submit"]');
//            $browser->clickLink($adult->firstname)->pause(self::WAIT)
//                ->assertSelected('form#camperinfo div.tab-content div.active select[name="days[]"]', 0);
//        });
//
//    }

    private function adh($camper)
    {
        $this->assertDatabaseHas('campers', ['pronoun_id' => $camper->pronoun_id,
            'firstname' => $camper->firstname, 'lastname' => $camper->lastname, 'email' => $camper->email,
            'phonenbr' => str_replace('-', '', $camper->phonenbr), 'birthdate' => $camper->birthdate,
            'roommate' => $camper->roommate, 'sponsor' => $camper->sponsor, 'is_handicap' => $camper->is_handicap,
            'foodoption_id' => $camper->foodoption_id, 'church_id' => $camper->church_id]);
    }

}
