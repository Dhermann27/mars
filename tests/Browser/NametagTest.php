<?php

namespace Tests\Browser;

use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\User;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @group Register
 * @group Nametag
 */
class NametagTest extends DuskTestCase
{
    private const WAIT = 500;
    private const ROUTE = 'nametag.index';
    private const ACTIVETAB = 'form#nametagform div.tab-content div.active';
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

        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->assertSee('until your deposit has been paid')
                ->assertSeeIn(self::ACTIVETAB . ' .label .name', $camper->firstname . ' ' . $camper->lastname)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line1', $camper->family->city . ', ' . $camper->family->province->code)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line2', $camper->church->name)
                ->assertSeeIn(self::ACTIVETAB . ' .label .pronoun', $camper->pronoun->name)
                ->assertMissing('button[type=submit]');
        });
    }

    public function testReturningCamperNameSizePronoun()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $camper->id, 'amount' => -200.0, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->assertSeeIn(self::ACTIVETAB . ' .label .name', $camper->firstname . ' ' . $camper->lastname)
                ->assertSeeIn(self::ACTIVETAB . ' .label .pronoun', $camper->pronoun->name)
                ->select('name-' . $camper->id, '4')
                ->select('namesize-' . $camper->id, '4')
                ->uncheck('pronoun-' . $camper->id)
                ->assertDontSeeIn('.label', $camper->lastname)
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-size: 2.3em')
                ->assertDontSeeIn('.label .pronoun', $camper->pronoun->name)->pause(self::WAIT);
            $this->submitSuccess($browser);
            $browser->assertDontSeeIn('.label', $camper->lastname)
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-size: 2.3em')
                ->assertDontSeeIn('.label', $camper->pronoun->name);
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'nametag' => '144215521']);
    }

    public function testReturningCoupleAllandNoneCopy()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $staff = YearattendingStaff::factory()->create(['yearattending_id' => $yas[0]]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);


        $this->browse(function (Browser $browser) use ($user, $campers, $staff) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            $this->pressTab($browser, $campers[0]->id);
            $browser->select('line1-' . $campers[0]->id, '1')
                ->select('line2-' . $campers[0]->id, '2')
                ->select('line3-' . $campers[0]->id, '3')
                ->select('line4-' . $campers[0]->id, '4')
                ->assertSeeIn(self::ACTIVETAB . ' .label .line1', $campers[0]->church->name)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line2', $campers[0]->family->city . ', ' . $campers[0]->family->province->code)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line3', 'Your PC Position')
                ->assertSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper');
//                ->click('#copyAnswers-' . $campers[0]->id)->pause(self::WAIT);
            $this->pressTab($browser, $campers[1]->id);
            $browser->assertSeeIn(self::ACTIVETAB . ' .label .line2', $campers[1]->church->name) // Reverse when copy fixed
            ->assertSeeIn(self::ACTIVETAB . ' .label .line1', $campers[1]->family->city . ', ' . $campers[0]->family->province->code)
//                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line3', 'Your PC Position')
//                ->assertSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper')
                ->select('line1-' . $campers[1]->id, '5')
                ->select('line2-' . $campers[1]->id, '5')
                ->select('line3-' . $campers[1]->id, '5')
                ->select('line4-' . $campers[1]->id, '5')
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line1', $campers[0]->church->name)
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line2', $campers[0]->family->city . ', ' . $campers[0]->family->province->code)
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line3', 'Your PC Position')
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper');
            $this->submitSuccess($browser);
            $this->pressTab($browser, $campers[0]->id);
            $browser->assertSeeIn(self::ACTIVETAB . ' .label .line1', $campers[0]->church->name)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line2', $campers[0]->family->city . ', ' . $campers[0]->family->province->code)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line3', $staff->staffposition->name)
                ->assertSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper');
            $this->pressTab($browser, $campers[1]->id);
            $browser->assertDontSeeIn(self::ACTIVETAB . ' .label .line1', $campers[0]->church->name)
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line2', $campers[0]->family->city . ', ' . $campers[0]->family->province->code)
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line3', 'Your PC Position')
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'nametag' => '222123421']);

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'nametag' => '222555521']);

    }


    public function testReturningFamilyFontsParent()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'lastname' => $campers[0]->lastname]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        Charge::factory()->create(['camper_id' => $campers[0]->id, 'amount' => -400.0, 'year_id' => self::$year->id]);


        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            $this->pressTab($browser, $campers[0]->id);
            $browser->select('font-' . $campers[0]->id, '2')
                ->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: "Indie Flower"');
            $this->pressTab($browser, $campers[1]->id);
            $browser->select('font-' . $campers[1]->id, '4')->pause(self::WAIT)
                ->check('fontapply-' . $campers[1]->id)
                ->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: Jost')
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-family: "Mystery Quest"');
            $this->pressTab($browser, $campers[2]->id);
            $browser
                ->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: Jost')
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-family: Jost')
                ->assertSeeIn(self::ACTIVETAB . ' .label .parent', $campers[0]->lastname);
            $this->submitSuccess($browser);
            $this->pressTab($browser, $campers[0]->id);
            $browser->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: Indie Flower');
            $this->pressTab($browser, $campers[1]->id);
            $browser->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: Jost')
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-family: Mystery Quest');
            $this->pressTab($browser, $campers[2]->id);
            $browser->assertAttributeContains(self::ACTIVETAB . ' .label', 'style', 'font-family: Jost')
                ->assertAttributeContains(self::ACTIVETAB . ' .label .name', 'style', 'font-family: Jost');

        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'nametag' => '222215522']);

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'nametag' => '222215514']);

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[2]->id, 'year_id' => self::$year->id,
            'nametag' => '222215521']);

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
