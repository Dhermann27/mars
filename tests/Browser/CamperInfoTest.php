<?php

namespace Tests\Browser;

use app\Models\Camper;
use App\Enums\Chargetypename;
use App\Enums\Usertype;
use app\Models\Family;
use app\Models\User;
use app\Models\Year;
use app\Models\Yearattending;
use Carbon\Carbon;
use Facebook\WebDriver\Exception\TimeOutException;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\CamperForm;
use Tests\DuskTestCase;
use Throwable;
use function count;
use function factory;
use function rand;
use function str_replace;

/**
 * @group Campers
 */
class CamperInfoTest extends DuskTestCase
{

    /**
     * @group Abraham
     * @throws Throwable
     */
    public function testAbraham()
    {

        $user = User::factory()->create();
        $camper = Camper::factory()->make(['family_id' => null, 'firstname' => 'Abraham', 'email' => $user->email]);
        $ya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->createCamper($browser, $camper, $ya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($camper);
        $camper = Camper::latest()->first();
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);

        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'firstname' => 'Abraham']);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    /**
     * @group Beto
     * @throws Throwable
     */
    public function testBeto()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Beto',  'email' => $user->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'firstname' => 'Beto']);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });
        $camper = Camper::latest()->first();

        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 200, 'chargetype_id' => Chargetypename::Deposit]);

    }

    /**
     * @group Charlie
     * @throws Throwable
     */
    public function testCharlie()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Charlie', 'email' => $cuser->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['family_id' => $camper->family_id, 'firstname' => 'Charlie']);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('users', ['email' => $changes->email]);
        $this->adh($changes);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
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
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
                $browser->viewCamper($camper, $ya);
            })->assertMissing('button[type="submit"]');
        });


    }

    /**
     * @group Deb
     * @throws Throwable
     */
    public function testDebDistinct()
    {
        $user = User::factory()->create();

        $campers = factory(Camper::class, 2)->make(['family_id' => null, 'email' => $user->email]);
        $campers[0]->firstname = "Deb";

        $yas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->createCamper($browser, $campers[0], $yas[0]);
            $browser->script('window.scrollTo(0,0)');
            $browser->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $campers[1], $yas[1]);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
            $campers[1]->email = 'deb@email.org';
            $browser->script('window.scrollTo(0,0)');
            $browser->pause(250)->clickLink($campers[1]->firstname)->pause(250)
                ->type('form#camperinfo div.tab-content div.active input[name="email[]"]', $campers[1]->email);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        foreach ($campers as $camper) $this->adh($camper);
        $camper = Camper::orderBy('id', 'desc')->first();
        foreach ($yas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }

        $changes = factory(Camper::class, 2)->make(['family_id' => $camper->family_id]);
        $changes[0]->firstname = "Deb";
        $cyas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            for ($i = 0; $i < count($campers); $i++) {
                $browser->script('window.scrollTo(0,0)');
                $browser->pause(250)->clickLink($campers[$i]->firstname)->pause(250);
                $this->changeCamper($browser, $campers[$i], $yas[$i], $changes[$i], $cyas[$i]);
            }
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        foreach ($changes as $change) $this->adh($change);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }

    /**
     * @group Evra
     * @throws Throwable
     */
    public function testEvraDistinct()
    {
        $user = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Evra', 'email' => $user->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $changes = factory(Camper::class, 2)->make(['family_id' => $campers[0]->family_id]);
        $changes[0]->firstname = "Evra";
        $changes[1]->email = $changes[0]->email;
        $cyas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            for ($i = 0; $i < count($campers); $i++) {
                $browser->script('window.scrollTo(0,0)');
                $browser->pause(250)->clickLink($campers[$i]->firstname)->pause(250);
                $this->changeCamper($browser, $campers[$i], $yas[$i], $changes[$i], $cyas[$i]);
            }
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
            $changes[1]->email = 'evra@email.org';
            $browser->script('window.scrollTo(0,0)');
            $browser->pause(250)->clickLink($changes[1]->firstname)->pause(250)
                ->type('form#camperinfo div.tab-content div.active input[name="email[]"]', $changes[1]->email);
            $browser->pause(250)->click('button[type="submit"]')->acceptDialog()
                ->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        foreach ($changes as $camper) $this->adh($camper);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
        $camper = Camper::orderBy('id', 'desc')->first();
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }

    /**
     * @group Franklin
     * @throws Throwable
     */
    public function testFranklinDistinct()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $changes = factory(Camper::class, 2)->make(['family_id' => $campers[0]->family_id]);
        $changes[0]->firstname = "Franklin";
        $changes[1]->email = $changes[0]->email;
        $cyas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $campers[0]->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            for ($i = 0; $i < count($campers); $i++) {
                $browser->script('window.scrollTo(0,0)');
                $browser->pause(250)->clickLink($campers[$i]->firstname)->pause(250);
                $this->changeCamper($browser, $campers[$i], $yas[$i], $changes[$i], $cyas[$i]);
            }
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
            $changes[1]->email = 'franklin@email.org';
            $browser->script('window.scrollTo(0,0)');
            $browser->pause(250)->clickLink($changes[1]->firstname)->pause(250)
                ->type('form#camperinfo div.tab-content div.active input[name="email[]"]', $changes[1]->email);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        foreach ($changes as $camper) $this->adh($camper);
        foreach ($cyas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }
    }

    /**
     * @group Franklin
     * @throws Throwable
     */
    public function testFranklinRO()
    {

        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Franklin', 'email' => $cuser->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $campers[0]->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            for ($i = 0; $i < count($campers); $i++) {
                $browser->script('window.scrollTo(0,0)');
                $browser->pause(250)->clickLink($campers[$i]->firstname)->pause(250);
                $browser->within(new CamperForm, function (Browser $browser) use ($i, $campers, $yas) {
                    $browser->viewCamper($campers[$i], $yas[$i]);
                });
            }
            $browser->assertMissing('button[type="submit"]');
        });
    }


    /**
     * @group Geoff
     * @throws Throwable
     */
    public function testGeoffUniqueCamper()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create();

        $camper = Camper::factory()->make(['firstname' => 'Geoff', 'family_id' => null,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString(), 'email' => $user->email]);
        $ya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->createCamper($browser, $camper, $ya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($camper);
        $camper = Camper::latest()->first();
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);

        $snowflake = Camper::factory()->create();
        $changes = Camper::factory()->make(['firstname' => 'Geoff', 'birthdate' => $birth->addDays(rand(0, 364))->toDateString(), 'email' => $snowflake->email]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
        });

    }

    /**
     * @group Henrietta
     * @throws Throwable
     */
    public function testHenriettaUniqueUser()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Henrietta', 'email' => $user->email,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $snowflake = Camper::factory()->create();
        $changes = Camper::factory()->make(['firstname' => 'Henrietta', 'family_id' => $camper->family_id,
            'email' => $snowflake->email, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
        });

    }

    /**
     * @group Ingrid
     * @throws Throwable
     */
    public function testIngridUniqueCamper()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'email' => $cuser->email,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $snowflake = Camper::factory()->create();
        $changes = Camper::factory()->make(['firstname' => 'Ingrid', 'email' => $snowflake->email,
            'family_id' => $camper->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->changeCamper($browser, $camper, $ya, $changes, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-danger')->assertPresent('span.invalid-feedback');
        });
    }

    /**
     * @group Ingrid
     * @throws Throwable
     */
    public function testIngridRO()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - 20;

        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $cuser = User::factory()->create();
        $family = Family::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Ingrid', 'family_id' => $family->id, 'email' => $cuser->email, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active');
            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
                $browser->viewCamper($camper, $ya);
            })->assertMissing('button[type="submit"]');
        });

    }


    /**
     * @group Juliet
     * @throws Throwable
     */
    public function testJulietClickTwice()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - rand(1, 17);

        $user = User::factory()->create();

        $camper = Camper::factory()->make(['firstname' => 'Juliet', 'sponsor' => 'Geoff Jefferson', 'family_id' => null,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString(), 'email' => $user->email]);
        $ya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->createCamper($browser, $camper, $ya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success')->click('button[type="submit"]')
                ->acceptDialog()->waitFor('div.alert')->assertVisible('div.alert-success');
        });

        $this->adh($camper);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);

    }

    /**
     * @group Knopf
     * @throws Throwable
     */
    public function testKnopfAdultNotComing()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - rand(1, 17);

        $user = User::factory()->create();
        $adult = Camper::factory()->create(['email' => $user->email]);
        $camper = Camper::factory()->create(['firstname' => 'Knopf', 'sponsor' => 'Henrietta Hendricks',
            'family_id' => $adult->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $adult->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['firstname' => 'Knopf', 'family_id' => $camper->family_id,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $adult, $camper, $yas, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->clickLink($camper->firstname)->pause(250);
            $this->changeCamper($browser, $camper, $yas[1], $changes, $cya);
            $browser->clickLink($adult->firstname)->pause(250)
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', 0)
                ->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->assertDatabaseHas('users', ['email' => $adult->email]);
        $this->adh($adult);
        $this->adh($changes);
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $adult->id, 'year_id' => self::$year->id]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);;

    }

    /**
     * @group Lucy
     * @throws Throwable
     */
    public function testLucyAdultNotComing()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - rand(1, 17);

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $adult = Camper::factory()->create(['email' => $user->email]);
        $camper = Camper::factory()->create(['firstname' => 'Lucy', 'sponsor' => 'Ingrid Illia',
            'family_id' => $adult->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $adult->id, 'year_id' => self::$year->id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $changes = Camper::factory()->make(['firstname' => 'Lucy', 'family_id' => $camper->family_id,
            'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $adult, $camper, $yas, $changes, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->clickLink($camper->firstname)->pause(250);
            $this->changeCamper($browser, $camper, $yas[1], $changes, $cya);
            $browser->clickLink($adult->firstname)->pause(250)
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', 0)
                ->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($adult);
        $this->adh($changes);
        $this->assertDatabaseMissing('yearsattending', ['camper_id' => $adult->id, 'year_id' => self::$year->id]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $camper->id, 'year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);;
    }

    /**
     * @group Lucy
     * @throws Throwable
     */
    public function testLucyRO()
    {
        $birth = Carbon::now();
        $birth->year = self::$year->year - rand(1, 17);

        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $adult = Camper::factory()->create(['email' => $user->email]);
        $camper = Camper::factory()->create(['firstname' => 'Lucy', 'sponsor' => 'Ingrid Illia',
            'family_id' => $adult->family_id, 'birthdate' => $birth->addDays(rand(0, 364))->toDateString()]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $adult, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $camper->id])
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->clickLink($camper->firstname)->pause(250);
            $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
                $browser->viewCamper($camper, $ya);
            })->assertMissing('button[type="submit"]');
            $browser->clickLink($adult->firstname)->pause(250)
                ->assertSelected('form#camperinfo div.tab-content div.active select[name="days[]"]', 0);
        });

    }

    /**
     * @group Matthew
     * @throws Throwable
     */
    public function testMatthewAddLater()
    {
        $user = User::factory()->create();

        $campers = factory(Camper::class, 2)->make(['family_id' => null]);
        $campers[0]->firstname = "Matthew";
        $campers[0]->email = $user->email;

        $yas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $yas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active');
            $this->createCamper($browser, $campers[0], $yas[0]);
            $browser->script('window.scrollTo(0,0)');
            $browser->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $campers[1], $yas[1]);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        foreach ($campers as $camper) $this->adh($camper);
        $camper = Camper::latest()->first();
        foreach ($yas as $ya) {
            $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        }

        $change = Camper::factory()->make(['family_id' => $camper->family_id]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $change, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $change, $cya);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($change);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
    }

    /**
     * @group Nancy
     * @throws Throwable
     */
    public function testNancyAddLater()
    {
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['firstname' => 'Nancy', 'email' => $user->email]);
        $ya = Yearattending::factory()->make(['camper_id' => $camper->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $camper, $ya) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', $ya->days)
                ->select('form#camperinfo div.tab-content div.active select[name="program_id[]"]', $ya->program_id)
                ->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($camper);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $ya->program_id, 'days' => $ya->days]);
        $this->assertDatabaseHas('gencharges', ['camper_id' => $camper->id, 'charge' => 200, 'chargetype_id' => Chargetypename::Deposit]);

        $changes = factory(Camper::class, 2)->make(['family_id' => $camper->family_id]);
        $cyas = factory(Yearattending::class, 2)->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $changes, $cyas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $changes[0], $cyas[0]);
            $browser->script('window.scrollTo(0,0)');
            $browser->pause(250)->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $changes[1], $cyas[1]);
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($changes[0]);
        $this->adh($changes[1]);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cyas[0]->program_id, 'days' => $cyas[0]->days]);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cyas[1]->program_id, 'days' => $cyas[1]->days]);
        $this->assertDatabaseHas('thisyear_charges', ['family_id' => $camper->family_id, 'amount' => 400, 'chargetype_id' => Chargetypename::Deposit]);
    }

    /**
     * @group Oscar
     * @throws Throwable
     */
    public function testOscarAddCamper()
    {

        $user = User::factory()->create(['usertype' => Usertype::Admin]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Oscar', 'email' => $cuser->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $change = Camper::factory()->make(['family_id' => $campers[0]->family_id]);
        $cya = Yearattending::factory()->make(['year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers, $change, $cya) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $campers[0]->id])
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->click('a#newcamper')->pause(250);
            $this->createCamper($browser, $change, $cya);;
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($change);
        $this->assertDatabaseHas('yearsattending', ['year_id' => self::$year->id, 'program_id' => $cya->program_id, 'days' => $cya->days]);
    }

    /**
     * @group Oscar
     * @throws Throwable
     */
    public function testOscarRO()
    {

        $user = User::factory()->create(['usertype' => Usertype::Pc]);

        $cuser = User::factory()->create();
        $campers[0] = Camper::factory()->create(['firstname' => 'Oscar', 'email' => $cuser->email]);
        $yas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$year->id]);
        $campers[1] = Camper::factory()->create(['family_id' => $campers[0]->family_id]);
        $yas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$year->id]);

        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user->id)->visitRoute('campers.index', ['id' => $campers[0]->id])
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->assertMissing('a#newcamper')
                ->assertMissing('button[type="submit"]');
        });
    }

    /**
     * @group Quentin
     * @throws Throwable
     */
    public function testQuentinLastProgramId()
    {
        $user = User::factory()->create();
        $head = Camper::factory()->create(['firstname' => 'Quentin', 'email' => $user->email]);
        $campers = factory(Camper::class, 2)->create(['family_id' => $head->family_id]);
        $lyah = Yearattending::factory()->create(['camper_id' => $head->id, 'year_id' => self::$lastyear->id]);
        $lyas[0] = Yearattending::factory()->create(['camper_id' => $campers[0]->id, 'year_id' => self::$lastyear->id]);
        $lyas[1] = Yearattending::factory()->create(['camper_id' => $campers[1]->id, 'year_id' => self::$lastyear->id]);


        $this->browse(function (Browser $browser) use ($user, $head, $campers, $lyah, $lyas) {
            $browser->loginAs($user->id)->visitRoute('campers.index')
                ->waitFor('form#camperinfo div.tab-content div.active')
                ->clickLink($head->firstname)->pause(250)
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', $lyah->days)
                ->assertSelected('form#camperinfo div.tab-content div.active select[name="program_id[]"]', $lyah->program_id)
                ->clickLink($campers[0]->firstname)->pause(250)
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', $lyas[0]->days)
                ->assertSelected('form#camperinfo div.tab-content div.active select[name="program_id[]"]', $lyas[0]->program_id)
                ->clickLink($campers[1]->firstname)->pause(250)
                ->select('form#camperinfo div.tab-content div.active select[name="days[]"]', $lyas[1]->days)
                ->assertSelected('form#camperinfo div.tab-content div.active select[name="program_id[]"]', $lyas[1]->program_id)
                ->select('form#camperinfo div.tab-content div.active select[name="program_id[]"]', $lyas[0]->program_id)
                ->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success');
        });

        $this->adh($head);
        $this->adh($campers[0]);
        $this->adh($campers[1]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $head->id, 'year_id' => self::$year->id, 'program_id' => $lyah->program_id, 'days' => $lyah->days]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[0]->id, 'year_id' => self::$year->id, 'program_id' => $lyas[0]->program_id, 'days' => $lyas[0]->days]);
        $this->assertDatabaseHas('yearsattending', ['camper_id' => $campers[1]->id, 'year_id' => self::$year->id, 'program_id' => $lyas[0]->program_id, 'days' => $lyas[1]->days]);

    }

    /**
     * @throws TimeOutException
     */
    private function createCamper(Browser $browser, $camper, $ya)
    {
        $browser->within(new CamperForm, function ($browser) use ($camper, $ya) {
            $browser->createCamper($camper, $ya);
        })->waitFor('.select2-container--open')
            ->type('span.select2-container input.select2-search__field', $camper->church->name)
            ->waitFor('li.select2-results__option--highlighted')->click('li.select2-results__option--highlighted');
    }

    private function adh($camper)
    {
        $this->assertDatabaseHas('campers', ['pronoun_id' => $camper->pronoun_id,
            'firstname' => $camper->firstname, 'lastname' => $camper->lastname, 'email' => $camper->email,
            'phonenbr' => str_replace('-', '', $camper->phonenbr), 'birthdate' => $camper->birthdate,
            'roommate' => $camper->roommate, 'sponsor' => $camper->sponsor, 'is_handicap' => $camper->is_handicap,
            'foodoption_id' => $camper->foodoption_id, 'church_id' => $camper->church_id]);
    }

    /**
     * @throws TimeOutException
     */
    private function changeCamper(Browser $browser, $camper, $ya, $changes, $cya)
    {
        $browser->within(new CamperForm, function ($browser) use ($camper, $ya, $changes, $cya) {
            $browser->changeCamper([$camper, $ya], [$changes, $cya]);
        })->waitFor('.select2-container--open')
            ->type('span.select2-container input.select2-search__field', $changes->church->name)
            ->waitFor('li.select2-results__option--highlighted')->click('li.select2-results__option--highlighted');
    }


}
