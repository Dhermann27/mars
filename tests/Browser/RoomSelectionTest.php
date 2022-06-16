<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Jobs\ExposeRoomselection;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\Program;
use App\Models\Rate;
use App\Models\Room;
use App\Models\RoomselectionExpo;
use App\Models\User;
use App\Models\Yearattending;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

/**
 * @group Register
 * @group RoomSelect
 * @group RoomSelection
 * @group Roomselect
 * @group Roomselection
 */
class RoomSelectionTest extends DuskTestCase
{
    private const WAIT = 250;
    private const ROUTE = 'roomselection.index';

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
            $browser->loginAs($user)->visit(route(self::ROUTE))->assertSee('Camp Lakewood Cabins')
                ->assertSee('There are no campers')
                ->assertPresent('button.disabled[type=submit]');
        });
        $this->assertDatabaseHas('campers', ['email' => $user->email]);
    }

    public function testReturningAdultNotPaidExpoOld()
    {
        DB::table('roomselection_expo')->update(['created_at' => Carbon::now()->subDays(5)->toDateTimeString()]);
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $room = Room::factory()->create(['is_workshop' => 0]);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->assertSee('Camp Lakewood Cabins')
                ->assertSee('You need to pay your deposit')
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $room->room_number)
                ->assertPresent('button.disabled[type=submit]');
        });

    }

    public function testReturningYA()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);
        $rate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $room->building_id]);
        $newroom = Room::factory()->create(['is_workshop' => 0]);
        $newrate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $newroom->building_id]);
        ExposeRoomselection::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $room->room_number)
                ->click('rect#room-' . $room->id);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 200.0]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $rate->rate * 6]);

        $this->browse(function (Browser $browser) use ($user, $room, $newroom) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', 'Locked by')
                ->mouseover('rect#room-' . $newroom->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $newroom->room_number)
                ->click('rect#room-' . $newroom->id);
            $this->submitSuccess($browser);
        });

        $this->browse(function (Browser $browser) use ($user, $camper, $room, $newroom) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertDontSeeIn('div.tooltip-inner', $camper->firstname . ' ' . $camper->lastname)
                ->mouseover('rect#room-' . $newroom->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', 'Locked by:')
                ->assertSeeIn('div.tooltip-inner', $camper->firstname . ' ' . $camper->lastname)
                ->assertSeeIn('div.tooltip-inner', 'Current selection');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 200.0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $rate->rate * 6]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $newrate->rate * 6]);

    }

    /**
     * @group Charlie
     * @throws Throwable
     */
//    public function testCharlieLocked()
//    {
//
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//        Camper::factory()->create(['email' => $user->email]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        GenerateCharges::dispatchSync(self::$year->id);
//        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);
//
//        $room = Room::factory()->create(['is_workshop' => 0]);
//        $rate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $room->building_id]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $room) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->click('rect#room-' . $room->id)->click('button[type="submit"]')
//                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
//        });
//
//        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'room_id' => $room->id, 'is_setbyadmin' => 1]);
//        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'chargetype_id' => Chargetypename::Deposit, 'charge' => 200.0]);
//        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'chargetype_id' => Chargetypename::Fees, 'charge' => $rate->rate * 6]);
//
//        $this->browse(function (Browser $browser) use ($cuser, $camper, $room) {
//            $browser->loginAs($cuser->id)->visitRoute(self::ROUTE)
//                ->mouseover('rect#room-' . $room->id)
//                ->waitFor('div.tooltip-inner')->assertSeeIn('div.tooltip-inner', 'Current selection')
//                ->assertSee('locked by the Registrar');
//        });
//    }

    /**
     * @group Charlie
     * @throws Throwable
     */
//    public function testCharlieRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $cuser = User::factory()->create();
//        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
//            'room_id' => function () {
//                return Room::factory()->create(['is_workshop' => 0])->id;
//            }
//        ]);
//
//        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
//            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
//                ->mouseover('rect#room-' . $ya->room_id)->waitFor('div.tooltip-inner')
//                ->assertSeeIn('div.tooltip-inner', 'Locked by')
//                ->assertSeeIn('div.tooltip-inner', $camper->firstname . ' ' . $camper->lastname)
//                ->assertMissing('button[type="submit"]');
//        });
//
//
//    }

    public function testReturningCoupleLockedByOtherFamily()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);

        $otherfamily = Camper::factory()->count(2)->create(['roommate' => __FUNCTION__]);
        $oyas[0] = Yearattending::factory()->create(['camper_id' => $otherfamily[0]->id,
            'year_id' => self::$year->id, 'room_id' => $room->id]);
        $oyas[1] = Yearattending::factory()->create(['camper_id' => $otherfamily[1]->id,
            'year_id' => self::$year->id, 'room_id' => $room->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);
        $newroom = Room::factory()->create(['is_workshop' => 0]);
        $newrates[0] = Rate::factory()->create(['program_id' => $yas[0]->program_id, 'building_id' => $newroom->building_id]);
        $newrates[1] = Rate::factory()->create(['program_id' => $yas[1]->program_id, 'building_id' => $newroom->building_id]);
        ExposeRoomselection::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $room->room_number)
                ->click('rect#room-' . $room->id);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);

        $this->browse(function (Browser $browser) use ($user, $newroom) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $newroom->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $newroom->room_number)
                ->click('rect#room-' . $newroom->id);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 400.0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 400.0]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $newrates[0]->rate * 6]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $newrates[1]->rate * 6]);

    }

    public function testReturningFamilyProgramHousing()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(2)->create(['family_id' => $head->family_id,
            'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => function () {
                return Program::factory()->create(['is_program_housing' => 1])->id;
            }]);

        $room = Room::factory()->create(['is_workshop' => 0]);
        ExposeRoomselection::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $room->room_number)
                ->click('rect#room-' . $room->id);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => null, 'is_setbyadmin' => 0]);
    }

    public function testReturningSeniorSetByAdmin()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'is_setbyadmin' => '1', 'room_id' => Room::factory()->create(['is_workshop' => 0])->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        ExposeRoomselection::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $ya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->assertSee('locked by the Registrar')
                ->mouseover('rect#room-' . $ya->room_id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', 'Locked by')
                ->assertPresent('button.disabled[type=submit]');
        });
    }
    /**
     * @group Oscar
     * @throws Throwable
     */
//    public function testOscarAssign()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $cuser = User::factory()->create();
//        $head = Camper::factory()->create(['firstname' => 'Oscar', 'email' => $cuser->email]);
//        $campers = Camper::factory()->count(2)->create(['family_id' => $head->family_id]);
//        $yas[0] = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
//        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
//        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
//
//        $rooms = Room::factory()->count(6)->create(['is_workshop' => 0]);
//
//        $oyas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$years[1]->id, 'room_id' => $rooms[1]->id]);
//        $oyas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$years[2]->id, 'room_id' => $rooms[2]->id]);
//
//
//        $this->browse(function (Browser $browser) use ($user, $head, $campers, $rooms, $oyas) {
//            $browser->loginAs($user->id)->visitRoute('roomselection.read', ['id' => $head->id])
//                ->assertSee(self::$years[0]->year)->assertSee($rooms[0]->room_number)
//                ->assertSee(self::$years[1]->year)->assertSee($rooms[1]->room_number)
//                ->select('roomid-' . $head->id, $rooms[3]->id)
//                ->select('roomid-' . $campers[0]->id, $rooms[4]->id)
//                ->select('roomid-' . $campers[1]->id, $rooms[5]->id)->click('button[type="submit"]')
//                ->waitFor('div.alert')->assertVisible('div.alert-success');
//        });
//
//        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id,
//            'room_id' => $rooms[3]->id, 'is_setbyadmin' => 1]);
//        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
//            'room_id' => $rooms[4]->id, 'is_setbyadmin' => 1]);
//        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
//            'room_id' => $rooms[5]->id, 'is_setbyadmin' => 1]);
//    }


    public function testReturningSingleMomSwitchView()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(3)->create(['family_id' => $head->family_id,
            'roommate' => __FUNCTION__, 'birthdate' => parent::getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $yas[3] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);
        $newroom = Room::factory()->create(['is_workshop' => 0]);
        ExposeRoomselection::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $head, $campers, $room, $newroom) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->mouseover('rect#room-' . $room->id)->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $room->room_number)
                ->click('rect#room-' . $room->id)
                ->resize(1024, 3072)
                ->assertSeeIn('div.note-info', $head->firstname . ' ' . $head->lastname)
                ->assertSeeIn('div.note-info', $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertSeeIn('div.note-info', $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->assertSeeIn('div.note-info', $campers[2]->firstname . ' ' . $campers[2]->lastname)
                ->assertSelected('roomselect_id', 'room-' . $room->id)
                ->select('roomselect_id', 'room-' . $newroom->id)
                ->resize(2048, 3072)
                ->mouseover('rect.active')->waitFor('div.tooltip-inner')
                ->assertSeeIn('div.tooltip-inner', $newroom->room_number);
            $this->submitSuccess($browser);
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[2]->id, 'year_id' => self::$year->id,
            'room_id' => $newroom->id, 'is_setbyadmin' => 0]);
    }

    // TODO: Add previous year assignment tests

    private function submitError(Browser $browser)
    {
        $browser->script('window.scrollTo(9999,9999)');
        $browser->pause(self::WAIT)->press('Lock Room')->waitFor('div.alert')
            ->assertVisible('div.alert-danger');
        return $browser;
    }

    private function submitSuccess(Browser $browser)
    {
        $browser->script('window.scrollTo(9999,9999)');
        $browser->pause(self::WAIT)->press('Lock Room')->waitUntilMissing('div.alert-danger')
            ->waitFor('div.alert')->assertVisible('div.alert-success');
        return $browser;
    }
}
