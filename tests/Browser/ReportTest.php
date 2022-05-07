<?php

namespace Tests\Browser;

use app\Models\Camper;
use app\Models\Charge;
use App\Enums\Chargetypename;
use App\Enums\Timeslotname;
use App\Enums\Usertype;
use App\Jobs\Chartdata;
use app\Models\User;
use app\Models\Workshop;
use app\Models\Yearattending;
use app\Models\YearattendingWorkshop;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use function array_push;
use function factory;
use function number_format;
use function rand;

/**
 * @group Reports
 */
class ReportTest extends DuskTestCase
{
    public function testCampers()
    {
        $faker = Factory::create();

        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        $campers = factory(Camper::class, 4)->create(['lastname' => 'Aaron']);
        foreach ($campers as $camper) {
            Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $camper->id,
                'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]);
        }
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user)->visitRoute('reports.campers')->waitForText('Show')
                ->clickLink(self::$year->year)->pause(250);
            foreach ($campers as $camper) {
                $browser->assertSee($camper->firstname)->assertSee($camper->lastname)->assertSee($camper->email)
                    ->assertSee(Carbon::parse($camper->birthdate)->diff(self::$year->checkin)->format('%y'));
            }
        });
    }

    public function testChart()
    {
        $faker = Factory::create();
        Chartdata::dispatchNow();

        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        $campers = factory(Camper::class, 3)->create();
        Yearattending::factory()->create(['year_id' => self::$lastyear->id, 'camper_id' => $campers[0]->id,
            'created_at' => $faker->dateTimeInInterval(self::$lastyear->year-1 . '-07-22 00:01:00', '+ 1 year')]); // Lost
        Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $campers[1]->id,
            'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]); // New
        Yearattending::factory()->create(['year_id' => self::$years[2]->id, 'camper_id' => $campers[2]->id]); // Old
        Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $campers[2]->id,
            'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]);
        $this->browse(function (Browser $browser) use ($user, $campers) {
            $browser->loginAs($user)->visitRoute('reports.chart')->screenshot('chart')
                ->assertSee(self::$lastyear->year);
        });
    }

    public function testDeposits()
    {
        $faker = Factory::create();
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        $charges = array();
        for ($i = 0; $i < rand(1, 50); $i++) {
            array_push($charges, Charge::factory()->create(['chargetype_id' => Chargetypename::PayPalPayment,
                'deposited_date' => null, 'year_id' => self::$year->id, 'created_at' => $faker->dateTimeThisMonth,
                'amount' => $faker->randomNumber(4) * -1]));
        }
        $donation = Charge::factory()->create(['chargetype_id' => Chargetypename::Donation,
            'year_id' => self::$year->id, 'parent_id' => $charges[0]->id]);
        $addthree = Charge::factory()->create(['chargetype_id' => Chargetypename::PayPalServiceCharge,
            'year_id' => self::$year->id, 'parent_id' => $charges[0]->id]);
        $this->browse(function (Browser $browser) use ($user, $charges, $donation, $addthree) {
            $browser->loginAs($user)->visitRoute('reports.deposits')
                ->waitFor('div.tab-content div.active')->assertSee('Undeposited');
            $browser->assertSee(number_format(abs($charges[0]->amount + $donation->amount + $addthree->amount), 2))
                ->assertSee(number_format(abs($donation->amount), 2))
                ->assertSee(number_format(abs($addthree->amount), 2));
            foreach ($charges as $charge) {
                $browser->assertSee(number_format(abs($charge->amount), 2))->assertSee($charge->timestamp)
                    ->assertSee($charge->memo);
            }
            $browser->click('button[type="submit"]')->acceptDialog()->waitFor('div.alert')
                ->assertVisible('div.alert-success')->assertDontSee('Undeposited');
        });

    }

    public function testWorkshops()
    {
        $faker = Factory::create();
        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        $campers = array();
        $workshop = Workshop::factory()->create(['year_id' => self::$year->id, 'capacity' => rand(1, 98),
            'timeslot_id' => Timeslotname::Sunrise]);
        for ($i = 0; $i < rand($workshop->capacity + 1, 99); $i++) {
            $camper = Camper::factory()->create();
            $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
            $yw = ['yearattending_id' => $ya->id, 'workshop_id' => $workshop->id, 'created_at' => $faker->dateTimeThisMonth];
            if ($i == 0) $yw["is_leader"] = 1;
            YearattendingWorkshop::factory()->create($yw);
        }
        DB::statement('CALL workshops()');

        $this->browse(function (Browser $browser) use ($user, $campers, $workshop) {
            $browser->loginAs($user)->visitRoute('reports.workshops')->waitForText($workshop->name)
                ->assertSee('Leader');
            foreach ($campers as $camper) {
                $browser->assertSee($camper->firstname)->assertSee($camper->lastname);
            }
            $browser->assertPresent('tr.table-danger');
        });
    }
}
