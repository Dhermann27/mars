<?php

namespace Tests\Browser;

use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\Family;
use App\Models\Medicalresponse;
use App\Models\Program;
use App\Models\Room;
use App\Models\User;
use App\Models\Workshop;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

const ROUTE = 'dashboard';
const STEPS = array('@step-camperselect', '@step-household', '@step-campers', '@step-payment', '@step-roomselection',
    '@step-workshopchoice', '@step-nametag', '@step-medicalresponse');
const FA_ACTION = 'fa-diamond-exclamation';
const FA_BLOCKED = 'fa-do-not-enter';
const FA_SUCCESS = 'fa-square-check';
const FA_BASE = 'svg-inline--fa stepper-state-icon fa-5x';
/**
 * @group Register
 * @group Dashboard
 */
class DashboardTest extends DuskTestCase
{

    public function testNewVisitor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route(ROUTE))->pause(500)->assertSee('You need to be logged in');
        });
    }

    public function testAccountButNoCamper()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertAttributeContains(STEPS[0], 'class', FA_ACTION);
            $this->assertAfter($browser, 0, FA_BLOCKED);
        });
    }

    public function testCamperButNotSelected()
    {
        $user = User::factory()->create();
        $campers = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertAttributeContains(STEPS[0], 'class', FA_ACTION);
            $this->assertAfter($browser, 0, FA_BLOCKED);
        });

    }

    public function testCamperSelected()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 3, FA_SUCCESS);
            $browser->assertAttributeContains(STEPS[3], 'class', FA_ACTION);
            $this->assertAfter($browser, 3, FA_BLOCKED);
        });
    }

    public function testCamperAddressNotCurrent()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'family_id' => function () {
                return Family::factory()->create(['is_address_current' => 0])->id;
            }]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertAttributeContains(STEPS[0], 'class', FA_SUCCESS)
                ->assertAttributeContains(STEPS[1], 'class', FA_ACTION);
            $this->assertAfter($browser, 1, FA_BLOCKED);
        });
    }

    public function testCamperAddressNotSet()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'family_id' => function () {
                return Family::factory()->create(['address1' => 'NEED ADDRESS'])->id;
            }]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertAttributeContains(STEPS[0], 'class', FA_SUCCESS)
                ->assertAttributeContains(STEPS[1], 'class', FA_ACTION);
            $this->assertAfter($browser, 1, FA_BLOCKED);
        });
    }

    public function testCamperBirthdateNotSet()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'birthdate' => null]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 2, FA_SUCCESS);
            $browser->assertAttributeContains(STEPS[2], 'class', FA_ACTION);
            $this->assertAfter($browser, 2, FA_BLOCKED);
        });
    }

    public function testFamilyProgramNotSet()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(2)->create(['family_id' => $camper->family_id,
            'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => null]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 2, FA_SUCCESS);
            $browser->assertAttributeContains(STEPS[2], 'class', FA_ACTION);
            $this->assertAfter($browser, 2, FA_BLOCKED);
        });
    }

    public function testCamperPaid()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id, 'amount' => -200]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 4, FA_SUCCESS);
            $browser->assertAttributeContains(STEPS[4], 'class', FA_ACTION)
                ->assertAttribute(STEPS[5], 'class', FA_BASE)
                ->assertAttribute(STEPS[6], 'class', FA_BASE);
            $this->assertAfter($browser, 6, FA_BLOCKED);
        });
    }

    public function testCamperRoomSelected()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => function () {
                return Room::factory()->create()->id;
            }]);
        GenerateCharges::dispatchSync(self::$year->id);
        $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id, 'amount' => -200]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 5, FA_SUCCESS);
            $browser->assertAttribute(STEPS[6], 'class', FA_BASE);
            $this->assertAfter($browser, 6, FA_BLOCKED);
        });
    }

    public function testFamilyProgramHousing()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[0] = Camper::factory()->create(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);

        $yas[0] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => function () {
                return Room::factory()->create()->id;
            }]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $yas[0]->room_id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => function () {
                return Program::factory()->create(['is_program_housing' => 1])->id;
            }]);
        GenerateCharges::dispatchSync(self::$year->id);
        $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id, 'amount' => -400]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 5, FA_SUCCESS);
            $browser->assertAttribute(STEPS[6], 'class', FA_BASE)
                ->assertAttributeContains(STEPS[7], 'class', FA_ACTION);
        });
    }

    public function testFamilyWorkshopsNametags()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[0] = Camper::factory()->create(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);

        $yas[0] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => function () {
                return Room::factory()->create()->id;
            }, 'nametag' => 12345678]);
        $yws = YearattendingWorkshop::factory()->count(3)->create(['yearattending_id' => $yas[0]->id,
            'workshop_id' => function () {
                return Workshop::factory()->create(['year_id' => self::$year->id])->id;
            }]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => $yas[0]->room_id, 'nametag' => 12345678]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'program_id' => function () {
                return Program::factory()->create(['is_program_housing' => 1])->id;
            }, 'nametag' => 12345678]);
        GenerateCharges::dispatchSync(self::$year->id);
        $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id, 'amount' => -400]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 7, FA_SUCCESS);
            $browser->assertAttributeContains(STEPS[7], 'class', FA_ACTION);
        });
    }

    public function testFamilyMedicalResponses()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers = Camper::factory()->count(3)->create(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);

        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'room_id' => function () {
                return Room::factory()->create()->id;
            }, 'nametag' => 12345678]);
        $yw = YearattendingWorkshop::factory()->create(['yearattending_id' => $ya->id,
            'workshop_id' => function () {
                return Workshop::factory()->create(['year_id' => self::$year->id])->id;
            }]);
        foreach ($campers as $child) {
            $cya = Yearattending::factory()->create(['camper_id' => $child->id, 'year_id' => self::$year->id,
                'room_id' => $ya->room_id]);
            $medicalresponses[] = Medicalresponse::factory()->create(['yearattending_id' => $cya->id]);
            $yw = YearattendingWorkshop::factory()->create(['yearattending_id' => $cya->id,
                'workshop_id' => $yw->workshop_id]);
        }
        GenerateCharges::dispatchSync(self::$year->id);
        $charge = Charge::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id, 'amount' => -400]);
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500);
            $this->assertBefore($browser, 8, FA_SUCCESS);
        });
    }

    /**
     * @param Browser $browser
     * @return void
     */
    function assertAfter(Browser $browser, $index, $icon): void
    {
        for ($i = $index + 1; $i < count(STEPS); $i++) {
            $browser->assertAttributeContains(STEPS[$i], 'class', $icon);

        }
    }

    /**
     * @param Browser $browser
     * @return void
     */
    function assertBefore(Browser $browser, $index, $icon): void
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            $browser->assertAttributeContains(STEPS[$i], 'class', $icon);

        }
    }
}
