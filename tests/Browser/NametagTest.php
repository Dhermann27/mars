<?php

namespace Tests\Browser;

use App\Enums\Pronounname;
use App\Enums\Usertype;
use App\Jobs\ExposeParentsChild;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\ThisyearCamper;
use App\Models\User;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @group Register
 * @group Nametag
 * @group Nametags
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

    public function testReturningSingleMomAllandNoneCopy()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'pronoun_id' => Pronounname::SheHer]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => $this->getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $staff = YearattendingStaff::factory()->create(['yearattending_id' => $yas[0]]);
        GenerateCharges::dispatchSync(self::$year->id);
        ExposeParentsChild::dispatchSync(self::$year->id);
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
                ->assertSeeIn(self::ACTIVETAB . ' .label .parent', $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertAttributeContains(self::ACTIVETAB . ' .label .parent svg', 'class', 'fa-person-dress')
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
                ->assertDontSeeIn(self::ACTIVETAB . ' .label .line4', 'First-time Camper')
                ->assertSeeIn(self::ACTIVETAB . ' .label .parent', $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertAttributeContains(self::ACTIVETAB . ' .label .parent svg', 'class', 'fa-person-dress');
        });

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'nametag' => '222123421']);

        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'nametag' => '222555521']);

    }


    public function testReturningFamilyFontsParentHeShe()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__,
            'pronoun_id' => Pronounname::HeHim]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'lastname' => $campers[0]->lastname, 'pronoun_id' => Pronounname::SheHer]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        GenerateCharges::dispatchSync(self::$year->id);
        ExposeParentsChild::dispatchSync(self::$year->id);
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
                ->assertAttributeContains(self::ACTIVETAB . ' .label .parent svg', 'class', 'fa-family')
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

    public function testPCNametagPrint()
    {
        $offset = ThisyearCamper::count();

        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        $campers[0] = Camper::factory()->create(['roommate' => __FUNCTION__, 'pronoun_id' => Pronounname::HeHim]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $campers[3] = Camper::factory()->create(['roommate' => __FUNCTION__, 'pronoun_id' => Pronounname::HeHim]);
        $campers[4] = Camper::factory()->create(['family_id' => $campers[3]->family_id, 'roommate' => __FUNCTION__,
            'lastname' => $campers[3]->lastname]);
        $campers[5] = Camper::factory()->create(['family_id' => $campers[3]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $campers[6] = Camper::factory()->create(['roommate' => __FUNCTION__]);
        $campers[7] = Camper::factory()->create(['family_id' => $campers[6]->family_id, 'roommate' => __FUNCTION__,
            'lastname' => $campers[6]->lastname]);
        $campers[8] = Camper::factory()->create(['family_id' => $campers[6]->family_id, 'roommate' => __FUNCTION__,
            'lastname' => $campers[6]->lastname, 'birthdate' => $this->getYABirthdate()]);
        $campers[9] = Camper::factory()->create(['family_id' => $campers[6]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $campers[10] = Camper::factory()->create(['family_id' => $campers[6]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => parent::getChildBirthdate()]);
        $campers[11] = Camper::factory()->create(['roommate' => __FUNCTION__, 'birthdate' => parent::getChildBirthdate()]);
        $campers[12] = Camper::factory()->create(['roommate' => __FUNCTION__]);
        $campers[13] = Camper::factory()->create(['roommate' => __FUNCTION__,
            'sponsor' => $campers[12]->firstname . ' ' . $campers[12]->lastname, 'birthdate' => parent::getChildBirthdate()]);
        foreach ($campers as $camper) {
            $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        }

        $this->browse(function (Browser $browser) use ($user, $campers, $offset) {
            $browser->loginAs($user->id)->visitRoute('tools.nametags.all')
                // Single Dad Two Kids
                ->assertSeeIn('@label-' . $offset++, $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-person')
                ->assertSeeIn('@parent-' . $offset++, $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[2]->firstname . ' ' . $campers[2]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-person')
                ->assertSeeIn('@parent-' . $offset++, $campers[0]->firstname . ' ' . $campers[0]->lastname)
                // Couple Any One Kid
                ->assertSeeIn('@label-' . $offset++, $campers[3]->firstname . ' ' . $campers[3]->lastname)
                ->assertSeeIn('@label-' . $offset++, $campers[4]->firstname . ' ' . $campers[4]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[5]->firstname . ' ' . $campers[5]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-family')
                ->assertSeeIn('@parent-' . $offset++, $campers[3]->lastname)
                // Older Sibling Two Kids
                ->assertSeeIn('@label-' . $offset++, $campers[6]->firstname . ' ' . $campers[6]->lastname)
                ->assertSeeIn('@label-' . $offset++, $campers[7]->firstname . ' ' . $campers[7]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[8]->firstname . ' ' . $campers[8]->lastname)
                ->assertMissing('@parent-' . $offset++)
                ->assertSeeIn('@label-' . $offset, $campers[9]->firstname . ' ' . $campers[9]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-people-group')
                ->assertSeeIn('@parent-' . $offset++, $campers[6]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[10]->firstname . ' ' . $campers[10]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-people-group')
                ->assertSeeIn('@parent-' . $offset++, $campers[6]->lastname)
                // Lost
                ->assertSeeIn('@label-' . $offset, $campers[11]->firstname . ' ' . $campers[11]->lastname)
                ->assertSeeIn('@parent-' . $offset++, 'SPONSOR NEEDED')
                // Sponsored
                ->assertSeeIn('@label-' . $offset++, $campers[12]->firstname . ' ' . $campers[12]->lastname)
                ->assertSeeIn('@label-' . $offset, $campers[13]->firstname . ' ' . $campers[13]->lastname)
                ->assertAttributeContains('@icon-' . $offset, 'class', 'fa-person')
                ->assertSeeIn('@parent-' . $offset, $campers[12]->firstname . ' ' . $campers[12]->lastname);
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
