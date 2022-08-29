<?php

namespace Tests\Browser;

use App\Enums\Usertype;
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

                    $this->assertDatabaseHas('families', ['address1' => 'NEED ADDRESS',
                        'is_address_current' => 0]);

                    $this->submitSuccess($browser, 0);
                });

            $browser->scrollIntoView('@previous')->pause(250)->press('@previous')->assertPathIs('/camperselect');
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
                    $this->submitSuccess($browser, 0);

                    $browser->scrollIntoView('@next')->pause(250)->press('@next')->assertPathIs('/camperinfo');
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country, 'is_ecomm' => $changes->is_ecomm,
            'is_scholar' => $changes->is_scholar, 'is_address_current' => 1]);

    }

    public function testReturningCamperAdminSnailmailScholarship()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['family_id' => $family->id, 'roommate' => __FUNCTION__,
            'email' => $user->email]);

        $changes = Family::factory()->make(['is_ecomm' => 0, 'is_scholar' => 1]);
        $this->browse(function (Browser $browser) use ($user, $family, $camper, $changes) {
            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
                ->waitFor('form#household')
                ->within(new Household, function ($browser) use ($family, $changes) {
                    $browser->assertChecked('#is_address_current')
                        ->changeHousehold($family, $changes);
                    $this->submitSuccess($browser, 0);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country, 'is_address_current' => 1,
            'is_ecomm' => $changes->is_ecomm, 'is_scholar' => $changes->is_scholar]);
    }

    public function testReturningCamperRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['family_id' => $family->id]);

        $this->browse(function (Browser $browser) use ($user, $family, $camper) {
            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
                ->waitFor('form#household')
                ->within(new Household, function ($browser) use ($family) {
                    $browser->viewHousehold($family);
                });
        });
    }
}
