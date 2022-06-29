<?php

namespace Tests\Browser;

use App\Enums\Buildingtype;
use App\Models\Building;
use App\Models\Program;
use App\Models\Rate;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @group Home
 * @group Welcome
 */
class WelcomeTest extends DuskTestCase
{

    public function testWelcome()
    {
        $firstday = Carbon::parse('first Sunday of July ' . self::$year->year); // TODO: Replace with regexp
        $this->browse(function (Browser $browser) use ($firstday) {
            $browser->visit('/')
                ->assertSee('Midwest Unitarian Universalist Summer Assembly')
//                ->assertSee('Register for ' . self::$year->year) WHY???
                ->assertSee('Sunday ' . $firstday->format('F jS') .
                    ' through Saturday July ' . $firstday->addDays(6)->format('jS') . ' ' . self::$year->year);
        });
    }

    public function testHousing()
    {
        $building = Building::factory()->create();

        $this->browse(function (Browser $browser) use ($building) {
            $browser->visit('/housing')
                ->assertSee($building->name);
        });
    }

    public function testPrograms()
    {
        $program = Program::factory()->create();

        $this->browse(function (Browser $browser) use ($program) {
            $browser->visit('/programs')
                ->assertSee($program->name);
        });
    }

    public function testScholarship()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/scholarship')
                ->assertSee('financial assistance');
        });
    }

    public function testThemeSpeaker()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/themespeaker')
                ->assertSee('Rev');
        });
    }

    public function testCampCost()
    {
        $program = Program::factory()->create();
        $lodgerates = array();
        $lakewoodrates = array();
        $tentrates = array();

        for ($i = 1; $i < 8; $i++) {
            $lodgerates[] = Rate::factory()->create(
                ['building_id' => Buildingtype::Trout, 'program_id' => $program->id, 'min_occupancy' => $i, 'max_occupancy' => $i]);
            $tentrates[] = Rate::factory()->create(
                ['building_id' => Buildingtype::Tent, 'program_id' => $program->id, 'min_occupancy' => $i, 'max_occupancy' => $i]);
            $lakewoodrates[] = Rate::factory()->create(
                ['building_id' => Buildingtype::LakewoodCabin, 'program_id' => $program->id, 'min_occupancy' => $i, 'max_occupancy' => $i]);
        }

        $this->browse(function (Browser $browser) use ($lodgerates, $lakewoodrates, $tentrates) {
            $browser->visit('/cost')->assertSee('actual fees may vary');

            $browser->click('@adultsup')
                ->assertSee('half the amount shown')
                ->assertSeeIn('span#deposit', 200.00);

            for ($i = 1; $i < 6; $i++) {
                $browser->assertSeeIn('div#adults-fee', $this->moneyFormat($lodgerates[0 + min($i - 1, 3)]->rate * 6 * $i));

                $browser->click('label[for=adults-housing2]')
                    ->assertSeeIn('div#adults-fee', $this->moneyFormat($lakewoodrates[0]->rate * 6 * $i));

                $browser->click('label[for=adults-housing3]')
                    ->assertSeeIn('div#adults-fee', $this->moneyFormat($tentrates[0]->rate * 6 * $i));

                $browser->click('@adultsup')->click('label[for=adults-housing1]');
            }

            for ($i = 1; $i < 6; $i++) {
                $browser->click('@childrenup')->click('label[for=adults-housing1]')
                    ->assertSeeIn('div#children-fee', $this->moneyFormat($lodgerates[4]->rate * 6 * $i));

                $browser->click('label[for=adults-housing2]')
                    ->assertSeeIn('div#children-fee', $this->moneyFormat($lakewoodrates[2]->rate * 6 * $i));

                $browser->click('label[for=adults-housing3]')
                    ->assertSeeIn('div#children-fee', $this->moneyFormat($tentrates[2]->rate * 6 * $i));

            }

            $browser->click('@yasup');
            for ($i = 1; $i < 6; $i++) {
                $browser->click('label[for=yas-housing2]')
                    ->assertSeeIn('div#yas-fee', $this->moneyFormat($lakewoodrates[6]->rate * 6 * $i));

                $browser->click('label[for=yas-housing3]')
                    ->assertSeeIn('div#yas-fee', $this->moneyFormat($tentrates[6]->rate * 6 * $i));

                $browser->click('@yasup');
            }

            for ($i = 1; $i < 6; $i++) {
                $browser->click('@jrsrsup')
                    ->assertSeeIn('div#jrsrs-fee', $this->moneyFormat($lakewoodrates[1]->rate * 6 * $i));
            }

            for ($i = 1; $i < 6; $i++) {
                $browser->click('@babiesup')
                    ->assertSeeIn('div#babies-fee', $this->moneyFormat($lodgerates[6]->rate * 6 * $i));
            }

            $browser->assertSeeIn('span#deposit', 400.00);

        });
    }

}
