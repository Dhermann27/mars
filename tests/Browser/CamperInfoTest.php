<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Enums\Programname;
use App\Enums\Usertype;
use App\Models\Camper;
use App\Models\Family;
use App\Models\User;
use App\Models\Yearattending;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\CamperInfo;
use Tests\DuskTestCase;


/**
 * @group Register
 * @group CamperInfo
 */
class CamperInfoTest extends DuskTestCase
{
    private const WAIT = 400;
    private const ROUTE = 'camperinfo.index';
    private const ACTIVETAB = 'form#camperinfo div.tab-content div.active';

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
            $browser->loginAs($user)->visit(route(self::ROUTE))
                ->assertInputValue('firstname[]', 'New Camper')
                ->assertInputValue('email[]', $user->email);
        });
        $this->assertDatabaseHas('campers', ['email' => $user->email]);
    }

    public function testNewCamperChangeLoginLastProgramID()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => null]);
        $lya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$lastyear->id,
            'program_id' => Programname::Burt]);

        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__]);
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$lastyear->id]);
        $this->browse(function (Browser $browser) use ($user, $camper, $lya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $lya, $changes, $cya) {
                    $browser->changeCamper([$camper, $lya], [$changes, $cya]);
                });
            $this->submitSuccess($browser, self::WAIT);

            $browser->scrollIntoView('@next')->pause(self::WAIT)->press('@next')->assertPathIs('/payment');
        });

        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    public function testReturningCamperAdmin()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $cuser->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'roommate' => __FUNCTION__]);
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
                ->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                })->assertInputValue(self::ACTIVETAB . ' input[name="days[]"]', $cya->days)
                ->clear(self::ACTIVETAB . ' input[name="days[]"]')
                ->type(self::ACTIVETAB . ' input[name="days[]"]', $cya->days);
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
    }

    public function testReturningCamperRO()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $cuser->email, 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $camper->id])
                ->waitFor(self::ACTIVETAB);
            $browser->within(new CamperInfo, function ($browser) use ($camper, $ya) {
                $browser->viewCamper($camper, $ya);
            })->assertInputValue(self::ACTIVETAB . ' input[name="days[]"]', $ya->days)
                ->assertAttributeContains(self::ACTIVETAB . ' input[name="days[]"]', 'readonly', 'true')
                ->assertMissing('button[type="submit"]');
        });


    }

    public function testReturningCoupleHandicapDistinctEmails()
    {
        $users = User::factory()->count(2)->create();

        // Need $campers[0] to be in tab 0
        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $users[0]->email, 'roommate' => __FUNCTION__, 'birthdate' => self::$year->year - 35 . '-01-01']);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => self::$year->year - 30 . '-01-01', 'email' => $users[1]->email, 'is_handicap' => 1]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->count(2)->make(['roommate' => __FUNCTION__, 'is_handicap' => 1]);
        $cyas[0] = Yearattending::factory()->make(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $cyas[1] = Yearattending::factory()->make(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $this->browse(function (Browser $browser) use ($users, $campers, $yas, $changes, $cyas) {
            $otheremail = $changes[1]->email;
            $changes[1]->email = $changes[0]->email;

            $browser->loginAs($users[0]->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $yas, $changes, $cyas) {
                        $browser->changeCamper([$campers[$i], $yas[$i]], [$changes[$i], $cyas[$i]]);
                    });
            }
            $this->submitError($browser, self::WAIT);
            $changes[0]->email = $otheremail;
            $browser->type('email[]', $otheremail);
            $this->submitSuccess($browser, self::WAIT);

        });

        $this->assertDatabaseMissing('users', ['email' => $users[0]->email]);
        $this->assertDatabaseMissing('users', ['email' => $users[1]->email]);
        foreach ($changes as $change) $this->adh($change);
        $this->assertDatabaseHas('users', ['email' => $changes[0]->email]);
        $this->assertDatabaseHas('users', ['email' => $changes[1]->email]);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $ya->camper_id,
                'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'amount' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }

    public function testReturningCoupleDaysAdmin()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['email' => $cuser->email, 'roommate' => __FUNCTION__]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->count(2)->make(['family_id' => $campers[0]->family_id,
            'roommate' => __FUNCTION__]);
        $cyas[0] = Yearattending::factory()->make(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id,
            'days' => rand(1, 5)]);
        $cyas[1] = Yearattending::factory()->make(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id,
            'days' => rand(1, 5)]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE, ['id' => $campers[0]->id])
                ->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $yas, $changes, $cyas) {
                        $browser->changeCamper([$campers[$i], $yas[$i]], [$changes[$i], $cyas[$i]]);
                    })->assertInputValue(self::ACTIVETAB . ' input[name="days[]"]', $yas[$i]->days)
                    ->clear(self::ACTIVETAB . ' input[name="days[]"]')
                    ->type(self::ACTIVETAB . ' input[name="days[]"]', $cyas[$i]->days);
            }
            $this->submitSuccess($browser, self::WAIT);
        });

        foreach ($changes as $camper) $this->adh($camper);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id,
                'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
    }

    public function testReturningYAUniqueEmailUnder20NoPhone()
    {

        $user = User::factory()->create();

        $camper = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getYABirthdate()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdult]);

        $snowflake = Camper::factory()->create(['roommate' => __FUNCTION__]);
        $changes = Camper::factory()->make(['roommate' => __FUNCTION__, 'birthdate' => $this->getYABirthdate(),
            'phonenbr' => null]);
        $oldemail = $changes->email;
        $changes->email = $snowflake->email;
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdult]);
        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya, $oldemail) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                });
            $this->submitError($browser, self::WAIT);

            $changes->email = $oldemail;
            $browser->type('email[]', $oldemail);
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->adh($changes);
        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $cya->camper_id, 'year_id' => self::$year->id,
            'program_id' => Programname::YoungAdultUnderAge, 'days' => $cya->days]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'amount' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    public function testReturningSeniorUniqueUser()
    {

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email,
            'birthdate' => $this->getChildBirthdate(), 'roommate' => __FUNCTION__]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $snowflake = User::factory()->create();
        $changes = Camper::factory()->make(['birthdate' => $this->getChildBirthdate(), 'roommate' => __FUNCTION__]);
        $oldemail = $changes->email;
        $changes->email = $snowflake->email;
        $cya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya, $oldemail) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB)
                ->within(new CamperInfo, function ($browser) use ($camper, $ya, $changes, $cya) {
                    $browser->changeCamper([$camper, $ya], [$changes, $cya]);
                });
            $this->submitError($browser, self::WAIT);

            $changes->email = $oldemail;
            $browser->type('email[]', $oldemail);
            $this->submitSuccess($browser, self::WAIT);

            $browser->scrollIntoView('@previous')->pause(self::WAIT)->press('@previous')->assertPathIs('/household');
        });

        $this->adh($changes);
        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $cya->camper_id, 'year_id' => self::$year->id,
            'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id,
            'amount' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    public function testReturningFamilyOneKidCantComeClickTwice()
    {
        $user = User::factory()->create();

        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $user->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => $this->getChildBirthdate()]);
        $campers[3] = Camper::factory()->create(['family_id' => $campers[0]->family_id, 'roommate' => __FUNCTION__,
            'birthdate' => $this->getChildBirthdate()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $yas[2] = Yearattending::factory()->create(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $yas[3] = Yearattending::factory()->create(['camper_id' => $campers[3]->id, 'year_id' => self::$lastyear->id]);

        $changes = Camper::factory()->count(4)->make(['roommate' => __FUNCTION__]);
        $cyas[0] = Yearattending::factory()->make(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $cyas[1] = Yearattending::factory()->make(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);
        $cyas[2] = Yearattending::factory()->make(['camper_id' => $campers[2]->id, 'year_id' => self::$year->id]);
        $cyas[3] = Yearattending::factory()->make(['camper_id' => $campers[3]->id, 'year_id' => self::$lastyear->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute(self::ROUTE)->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $yas, $changes, $cyas) {
                        $browser->changeCamper([$campers[$i], $yas[$i]], [$changes[$i], $cyas[$i]]);
                    });
            }
            $this->submitSuccess($browser, self::WAIT);
        });

        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        foreach ($changes as $change) $this->adh($change);
        $this->assertDatabaseHas('users', ['email' => $changes[0]->email]);
        foreach (array_slice($cyas, 0, 3) as $ya) {
            $this->assertDatabaseHas('yearsattending', ['camper_id' => $ya->camper_id,
                'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $cyas[3]->camper_id,
            'year_id' => self::$year->id]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $campers[0]->family_id,
            'amount' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }

    public function testUpdateNonattendingFamilyMultipleUsersAdmin()
    {
        $admin = User::factory()->create(['usertype' => Usertype::Admin]);
        $users = User::factory()->count(4)->create();

        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $users[0]->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[1]->email, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[2]->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getChildBirthdate()]);
        $campers[3] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[3]->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getChildBirthdate()]);

        $changes = Camper::factory()->count(4)->make(['roommate' => __FUNCTION__]);

        $this->browse(function (Browser $browser) use ($admin, $users, $campers, $changes) {
            $browser->loginAs($admin->id)->visitRoute(self::ROUTE, ['id' => $campers[2]->id])
                ->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers, $changes) {
                        $browser->changeCamper([$campers[$i], null], [$changes[$i], null]);
                    })->assertMissing(self::ACTIVETAB . ' input[name="days[]"]');
            }
            $this->submitSuccess($browser, self::WAIT);
        });

        foreach ($users as $user) $this->assertDatabaseMissing('users', ['email' => $user->email]);
        foreach ($changes as $change) {
            $this->adh($change);
            $this->assertDatabaseHas('users', ['email' => $change->email]);
        }
        $this->assertDatabaseMissing('thisyear_charges', ['family_id' => $campers[0]->family_id]);
    }

    public function testNonattendingFamilyRO()
    {
        $pc = User::factory()->create(['usertype' => Usertype::Pc]);
        $users = User::factory()->count(4)->create();

        $campers[0] = Camper::factory()->create(['family_id' => Family::factory()->create(['is_address_current' => 1])->id,
            'email' => $users[0]->email, 'roommate' => __FUNCTION__]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[1]->email, 'roommate' => __FUNCTION__]);
        $campers[2] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[2]->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getChildBirthdate()]);
        $campers[3] = Camper::factory()->create(['family_id' => $campers[0]->family_id,
            'email' => $users[3]->email, 'roommate' => __FUNCTION__, 'birthdate' => $this->getChildBirthdate()]);

        $this->browse(function (Browser $browser) use ($pc, $campers) {
            $browser->loginAs($pc)->visitRoute(self::ROUTE, ['id' => $campers[2]->id])
                ->waitFor(self::ACTIVETAB);
            for ($i = 0; $i < count($campers); $i++) {
                $this->pressTab($browser, $campers[$i]->id, self::WAIT)
                    ->within(new CamperInfo, function ($browser) use ($i, $campers) {
                        $browser->viewCamper($campers[$i], null);
                    })->assertMissing(self::ACTIVETAB . ' input[name="days[]"]');;
            }
            $browser->assertMissing('input[type=submit]');
        });
    }

    private function adh($camper)
    {
        $this->assertDatabaseHas('campers', ['pronoun_id' => $camper->pronoun_id,
            'firstname' => $camper->firstname, 'lastname' => $camper->lastname, 'email' => $camper->email,
            'phonenbr' => isset($camper->phonenbr) ? str_replace('-', '', $camper->phonenbr) : null,
            'birthdate' => $camper->birthdate, 'roommate' => $camper->roommate, 'sponsor' => $camper->sponsor,
            'is_handicap' => $camper->is_handicap, 'foodoption_id' => $camper->foodoption_id,
            'church_id' => $camper->church_id]);
    }

}
