<?php

namespace Tests\Browser;

use App\Models\Camper;
use App\Models\Family;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Household;
use Tests\DuskTestCase;


/**
 * @group Register
 * @group Household
 */
class HouseholdTest extends DuskTestCase
{
    private const ROUTE = 'household.index';

    public function testNewVisitor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute(self::ROUTE)->assertSee('You need to be logged in');
        });

    }

    public function testNewCamper()
    {
        $user = User::factory()->create();
        $changes = Family::factory()->make();

        $this->browse(function (Browser $browser) use ($user, $changes) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->waitFor('form#household')
                ->within(new Household, function ($browser) use ($changes) {
                    $browser->createHousehold($changes);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country, 'is_ecomm' => $changes->is_ecomm,
            'is_scholar' => $changes->is_scholar, 'is_address_current' => 1]);

    }

    public function testReturningCamper()
    {
        $user = User::factory()->create();
        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['family_id' => $family->id, 'roommate' => __FUNCTION__,
            'email' => $user->email]);

        $changes = Family::factory()->make();

        $this->browse(function (Browser $browser) use ($user, $family, $changes) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)
                ->waitFor('form#household')
                ->within(new Household, function ($browser) use ($family, $changes) {
                    $browser->changeHousehold($family, $changes);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country, 'is_ecomm' => $changes->is_ecomm,
            'is_scholar' => $changes->is_scholar, 'is_address_current' => 1]);

    }

    /**
     * @group Charlie
     * @throws \Throwable
     */
//    public function testCharlie()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//
//        $family = Family::factory()->make(['is_address_current' => 0]);
//
//        $this->browse(function (Browser $browser) use ($user, $family) {
//            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => 0])
//                ->waitFor('form#household')
//                ->within(new HouseholdForm, function ($browser) use ($family) {
//                    $browser->select('select#is_address_current', $family->is_address_current)
//                        ->createHousehold($family);
//                });
//        });
//
//        $this->assertDatabaseHas('families', ['address1' => $family->address1,
//            'address2' => $family->address2, 'city' => $family->city, 'province_id' => $family->province_id,
//            'zipcd' => $family->zipcd, 'country' => $family->country,
//            'is_address_current' => $family->is_address_current, 'is_ecomm' => $family->is_ecomm,
//            'is_scholar' => $family->is_scholar]);
//
//        $family = Family::latest()->first();
//
//        $this->assertDatabaseHas('campers', ['family_id' => $family->id,
//            'firstname' => "New Camper", 'foodoption_id' => Foodoptionname::None]);
//
//        $camper = Camper::latest()->first();
//        $changes = Family::factory()->make(['is_address_current' => 1]);
//
//        $this->browse(function (Browser $browser) use ($user, $family, $camper, $changes) {
//            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
//                ->waitFor('form#household')
//                ->within(new HouseholdForm, function ($browser) use ($family, $changes) {
//                    $browser->assertSelected('select#is_address_current', $family->is_address_current)
//                        ->select('select#is_address_current', $changes->is_address_current)
//                        ->changeHousehold($family, $changes);
//                });
//        });
//
//        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
//            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
//            'zipcd' => $changes->zipcd, 'country' => $changes->country,
//            'is_address_current' => $changes->is_address_current, 'is_ecomm' => $changes->is_ecomm,
//            'is_scholar' => $changes->is_scholar]);
//    }

    /**
     * @group Charlie
     * @throws \Throwable
     */
//    public function testCharlieRO()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//
//        $family = Family::factory()->create();
//        $camper = Camper::factory()->create(['family_id' => $family->id]);
//
//        $this->browse(function (Browser $browser) use ($user, $family, $camper) {
//            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
//                ->waitFor('form#household')
//                ->within(new HouseholdForm, function ($browser) use ($family) {
//                    $browser->assertSelected('select#is_address_current', $family->is_address_current)
//                        ->assertDisabled('select#is_address_current')
//                        ->viewHousehold($family);
//                });
//        });
//    }
}
