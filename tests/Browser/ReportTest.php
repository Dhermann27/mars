<?php

namespace Tests\Browser;

use App\Enums\Chargetypename;
use App\Enums\Timeslotname;
use App\Enums\Usertype;
use App\Models\Camper;
use App\Models\Charge;
use App\Models\User;
use App\Models\Workshop;
use App\Models\Yearattending;
use App\Models\YearattendingWorkshop;
use Carbon\Carbon;
use Faker\Factory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use function rand;

/**
 * @group Reports
 */
class ReportTest extends DuskTestCase
{
//    public function testCampers()
//    {
//        $faker = Factory::create();
//
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//        $campers = Camper::factory()->count(4)->create(['lastname' => 'Aaron']);
//        foreach ($campers as $camper) {
//            Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $camper->id,
//                'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]);
//        }
//        $this->browse(function (Browser $browser) use ($user, $campers) {
//            $browser->loginAs($user)->visitRoute('reports.campers')->waitForText('Show')
//                ->clickLink(self::$year->year)->pause(self::WAIT);
//            foreach ($campers as $camper) {
//                $browser->assertSee($camper->firstname)->assertSee($camper->lastname)->assertSee($camper->email)
//                    ->assertSee(Carbon::parse($camper->birthdate)->diff(self::$year->checkin)->format('%y'));
//            }
//        });
//    }
//
//    public function testChart()
//    {
//        $faker = Factory::create();
//        Chartdata::dispatchNow();
//
//        $user = User::factory()->create(['usertype' => Usertype::Pc]);
//        $campers = Camper::factory()->count(3)->create();
//        Yearattending::factory()->create(['year_id' => self::$lastyear->id, 'camper_id' => $campers[0]->id,
//            'created_at' => $faker->dateTimeInInterval(self::$lastyear->year-1 . '-07-22 00:01:00', '+ 1 year')]); // Lost
//        Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $campers[1]->id,
//            'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]); // New
//        Yearattending::factory()->create(['year_id' => self::$years[2]->id, 'camper_id' => $campers[2]->id]); // Old
//        Yearattending::factory()->create(['year_id' => self::$year->id, 'camper_id' => $campers[2]->id,
//            'created_at' => $faker->dateTimeInInterval(self::$year->year-1 . '-07-22 00:01:00', '+ 1 year')]);
//        $this->browse(function (Browser $browser) use ($user, $campers) {
//            $browser->loginAs($user)->visitRoute('reports.chart')->screenshot('chart')
//                ->assertSee(self::$lastyear->year);
//        });
//    }
//
    public function testDeposits()
    {
        $faker = Factory::create();
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        $charges = Charge::factory()->count(rand(3, 50))->create(['chargetype_id' => Chargetypename::PayPalPayment,
            'deposited_date' => null, 'year_id' => self::$year->id, 'created_at' => $faker->dateTimeThisMonth,
            'amount' => $faker->randomNumber(4) * -1]);
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
            $chunks = $charges->chunk(rand(1, count($charges) - 2));
            foreach ($chunks[0] as $mark) {
                $browser->scrollIntoView('@mark' . $mark->id)->pause(250)
                    ->check('@mark' . $mark->id);
            }
            $this->submitSuccess($browser, 200, 'Mark as Deposited');
            $browser->assertSee('Undeposited');
            foreach ($chunks[0] as $mark) {
                $browser->assertDontSee($mark->memo);
                $this->assertDatabaseHas('charges', ['id' => $mark->id,
                    'deposited_date' => Carbon::now()->toDateString()]);
            }

            $browser->pause(200)->scrollIntoView('button[type=submit]')->pause(200)
                ->press('Mark as Deposited')->acceptDialog()->waitUntilMissing('div.alert-danger')
                ->waitFor('div.alert')->assertVisible('div.alert-success');
            $browser->assertDontSee('Undeposited');

            foreach ($chunks[1] as $charge) {
                $this->assertDatabaseHas('charges', ['id' => $charge->id,
                    'deposited_date' => Carbon::now()->toDateString()]);
            }
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
