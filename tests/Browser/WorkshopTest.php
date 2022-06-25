<?php

namespace Tests\Browser;

use App\Enums\Programname;
use App\Enums\Timeslotname;
use App\Jobs\GenerateCharges;
use App\Jobs\UpdateWorkshops;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\Timeslot;
use App\Models\User;
use App\Models\Workshop;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Faker\Factory;
use Laravel\Dusk\Browser;
use ReflectionClass;
use Tests\DuskTestCase;


/**
 * @group Register
 * @group Workshop
 * @group Workshops
 */
class WorkshopTest extends DuskTestCase
{
    private const WAIT = 400;
    private const ROUTE = 'workshopchoice.index';
    private const ACTIVETAB = 'form#workshops div.tab-content div.active';
    private const RANDOELEMENT = 'a.btn-primary';

    public function testNewVisitor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('You need to be logged in');
        });
    }

    public function testAccountButNoCamper()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)->visit(route(self::ROUTE))
                ->assertSee('no campers registered');
        });
        $this->assertDatabaseHas('campers', ['email' => $user->email]);
    }

    public function testNewCamperUnpaid()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $workshop = Workshop::factory()->create(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $workshop) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->waitFor(self::ACTIVETAB)
                ->assertSee('until your deposit has been paid')
                ->assertSee($workshop->name)
                ->assertMissing('button[type=submit]');
        });
    }

//
//    /**
//     * @group Charlie
//     * @throws \Throwable
//     */
//    public function testCharlie()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);
//
//        $workshop = Workshop::factory()->create(['year_id' => self::$year->id]);
//        $yaw = YearattendingWorkshop::factory()->make(['yearattending_id' => $ya->id,
//            'workshop_id' => $workshop->id]);
//
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $workshop, $yaw) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->assertSee($workshop->name)
//                ->press('button#workshop-' . $camper->id . '-' . $workshop->id)
//                ->waitFor('form#workshops button.active')
//                ->assertSeeIn('form#workshops button.active', $workshop->name)
//                ->press('button[type="submit"]')->waitFor('div.alert')
//                ->assertVisible('div.alert-success');
//        });
//
//        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yaw->yearattending_id,
//            'workshop_id' => $yaw->workshop_id, 'is_enrolled' => 1]);
//        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'enrolled' => 1]);
//    }
//
//    /**
//     * @group Charlie
//     * @throws \Throwable
//     */
//    public function testCharlieRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);
//
//        $workshops = Workshop::factory()->count(2)->create(['year_id' => self::$year->id]);
//        $yaw = YearattendingWorkshop::factory()->create(['yearattending_id' => $ya->id,
//            'workshop_id' => $workshops[0]->id]);
//
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $workshops, $yaw) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->waitFor(self::ACTIVETAB)
//                ->assertSeeIn('form#workshops button.active', $workshops[0]->name)
//                ->assertDontSeeIn('form#workshops button.active', $workshops[1]->name)
//                ->press('button#workshop-' . $camper->id . '-' . $workshops[1]->id)
//                ->waitFor('form#workshops button.active')
//                ->assertDontSeeIn('form#workshops button.active', $workshops[1]->name)
//                ->assertMissing('button[type="submit"]');
//        });
//
//    }
//
    public function testReturningCoupleAll()
    {

        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);

        $ref = new ReflectionClass('App\Enums\Timeslotname');
        $slots = $ref->getConstants();
        $workshops = array();
        foreach ($slots as $slot) {
            $workshop = Workshop::factory()->create(['year_id' => self::$year->id, 'timeslot_id' => $slot,
                'capacity' => rand(3, 99)]);
            $workshops[] = $workshop;
            YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
                'workshop_id' => $workshop->id]);
        }

        $this->browse(function (Browser $browser) use ($user, $campers, $workshops) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            $this->pressTab($browser, $campers[0]->id);
            foreach ($workshops as $workshop) {
                $browser->assertSee($workshop->name);
                parent::assertHasClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshop->id, 'active');
            }

            $this->pressTab($browser, $campers[1]->id);
            $browser->assertMissing(self::ACTIVETAB . ' button.active');
            foreach ($workshops as $workshop) {
                $browser->assertSee($workshop->name)
                    ->press('button#workshop-' . $campers[1]->id . '-' . $workshop->id)
                    ->mouseover(self::RANDOELEMENT)->pause(self::WAIT);
            }
            $this->submitSuccess($browser);
        });

        foreach ($workshops as $workshop) {
            $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[1]->id,
                'workshop_id' => $workshop->id, 'is_enrolled' => 1]);
            $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'enrolled' => 2]);
        }

    }

    public function testReturningSeniorExcursion()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => Programname::Burt]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $workshops[0] = Workshop::factory()->create(['year_id' => self::$year->id,
            'timeslot_id' => Timeslotname::Morning]);
        $workshops[1] = Workshop::factory()->create(['year_id' => self::$year->id,
            'timeslot_id' => Timeslotname::Excursions]);

        $this->browse(function (Browser $browser) use ($user, $camper, $workshops) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->waitFor(self::ACTIVETAB)
                ->assertSee("automatically enrolled in Burt")
                ->assertDontSee("Morning")->assertDontSee($workshops[0]->name)
                ->press('button#workshop-' . $camper->id . '-' . $workshops[1]->id);
//                ->waitFor(self::ACTIVETAB . ' button.active')
            parent::assertHasClass($browser, '#workshop-' . $camper->id . '-' . $workshops[1]->id, 'active');
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $ya->id,
            'workshop_id' => $workshops[1]->id, 'is_enrolled' => 1]);
        $this->assertDatabaseHas('workshops', ['id' => $workshops[1]->id, 'enrolled' => 1]);

    }

    public function testReturningFamilyRemove()
    {

        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(2)->create(['family_id' => $head->family_id,
            'roommate' => __FUNCTION__]);
        $yah = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);

        $workshop = Workshop::factory()->create(['year_id' => self::$year->id, 'capacity' => rand(3, 99)]);
        $hw = YearattendingWorkshop::factory()->create(['yearattending_id' => $yah->id,
            'workshop_id' => $workshop->id]);
        $yaws[0] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshop->id]);
        $yaws[1] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[1]->id,
            'workshop_id' => $workshop->id]);
        UpdateWorkshops::dispatchSync(self::$year->id);
        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'enrolled' => 3]);

        $this->browse(function (Browser $browser) use ($user, $head, $campers, $workshop) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            $this->pressTab($browser, $head->id);
            parent::assertHasClass($browser, '#workshop-' . $head->id . '-' . $workshop->id, 'active');
            $browser->press('button#workshop-' . $head->id . '-' . $workshop->id);
            $this->pressTab($browser, $campers[0]->id);
            parent::assertHasClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshop->id, 'active');
            $this->pressTab($browser, $campers[1]->id);
            parent::assertHasClass($browser, '#workshop-' . $campers[1]->id . '-' . $workshop->id, 'active');
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseMissing('yearsattending__workshop', ['yearattending_id' => $hw->id,
            'workshop_id' => $workshop->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshop->id, 'is_enrolled' => 1]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[1]->id,
            'workshop_id' => $workshop->id, 'is_enrolled' => 1]);
        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'enrolled' => 2]);
    }

    public function testReturningFamilyConflict()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[0] = Camper::factory()->create(['family_id' => $head->family_id, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $head->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $yah = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => Programname::Cratty]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $head->id, 'amount' => -400.0, 'year_id' => self::$year->id]);

        $workshopsC = Workshop::factory()->count(2)->create(['year_id' => self::$year->id,
            'timeslot_id' => Timeslotname::Early_Afternoon, 'w' => 1]);
        $workshopsNC[0] = Workshop::factory()->create(['year_id' => self::$year->id,
            'timeslot_id' => Timeslotname::Late_Afternoon, 'm' => 1, 't' => 1, 'w' => 1, 'th' => 0, 'f' => 0]);
        $workshopsNC[1] = Workshop::factory()->create(['year_id' => self::$year->id,
            'timeslot_id' => Timeslotname::Late_Afternoon, 'm' => 0, 't' => 0, 'w' => 0, 'th' => 1, 'f' => 1]);
        $yaws[0] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsC[0]->id]);
        $yaws[1] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsC[1]->id]);
        $yaws[2] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsNC[0]->id]);
        $yaws[3] = YearattendingWorkshop::factory()->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsNC[1]->id]);

        $this->browse(function (Browser $browser) use ($user, $head, $campers, $workshopsC, $workshopsNC) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            $this->pressTab($browser, $head->id);
            $browser->assertMissing('h6.alert')
                ->press('button#workshop-' . $head->id . '-' . $workshopsNC[0]->id)
                ->press('button#workshop-' . $head->id . '-' . $workshopsNC[1]->id)
                ->assertMissing('h6.alert')
                ->press('button#workshop-' . $head->id . '-' . $workshopsC[0]->id)
                ->press('button#workshop-' . $head->id . '-' . $workshopsC[1]->id)
                ->assertPresent('h6.alert');
            parent::assertHasClass($browser, '#workshop-' . $head->id . '-' . $workshopsC[0]->id, 'list-group-item-danger');
            parent::assertHasClass($browser, '#workshop-' . $head->id . '-' . $workshopsC[1]->id, 'list-group-item-danger');
            parent::assertMissingClass($browser, '#workshop-' . $head->id . '-' . $workshopsNC[0]->id, 'list-group-item-danger');
            parent::assertMissingClass($browser, '#workshop-' . $head->id . '-' . $workshopsNC[1]->id, 'list-group-item-danger');
            $this->pressTab($browser, $campers[0]->id);
            parent::assertHasClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshopsC[0]->id, 'list-group-item-danger');
            parent::assertHasClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshopsC[1]->id, 'list-group-item-danger');
            parent::assertMissingClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshopsNC[0]->id, 'list-group-item-danger');
            parent::assertMissingClass($browser, '#workshop-' . $campers[0]->id . '-' . $workshopsNC[1]->id, 'list-group-item-danger');
            $browser->pause(self::WAIT)->press('button#workshop-' . $campers[0]->id . '-' . $workshopsC[1]->id)
                ->assertMissing('h6.alert');
            $this->pressTab($browser, $campers[1]->id); // Tooltip won't drop
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yah->id,
            'workshop_id' => $workshopsC[0]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsC[0]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yah->id,
            'workshop_id' => $workshopsC[1]->id]);
        $this->assertDatabaseMissing('yearsattending__workshop', ['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsC[1]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yah->id,
            'workshop_id' => $workshopsNC[0]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yah->id,
            'workshop_id' => $workshopsNC[1]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsNC[0]->id]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $yas[0]->id,
            'workshop_id' => $workshopsNC[1]->id]);
        $this->assertDatabaseHas('workshops', ['id' => $workshopsC[0]->id, 'enrolled' => 2]);
        $this->assertDatabaseHas('workshops', ['id' => $workshopsC[1]->id, 'enrolled' => 1]);
        $this->assertDatabaseHas('workshops', ['id' => $workshopsNC[0]->id, 'enrolled' => 2]);
        $this->assertDatabaseHas('workshops', ['id' => $workshopsNC[1]->id, 'enrolled' => 2]);
    }

    public function testReturningYAWaitinglistLeader()
    {
        $faker = Factory::create();
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -400.0, 'year_id' => self::$year->id]);

        $workshop = Workshop::factory()->create(['year_id' => self::$year->id, 'capacity' => 5]);
        $campers = Camper::factory()->count(5)->create(['roommate' => __FUNCTION__]);
        foreach ($campers as $onecamper) {
            $yap = Yearattending::factory()->create(['camper_id' => $onecamper->id, 'year_id' => self::$year->id]);
            YearattendingWorkshop::factory()->create(['yearattending_id' => $yap->id,
                'workshop_id' => $workshop->id,
                'created_at' => $faker->dateTimeBetween('-1 year', '-1 day')]);
        }
        UpdateWorkshops::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $camper, $workshop, $ya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->mouseover('button#workshop-' . $camper->id . '-' . $workshop->id)
                ->waitFor('div.tooltip-inner')->assertSeeIn('div.tooltip-inner', 'Workshop Full')
                ->press('button#workshop-' . $camper->id . '-' . $workshop->id);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $ya->id,
            'workshop_id' => $workshop->id, 'is_enrolled' => 0]);
        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'enrolled' => 6]);
        $this->assertEquals(YearattendingWorkshop::where('workshop_id', $workshop->id)->where('is_enrolled', 1)->count(), 4);

        $workshop->led_by = $camper->firstname . ' ' . $camper->lastname . ' and ' . $campers[0]->firstname . ' ' . $campers[0]->lastname;
        $workshop->save();
        UpdateWorkshops::dispatchSync(self::$year->id);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $ya->id,
            'workshop_id' => $workshop->id, 'is_enrolled' => 1, 'is_leader' => 1]);
        $this->assertDatabaseHas('yearsattending__workshop', ['yearattending_id' => $ya->id,
            'workshop_id' => $workshop->id, 'is_enrolled' => 1, 'is_leader' => 1]);
        $this->assertDatabaseHas('yearsattending__workshop', ['workshop_id' => $workshop->id,
            'is_enrolled' => 0]);

    }

    public function testWorkshops()
    {
        $timeslots = Timeslot::all()->except(Timeslotname::Excursions);
        foreach ($timeslots as $timeslot) {
            Workshop::factory()->count(rand(1, 10))->create(['timeslot_id' => $timeslot->id, 'year_id' => self::$year->id]);
            $wrongshops[] = Workshop::factory()->create(['timeslot_id' => $timeslot->id,
                'name' => 'This is the wrong workshop', 'year_id' => self::$lastyear]);
        }

        $this->browse(function (Browser $browser) use ($timeslots, $wrongshops) {
            $browser->visitRoute('workshops.display')->waitFor('div.tab-content div.active');
            foreach ($timeslots as $timeslot) {
                $this->pressTab($browser, $timeslot->id);
                $browser->assertSee($timeslot->start_time->format('g:i A'));
                foreach ($timeslot->workshops()->where('year_id', self::$year->id) as $workshop) {
                    $browser->assertSee($workshop->name)
                        ->assertSee($workshop->display_days);
                    if ($workshop->fee > 0) {
                        $browser->assertSee("$" . $workshop->fee);
                    }
                }
                foreach ($wrongshops as $wrongshop) $browser->assertDontSee($wrongshop->name);
            }
        });
    }


    public function testExcursions()
    {
        Workshop::factory()->count(rand(1, 10))->create(['timeslot_id' => Timeslotname::Excursions, 'year_id' => self::$year->id]);
        $wrongshop = Workshop::factory()->create(['timeslot_id' => Timeslotname::Excursions,
            'year_id' => self::$lastyear->id]);
        $this->browse(function (Browser $browser) use ($wrongshop) {
            $timeslot = Timeslot::findOrFail(Timeslotname::Excursions);
            $browser->visit('/excursions');
            foreach ($timeslot->workshops()->where('year_id', self::$year->id) as $workshop) {
                $browser->assertSee($workshop->name)->assertSee($workshop->displayDays);
                if ($workshop->fee > 0) {
                    $browser->assertSee("$" . $workshop->fee);
                }
            }
            $browser->assertDontSee($wrongshop->name)->assertDontSee(Timeslotname::Morning);
        });
    }

    private function submitSuccess(Browser $browser)
    {
        $browser->script('window.scrollTo(9999,9999)');
        $browser->pause(self::WAIT)->press('Save Changes')->waitUntilMissing('div.alert-danger')
            ->waitFor('div.alert')->assertVisible('div.alert-success');
        return $browser;
    }

    private function pressTab(Browser $browser, $id)
    {
        $browser->script('window.scrollTo(0,0)');
        $browser->pause(self::WAIT)->press('#tablink-' . $id)->pause(self::WAIT);
        return $browser;
    }
}
