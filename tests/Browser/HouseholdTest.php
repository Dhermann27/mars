<?php

namespace Tests\Browser;

use app\Models\Camper;
use app\Models\Charge;
use App\Enums\Foodoptionname;
use App\Enums\Usertype;
use app\Models\Family;
use App\Jobs\GenerateCharges;
use app\Models\User;
use app\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\HouseholdForm;
use Tests\DuskTestCase;
use function factory;

/**
 * @group Household
 */
class HouseholdTest extends DuskTestCase
{

    /**
     * @group Abraham
     * @throws \Throwable
     */
    public function testAbraham()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visitRoute('household.index')->assertSee('Error');
        });

    }

    /**
     * @group Beto
     * @throws \Throwable
     */
    public function testBeto()
    {

        $user = User::factory()->create();
        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['family_id' => $family->id, 'firstname' => 'Beto', 'email' => $user->email]);
        Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchNow(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $changes = Family::factory()->make();

        $this->browse(function (Browser $browser) use ($user, $family, $changes) {
            $browser->loginAs($user->id)->visitRoute('household.index')
                ->waitFor('form#household')
                ->within(new HouseholdForm, function ($browser) use ($family, $changes) {
                    $browser->changeHousehold($family, $changes);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country, 'is_ecomm' => $changes->is_ecomm,
            'is_scholar' => $changes->is_scholar]);

    }

    /**
     * @group Charlie
     * @throws \Throwable
     */
    public function testCharlie()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $family = Family::factory()->make(['is_address_current' => 0]);

        $this->browse(function (Browser $browser) use ($user, $family) {
            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => 0])
                ->waitFor('form#household')
                ->within(new HouseholdForm, function ($browser) use ($family) {
                    $browser->select('select#is_address_current', $family->is_address_current)
                        ->createHousehold($family);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $family->address1,
            'address2' => $family->address2, 'city' => $family->city, 'province_id' => $family->province_id,
            'zipcd' => $family->zipcd, 'country' => $family->country,
            'is_address_current' => $family->is_address_current, 'is_ecomm' => $family->is_ecomm,
            'is_scholar' => $family->is_scholar]);

        $family = Family::latest()->first();

        $this->assertDatabaseHas('campers', ['family_id' => $family->id,
            'firstname' => "New Camper", 'foodoption_id' => Foodoptionname::None]);

        $camper = Camper::latest()->first();
        $changes = Family::factory()->make(['is_address_current' => 1]);

        $this->browse(function (Browser $browser) use ($user, $family, $camper, $changes) {
            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
                ->waitFor('form#household')
                ->within(new HouseholdForm, function ($browser) use ($family, $changes) {
                    $browser->assertSelected('select#is_address_current', $family->is_address_current)
                        ->select('select#is_address_current', $changes->is_address_current)
                        ->changeHousehold($family, $changes);
                });
        });

        $this->assertDatabaseHas('families', ['address1' => $changes->address1,
            'address2' => $changes->address2, 'city' => $changes->city, 'province_id' => $changes->province_id,
            'zipcd' => $changes->zipcd, 'country' => $changes->country,
            'is_address_current' => $changes->is_address_current, 'is_ecomm' => $changes->is_ecomm,
            'is_scholar' => $changes->is_scholar]);
    }

    /**
     * @group Charlie
     * @throws \Throwable
     */
    public function testCharlieRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['family_id' => $family->id]);

        $this->browse(function (Browser $browser) use ($user, $family, $camper) {
            $browser->loginAs($user->id)->visitRoute('household.index', ['id' => $camper->id])
                ->waitFor('form#household')
                ->within(new HouseholdForm, function ($browser) use ($family) {
                    $browser->assertSelected('select#is_address_current', $family->is_address_current)
                        ->assertDisabled('select#is_address_current')
                        ->viewHousehold($family);
                });
        });
    }
}
