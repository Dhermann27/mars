<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Enums\Usertype;
use App\Jobs\GenerateCharges;
use App\Models\Camper;
use App\Models\CamperStaff;
use App\Models\Room;
use App\Models\User;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @group Register
 * @group CamperSelect
 */
class CamperSelectionTest extends DuskTestCase
{
    private const ROUTE = 'camperselect.index';
    private const WAIT = 250;

    public function testNewVisitor()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route(self::ROUTE))->pause(self::WAIT)->assertSee('You need to be logged in');
        });
    }

    public function testAccountButNoCamper()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending');

            $this->assertDatabaseHas('campers', ['email' => $user->email]);
            $camper = Camper::where('email', $user->email)->firstOrFail();
            $changes = Camper::factory()->make(['family_id' => $camper->family_id,
                'roommate' => __FUNCTION__]);
            $this->assertDatabaseHas('families', ['id' => $camper->family_id]);
            $this->assertDatabaseMissing('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);

            $browser->assertChecked('newcheck-' . $camper->id)
                ->type('newname-' . $camper->id, $changes->firstname . ' ' . $changes->lastname);
            $this->submitSuccess($browser, self::WAIT);

            $this->assertDatabaseHas('campers', ['email' => $user->email, 'family_id' => $camper->family_id,
                'firstname' => $changes->firstname, 'lastname' => $changes->lastname]);
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
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
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
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertSeeIn('label[for="camper-' . $campers[0]->id . '"]',
                    $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertChecked('camper-' . $campers[1]->id)
                ->assertSeeIn('label[for="camper-' . $campers[1]->id . '"]',
                    $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->uncheck('camper-' . $campers[0]->id)
                ->uncheck('camper-' . $campers[1]->id);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertNotChecked('camper-' . $campers[0]->id)
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
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending');

            $this->assertDatabaseHas('campers', ['email' => $user->email]);
            $camper = Camper::where('email', $user->email)->firstOrFail();
            $changes = Camper::factory()->make(['family_id' => $camper->family_id,
                'roommate' => __FUNCTION__]);
            $this->assertDatabaseHas('families', ['id' => $camper->family_id]);
            $campers = Camper::factory()->count(3)->make(['family_id' => $camper->family_id,
                'roommate' => __FUNCTION__]);

            $browser->assertChecked('newcheck-' . $camper->id)
                ->type('newname-' . $camper->id, $changes->firstname . ' ' . $changes->lastname);
            foreach ($campers as $index => $newcamper) {
                $browser->press('ADD NEW CAMPER')->assertChecked('newcheck-' . $index)
                    ->type('newname-' . $index, $newcamper->firstname . ' ' . $newcamper->lastname);
            }
            $this->submitSuccess($browser, self::WAIT);

            $this->assertDatabaseHas('campers', ['email' => $user->email, 'family_id' => $camper->family_id,
                'firstname' => $changes->firstname, 'lastname' => $changes->lastname]);
            foreach ($campers as $newcamper) {
                $this->assertDatabaseHas('campers', ['family_id' => $camper->family_id,
                    'firstname' => $newcamper->firstname, 'lastname' => $newcamper->lastname]);
            }
            $newcampers = Camper::where('family_id', $camper->family_id)->get();
            $this->assertCount(4, $newcampers);
            foreach ($newcampers as $newcamper) {
                $this->assertDatabaseHas('yearsattending', ['camper_id' => $newcamper->id,
                    'year_id' => self::$year->id]);
            }
            $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
                'chargetype_id' => Chargetypename::Deposit, 'amount' => 400]);
        });

    }
    public function testReturningDivorceeKidCantComeAddNewPartner()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->make(['family_id' => $campers[0]->family_id]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertNotChecked('camper-' . $campers[0]->id)
                ->assertSeeIn('label[for="camper-' . $campers[0]->id . '"]',
                    $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertNotChecked('camper-' . $campers[1]->id)
                ->assertSeeIn('label[for="camper-' . $campers[1]->id . '"]',
                    $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->check('camper-' . $campers[0]->id)
                ->press('ADD NEW CAMPER')
                ->press('ADD NEW CAMPER')
                ->press('ADD NEW CAMPER')
                ->click('button#delete-1')
                ->assertMissing('input#newname-1')
                ->check('newcheck-2')
                ->type('newname-2', $campers[2]->firstname . ' ' . $campers[2]->lastname);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertChecked('camper-' . $campers[0]->id)
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

    public function testReturningFamilyMisclick()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->make(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertSeeIn('label[for="camper-' . $campers[0]->id . '"]',
                    $campers[0]->firstname . ' ' . $campers[0]->lastname)
                ->assertChecked('camper-' . $campers[1]->id)
                ->assertSeeIn('label[for="camper-' . $campers[1]->id . '"]',
                    $campers[1]->firstname . ' ' . $campers[1]->lastname)
                ->press('ADD NEW CAMPER')
                ->press('ADD NEW CAMPER')
                ->press('ADD NEW CAMPER')
                ->click('button#delete-1')
                ->assertMissing('input#newname-1')
                ->check('newcheck-2')
                ->type('newname-2', $campers[2]->firstname . ' ' . $campers[2]->lastname);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertChecked('camper-' . $campers[0]->id)
                ->assertChecked('camper-' . $campers[1]->id);

            $this->assertDatabaseMissing('campers', ['family_id' => $campers[0]->family_id,
                'lastname' => null]);
            $this->assertDatabaseHas('campers', ['family_id' => $campers[0]->family_id,
                'firstname' => $campers[2]->firstname, 'lastname' => $campers[2]->lastname]);
            $camper = Camper::where('family_id', $campers[2]->family_id)->where('firstname', $campers[2]->firstname)
                ->where('lastname', $campers[2]->lastname)->firstOrFail();
            $browser->assertChecked('camper-' . $camper->id);

            $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id,
                'year_id' => self::$year->id]);
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id,
                'year_id' => self::$year->id]);
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);
        });
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'chargetype_id' => Chargetypename::Deposit, 'amount' => 400.0]);
    }

    public function testReturningYAWithJobs()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $cses = CamperStaff::factory()->count(2)->create(['camper_id' => $camper->id]);
        $this->browse(function (Browser $browser) use ($user, $camper) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertNotChecked('camper-' . $camper->id)
                ->assertSeeIn('label[for="camper-' . $camper->id . '"]',
                    $camper->firstname . ' ' . $camper->lastname)
                ->check('camper-' . $camper->id);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertChecked('camper-' . $camper->id);
        });
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        $ya = Yearattending::where(['camper_id' => $camper->id, 'year_id' => self::$year->id])->firstOrFail();
        $this->assertDatabaseHas('yearsattending__staff', ['yearattending_id' => $ya->id,
            'staffposition_id' => $cses[0]->staffposition_id]);
        $this->assertDatabaseHas('yearsattending__staff', ['yearattending_id' => $ya->id,
            'staffposition_id' => $cses[1]->staffposition_id]);
        $this->assertDatabaseMissing('camper__staff', ['camper_id' => $camper->id,
            'staffposition_id' => $cses[0]->staffposition_id]);
        $this->assertDatabaseMissing('camper__staff', ['camper_id' => $camper->id,
            'staffposition_id' => $cses[1]->staffposition_id]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'chargetype_id' => Chargetypename::Deposit, 'amount' => 200.0]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'chargetype_id' => Chargetypename::Staffcredit, 'memo' => 'Staff Position Credits']);

    }

    public function testReturningFamilyJobCancel()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => Room::factory()->create(['room_number' => __FUNCTION__])->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $yas[0]->room_id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id,
            'room_id' => $yas[0]->room_id]);
        $ys = YearattendingStaff::factory()->create(['yearattending_id' => $yas[2]->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[2]->family_id,
            'chargetype_id' => Chargetypename::Staffcredit, 'memo' => $ys->staffposition->name]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertChecked('camper-' . $campers[1]->id)
                ->assertChecked('camper-' . $campers[2]->id)
                ->uncheck('camper-' . $campers[2]->id);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertNotChecked('camper-' . $campers[2]->id);
        });
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $this->assertDatabaseMissing('yearsattending__staff', ['yearattending_id' => $ys->yearattending_id,
            'staffposition_id' => $ys->staffposition_id]);
        $this->assertDatabaseMissing('thisyear_charges', ['family_id' => $campers[2]->family_id,
            'chargetype_id' => Chargetypename::Staffcredit, 'memo' => $ys->staffposition->name]);
    }

    /**
     * @group Admin
     */
    public function testReturningCamperAddPartnerAdmin()
    {
        $admin = User::factory()->create(['usertype' => Usertype::Admin]);

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $partner = Camper::factory()->make(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($admin, $camper, $partner) {
            $browser->loginAs($admin->id)->visit(route(self::ROUTE, ['id' => $camper->id]))
                ->pause(self::WAIT)->assertSee('Who is attending')
                ->assertChecked('camper-' . $camper->id)
                ->assertSeeIn('label[for="camper-' . $camper->id . '"]',
                    $camper->firstname . ' ' . $camper->lastname)
                ->press('ADD NEW CAMPER')->assertChecked('newcheck-0')
                ->type('newname-0', $partner->firstname . ' ' . $partner->lastname);
            $this->submitSuccess($browser, self::WAIT);

            $browser->click('@next')->assertPathIs('/household/' . $camper->id);
        });
        $this->assertDatabaseHas('campers', ['email' => $user->email, 'family_id' => $camper->family_id]);
        $this->assertDatabaseHas('campers', ['family_id' => $camper->family_id,
            'firstname' => $partner->firstname, 'lastname' => $partner->lastname]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
            'year_id' => self::$year->id]);
        $partner = Camper::where('family_id', $camper->family_id)->where('firstname', $partner->firstname)
            ->where('lastname', $partner->lastname)->firstOrFail();
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
            'year_id' => self::$year->id]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $partner->id,
            'year_id' => self::$year->id]);
    }

    public function testReturningCamperRO()
    {
        $admin = User::factory()->create(['usertype' => Usertype::Pc]);

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($admin, $camper) {
            $browser->loginAs($admin->id)->visit(route(self::ROUTE, ['id' => $camper->id]))
                ->pause(self::WAIT)->assertSee('Who is attending')
                ->uncheck('camper-' . $camper->id)
                ->assertChecked('camper-' . $camper->id)
                ->assertSeeIn('label[for="camper-' . $camper->id . '"]',
                    $camper->firstname . ' ' . $camper->lastname)
                ->assertMissing('button#addcamper')
                ->assertMissing('button[type=submit]');
        });

    }

    public function testNewFamilyAdmin()
    {
        $admin = User::factory()->create(['usertype' => Usertype::Admin]);

        $changes = Camper::factory()->make(['roommate' => __FUNCTION__]);
        $children = Camper::factory()->count(rand(1, 9))->make(['family_id' => $changes->family_id,
            'roommate' => __FUNCTION__]);

        $this->browse(function (Browser $browser) use ($admin, $changes, $children) {
            $browser->loginAs($admin->id)->visit(route(self::ROUTE, ['id' => 0]))->pause(self::WAIT)
                ->assertSee('Who is attending');

            $camper = Camper::where('firstname', 'New Camper')->where('roommate', 'createFamilyAndCamper')
                ->orderBy('created_at', 'desc')->first();

            $browser->assertChecked('newcheck-' . $camper->id)
                ->type('newname-' . $camper->id, $changes->firstname . ' ' . $changes->lastname);
            foreach ($children as $index => $child) {
                $browser->press('ADD NEW CAMPER')->assertChecked('newcheck-' . $index)
                    ->type('newname-' . $index, $child->firstname . ' ' . $child->lastname);
            }
            $this->submitSuccess($browser, self::WAIT);

            $kidcount = count($children);
            $children = Camper::where('family_id', $camper->family_id)->whereNot('id', $camper->id)->get();
            $this->assertEquals($kidcount, count($children));

            $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id,
                'year_id' => self::$year->id]);
            foreach ($children as $child) {
                $this->assertDatabaseHas('yearsattending', ['camper_id' => $child->id,
                    'year_id' => self::$year->id]);
            }
        });

    }

    public function testReturningFamilyAddOneJobCancelAnotherAdmin()
    {
        $admin = User::factory()->create(['usertype' => Usertype::Admin]);

        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'room_id' => Room::factory()->create(['room_number' => __FUNCTION__])->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'room_id' => $yas[0]->room_id]);
        $ys = YearattendingStaff::factory()->create(['yearattending_id' => $yas[1]->id]);
        $cs = CamperStaff::factory()->create(['camper_id' => $campers[2]->id]);
        GenerateCharges::dispatchSync(self::$year->id);

        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[1]->family_id,
            'chargetype_id' => Chargetypename::Staffcredit, 'memo' => $ys->staffposition->name]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visit(route(self::ROUTE))->pause(self::WAIT)
                ->assertSee('Who is attending')
                ->assertChecked('camper-' . $campers[0]->id)
                ->assertChecked('camper-' . $campers[1]->id)
                ->uncheck('camper-' . $campers[1]->id)
                ->assertNotChecked('camper-' . $campers[2]->id)
                ->check('camper-' . $campers[2]->id);
            $this->submitSuccess($browser, self::WAIT);
            $browser->assertNotChecked('camper-' . $campers[1]->id)
                ->assertChecked('camper-' . $campers[2]->id);

            $browser->assertAttributeContains('@previous', 'class', 'disabled');
        });
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $this->assertDatabaseMissing('yearsattending__staff', ['yearattending_id' => $ys->yearattending_id,
            'staffposition_id' => $ys->staffposition_id]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $this->assertDatabaseMissing('camper__staff', ['camper_id' => $campers[2]->id,
            'staffposition_id' => $cs->staffposition_id]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[2]->family_id,
            'chargetype_id' => Chargetypename::Staffcredit, 'memo' => $cs->staffposition->name]);
    }

}
