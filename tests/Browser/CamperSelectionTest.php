<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Models\Camper;
use App\Models\User;
use App\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

const ROUTE = 'camperselect.index';

/**
 * @group CamperSelect
 */
class CamperSelectionTest extends DuskTestCase
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
                ->assertSee('Who is attending');

            $this->assertDatabaseHas('campers', ['email' => $user->email]);
            $camper = Camper::where('email', $user->email)->firstOrFail();
            $this->assertDatabaseHas('families', ['id' => $camper->family_id]);
            $this->assertDatabaseMissing('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);

            $browser->assertNotChecked('input#camper-' . $camper->id)
                ->assertSeeIn('label[for=camper-' . $camper->id . ']', 'New Camper')
                ->check('camper-' . $camper->id)
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success');

            $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);
        });

    }

    public function testReturningCamper()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $camper->id)
                ->assertSeeIn('label[for="camper-' . $camper->id . '"]',
                    $camper->firstname . ' ' . $camper->lastname);
        });

    }

    public function testReturningCoupleCancel()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertSeeIn('label[for="camper-' . $campers[0]->id . '"]',
                    $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertChecked('camper-' . $campers[1]->id)
                ->assertSeeIn('label[for="camper-' . $campers[1]->id . '"]',
                    $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->uncheck('camper-' . $campers[0]->id)
                ->uncheck('camper-' . $campers[1]->id)
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')
                ->assertNotChecked('camper-' . $campers[0]->id)
                ->assertNotChecked('camper-' . $campers[1]->id);
        });

        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[0]->id,
            'year_id' => self::$year->id]);
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[1]->id,
            'year_id' => self::$year->id]);
        $this->assertDatabaseMissing('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'chargetype_id' => Chargetypename::Deposit]);
    }

    public function testNewFamily()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertSee('Who is attending');

            $this->assertDatabaseHas('campers', ['email' => $user->email]);
            $camper = Camper::where('email', $user->email)->firstOrFail();
            $this->assertDatabaseHas('families', ['id' => $camper->family_id]);
            $campers = Camper::factory()->count(3)->make(['family_id' => $camper->family_id]);

            $browser->assertNotChecked('input#camper-' . $camper->id)->check('camper-' . $camper->id);
            foreach ($campers as $index => $newcamper) {
                $browser->click('button#addcamper')->check('newcheck-' . $index)
                    ->type('newname-' . $index, $newcamper->firstname . ' ' . $newcamper->lastname);
            }
            $browser->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success');

            foreach ($campers as $newcamper) {
                $this->assertDatabaseHas('campers', ['family_id' => $camper->family_id,
                    'firstname' => $newcamper->firstname, 'lastname' => $newcamper->lastname]);
            }
            $newcampers = Camper::where('family_id')->get();
            foreach ($newcampers as $newcamper) {
                $this->assertDatabaseHas('yearsattending', ['camper_id' => $newcamper->id,
                    'year_id' => self::$year->id]);
            }
            $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
                'chargetype_id' => Chargetypename::Deposit]);
        });

    }

    public function testReturningDivorceeKidCantComeAddNewPartnerWithMisclicks()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->make(['family_id' => $campers[0]->family_id]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(ROUTE))->pause(500)
                ->assertSee('Who is attending')
                ->assertNotChecked('camper-' . $campers[0]->id)
                ->assertSeeIn('label[for="camper-' . $campers[0]->id . '"]',
                    $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertNotChecked('camper-' . $campers[1]->id)
                ->assertSeeIn('label[for="camper-' . $campers[1]->id . '"]',
                    $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->check('camper-' . $campers[0]->id)
                ->click('button#addcamper')
                ->click('button#addcamper')
                ->click('button#addcamper')
                ->click('button#delete-1')
                ->assertMissing('input#newname-1')
                ->check('newcheck-2')
                ->type('newname-2', $campers[2]->firstname . ' ' . $campers[2]->lastname)
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertNotChecked('camper-' . $campers[1]->id);

            $this->assertDatabaseMissing('campers', ['family_id' => $campers[0]->family_id,
                'lastname' => null]);
            $this->assertDatabaseHas('campers', ['family_id' => $campers[0]->family_id,
                'firstname' => $campers[2]->firstname, 'lastname' => $campers[2]->lastname]);
            $camper = Camper::where('family_id', $campers[2]->family_id)->where('firstname', $campers[2]->firstname)
                ->where('lastname', $campers[2]->lastname)->firstOrFail();
            $browser->assertChecked('camper-' . $camper->id);

            $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id,
                'year_id' => self::$year->id]);
            $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[1]->id,
                'year_id' => self::$year->id]);
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);
        });
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'chargetype_id' => Chargetypename::Deposit, 'amount' => 400.0]);
    }
}
