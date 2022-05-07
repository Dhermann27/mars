<?php

namespace Tests\Browser;

use app\Models\Camper;
use app\Models\Charge;
use App\Enums\Chargetypename;
use App\Enums\Usertype;
use App\Jobs\GenerateCharges;
use app\Models\Program;
use app\Models\Rate;
use app\Models\Room;
use app\Models\User;
use app\Models\Year;
use app\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;
use function factory;

/**
 * @group RoomSelection
 */
class RoomSelectionTest extends DuskTestCase
{
    /**
     * @group Abraham
     * @throws Throwable
     */
    public function testAbraham()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->assertSee('Error');
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
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);
        $rate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $room->building_id]);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $room->id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', $room->room_number)
                ->click('rect#room-' . $room->id)->click('button[type="submit"]')
                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 200.0]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $rate->rate * 6]);

        $newroom = Room::factory()->create(['is_workshop' => 0]);
        $newrate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $newroom->building_id]);

        $this->browse(function (Browser $browser) use ($user, $room, $newroom) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $room->id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', 'Locked by')
                ->click('rect#room-' . $newroom->id)->click('button[type="submit"]')
                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
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
    public function testCharlieLocked()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        Camper::factory()->create(['email' => $user->email]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);
        $rate = Rate::factory()->create(['program_id' => $ya->program_id, 'building_id' => $room->building_id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $room) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index', ['id' => $camper->id])
                ->click('rect#room-' . $room->id)->click('button[type="submit"]')
                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 1]);
        $this->assertDatabaseMissing('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Deposit, 'charge' => 200.0]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'chargetype_id' => Chargetypename::Fees, 'charge' => $rate->rate * 6]);

        $this->browse(function (Browser $browser) use ($cuser, $camper, $room) {
            $browser->loginAs($cuser->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $room->id)
                ->waitFor('div#mytooltip')->assertSeeIn('div#mytooltip', 'Current selection')
                ->assertSee('locked by the Registrar');
        });
    }

    /**
     * @group Charlie
     * @throws Throwable
     */
    public function testCharlieRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => function () {
                return Room::factory()->create(['is_workshop' => 0])->id;
            }
        ]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index', ['id' => $camper->id])
                ->mouseover('rect#room-' . $ya->room_id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', 'Locked by')
                ->assertSeeIn('div#mytooltip', $camper->firstname . ' ' . $camper->lastname)
                ->assertMissing('button[type="submit"]');
        });


    }

    /**
     * @group Deb
     * @throws Throwable
     */
    public function testDebLocked()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Deb', 'email' => $user->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $room = Room::factory()->create(['is_workshop' => 0]);

        $otherfamily = factory(Camper::class, 2)->create();
        $oyas[0] = Yearattending::factory()->create(['camper_id' => $otherfamily[0]->id,
            'year_id' => self::$year->id, 'room_id' => $room->id]);
        $oyas[1] = Yearattending::factory()->create(['camper_id' => $otherfamily[1]->id,
            'year_id' => self::$year->id, 'room_id' => $room->id]);
        GenerateCharges::dispatchNow(self::$year->id);

        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);
        $newroom = Room::factory()->create(['is_workshop' => 0]);
        $newrates[0] = Rate::factory()->create(['program_id' => $yas[0]->program_id, 'building_id' => $newroom->building_id]);
        $newrates[1] = Rate::factory()->create(['program_id' => $yas[1]->program_id, 'building_id' => $newroom->building_id]);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $room->id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', $room->room_number)
                ->click('rect#room-' . $room->id)->click('button[type="submit"]')
                ->acceptDialog()->assertMissing('div.alert-success');
        });

        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);

        $this->browse(function (Browser $browser) use ($user, $newroom) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $newroom->id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', $newroom->room_number)
                ->click('rect#room-' . $newroom->id)->click('button[type="submit"]')
                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
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

    /**
     * @group Nancy
     * @throws Throwable
     */
    public function testNancyProgramHousing()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['firstname' => 'Nancy', 'email' => $user->email]);
        $campers = factory(Camper::class, 2)->create(['family_id' => $head->family_id]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => function () {
                return Program::factory()->create(['is_program_housing' => 1])->id;
            }]);

        $room = Room::factory()->create(['is_workshop' => 0]);

        $this->browse(function (Browser $browser) use ($user, $room) {
            $browser->loginAs($user->id)->visitRoute('roomselection.index')
                ->mouseover('rect#room-' . $room->id)->waitFor('div#mytooltip')
                ->assertSeeIn('div#mytooltip', $room->room_number)
                ->click('rect#room-' . $room->id)->click('button[type="submit"]')
                ->acceptDialog()->assertMissing('div.alert-success');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $room->id, 'is_setbyadmin' => 0]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => null, 'is_setbyadmin' => 0]);
    }

    /**
     * @group Oscar
     * @throws Throwable
     */
    public function testOscarAssign()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $head = Camper::factory()->create(['firstname' => 'Oscar', 'email' => $cuser->email]);
        $campers = factory(Camper::class, 2)->create(['family_id' => $head->family_id]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $rooms = factory(Room::class, 6)->create(['is_workshop' => 0]);

        $oyas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$years[1]->id, 'room_id' => $rooms[1]->id]);
        $oyas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$years[2]->id, 'room_id' => $rooms[2]->id]);


        $this->browse(function (Browser $browser) use ($user, $head, $campers, $rooms, $oyas) {
            $browser->loginAs($user->id)->visitRoute('roomselection.read', ['id' => $head->id])
                ->assertSee(self::$years[0]->year)->assertSee($rooms[0]->room_number)
                ->assertSee(self::$years[1]->year)->assertSee($rooms[1]->room_number)
                ->select('roomid-' . $head->id, $rooms[3]->id)
                ->select('roomid-' . $campers[0]->id, $rooms[4]->id)
                ->select('roomid-' . $campers[1]->id, $rooms[5]->id)->click('button[type="submit"]')
                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id,
            'room_id' => $rooms[3]->id, 'is_setbyadmin' => 1]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $rooms[4]->id, 'is_setbyadmin' => 1]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $rooms[5]->id, 'is_setbyadmin' => 1]);
    }

    // TODO: Add previous year assignment tests
}
