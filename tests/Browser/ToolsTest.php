<?php

namespace Tests\Browser;

use app\Models\Camper;
use App\Enums\Programname;
use App\Enums\Usertype;
use app\Models\Staffposition;
use app\Models\User;
use app\Models\Yearattending;
use app\Models\YearattendingStaff;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use function array_push;
use function factory;
use function number_format;

/**
 * @group Tools
 */
class ToolsTest extends DuskTestCase
{

    public function testCognoscenti()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        $positions = array();
        $campers = array();
        for ($i = 0; $i < 5; $i++) {
            $position = Staffposition::factory()->create(['pctype' => $i]);
            $camper = Camper::factory()->create();
            $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
            YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $position->id]);
            array_push($positions, $position);
            array_push($campers, $camper);
        }

        $this->browse(function (Browser $browser) use ($user, $positions, $campers) {
            $browser->loginAs($user)->visitRoute('tools.cognoscenti')->assertDontSee($positions[0]->name)
                ->assertDontSee($campers[0]->lastname . ', ' . $campers[0]->firstname)
                ->assertDontSee($campers[0]->email);

            for ($i = 1; $i < 5; $i++) {
                $browser->assertSee($positions[$i]->name)->assertSee($campers[$i]->firstname)
                    ->assertSee($campers[$i]->lastname)->assertSee($campers[$i]->email);
            }
        });
    }


    public function testPositions()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        $staffposition = Staffposition::factory()->create(['program_id' => Programname::Adult]);
        $camper = Camper::factory()->create();
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $staffposition->id]);
        $newposition = Staffposition::factory()->create(['program_id' => Programname::Burt]);
        $newcamper = Camper::factory()->create();
        $newya = Yearattending::factory()->create(['camper_id' => $newcamper->id, 'year_id' => self::$year->id]);
        $missingcamper = Camper::factory()->create();


        $this->browse(function (Browser $browser) use ($user, $staffposition, $camper, $ya, $newposition, $newcamper, $missingcamper) {
            $browser->loginAs($user)->visitRoute('tools.staff.index')
                ->waitFor('form#positions div.tab-content div.active')->clickLink('Adult')
                ->pause(250)->assertSee($staffposition->name)
                ->assertSee($camper->firstname)->assertSee($camper->lastname)
                ->assertSee(number_format($staffposition->compensationlevel->max_compensation, 2))
                ->check('delete-' . $ya->id . '-' . $staffposition->id)->clickLink('Burt')
                ->pause(250)->assertSee('No staff assigned')->click('button[type="submit"]')
                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Adult')
                ->pause(250)->assertSee('No staff assigned')->clickLink('Burt')
                ->pause(250)->click('select#camper_id + span.select2')
                ->waitFor('.select2-container--open')
                ->type('span.select2-container input.select2-search__field', $newcamper->email)
                ->waitFor('li.select2-results__option--highlighted')
                ->click('li.select2-results__option--highlighted')
                ->select('staffposition_id', $newposition->id)->click('button[type="submit"]')
                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Burt')
                ->pause(250)->assertSee($newposition->name)->assertSee($newcamper->firstname)
                ->assertSee($newcamper->lastname)
                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2))
                ->click('select#camper_id + span.select2')->waitFor('.select2-container--open')
                ->type('span.select2-container input.select2-search__field', $missingcamper->email)
                ->waitFor('li.select2-results__option--highlighted')
                ->click('li.select2-results__option--highlighted')
                ->select('staffposition_id', $newposition->id)->click('button[type="submit"]')
                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Burt')
                ->pause(250)->assertSee($newposition->name)->assertSee($missingcamper->firstname)
                ->assertSee($missingcamper->lastname)
                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2));;
        });

        $this->assertDatabaseMissing('yearsattending__staff', ['yearattending_id' => $ya->id,
            'staffposition_id' => $staffposition->id]);
        $this->assertDatabaseHas('yearsattending__staff', ['yearattending_id' => $newya->id,
            'staffposition_id' => $newposition->id]);
        $this->assertDatabaseHas('camper__staff', ['camper_id' => $missingcamper->id,
            'staffposition_id' => $newposition->id]);
    }
}
