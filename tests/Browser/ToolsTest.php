<?php

namespace Tests\Browser;

use App\Enums\Usertype;
use App\Models\Camper;
use App\Models\Staffposition;
use App\Models\User;
use App\Models\Yearattending;
use App\Models\YearattendingStaff;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * @group Tools
 */
class ToolsTest extends DuskTestCase
{

    public function testCognoscentiNoStaff()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        for ($i = 0; $i < 4; $i++) {
            $position = Staffposition::factory()->create(['pctype' => $i]);
            $camper = Camper::factory()->create();
            $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
            YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $position->id]);
            $positions[] = $position;
            $campers[] = $camper;
        }

        $this->browse(function (Browser $browser) use ($user, $positions, $campers) {
            $browser->loginAs($user)->visitRoute('tools.cognoscenti')->assertDontSee($positions[0]->name)
                ->assertDontSee($campers[0]->lastname . ', ' . $campers[0]->firstname)
                ->assertDontSee($campers[0]->email);

            for ($i = 1; $i < 4; $i++) {
                $browser->assertSee($positions[$i]->name)->assertSee($campers[$i]->firstname)
                    ->assertSee($campers[$i]->lastname)->assertSee($campers[$i]->email);
            }
        });
    }


//    public function testPositions()
//    {
//        $user = User::factory()->create(['usertype' => Usertype::Admin]);
//        $staffposition = Staffposition::factory()->create(['program_id' => Programname::Adult]);
//        $camper = Camper::factory()->create();
//        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
//        YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $staffposition->id]);
//        $newposition = Staffposition::factory()->create(['program_id' => Programname::Burt]);
//        $newcamper = Camper::factory()->create();
//        $newya = Yearattending::factory()->create(['camper_id' => $newcamper->id, 'year_id' => self::$year->id]);
//        $missingcamper = Camper::factory()->create();
//
//
//        $this->browse(function (Browser $browser) use ($user, $staffposition, $camper, $ya, $newposition, $newcamper, $missingcamper) {
//            $browser->loginAs($user)->visitRoute('tools.staff.index')
//                ->waitFor('form#positions div.tab-content div.active')->clickLink('Adult')
//                ->pause(self::WAIT)->assertSee($staffposition->name)
//                ->assertSee($camper->firstname)->assertSee($camper->lastname)
//                ->assertSee(number_format($staffposition->compensationlevel->max_compensation, 2))
//                ->check('delete-' . $ya->id . '-' . $staffposition->id)->clickLink('Burt')
//                ->pause(self::WAIT)->assertSee('No staff assigned')->click('button[type="submit"]')
//                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Adult')
//                ->pause(self::WAIT)->assertSee('No staff assigned')->clickLink('Burt')
//                ->pause(self::WAIT)->click('select#camper_id + span.select2')
//                ->waitFor('.select2-container--open')
//                ->type('span.select2-container input.select2-search__field', $newcamper->email)
//                ->waitFor('li.select2-results__option--highlighted')
//                ->click('li.select2-results__option--highlighted')
//                ->select('staffposition_id', $newposition->id)->click('button[type="submit"]')
//                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Burt')
//                ->pause(self::WAIT)->assertSee($newposition->name)->assertSee($newcamper->firstname)
//                ->assertSee($newcamper->lastname)
//                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2))
//                ->click('select#camper_id + span.select2')->waitFor('.select2-container--open')
//                ->type('span.select2-container input.select2-search__field', $missingcamper->email)
//                ->waitFor('li.select2-results__option--highlighted')
//                ->click('li.select2-results__option--highlighted')
//                ->select('staffposition_id', $newposition->id)->click('button[type="submit"]')
//                ->waitFor('div.alert')->assertVisible('div.alert-success')->clickLink('Burt')
//                ->pause(self::WAIT)->assertSee($newposition->name)->assertSee($missingcamper->firstname)
//                ->assertSee($missingcamper->lastname)
//                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2));;
//        });
//
//        $this->assertDatabaseMissing('yearsattending__staff', ['yearattending_id' => $ya->id,
//            'staffposition_id' => $staffposition->id]);
//        $this->assertDatabaseHas('yearsattending__staff', ['yearattending_id' => $newya->id,
//            'staffposition_id' => $newposition->id]);
//        $this->assertDatabaseHas('camper__staff', ['camper_id' => $missingcamper->id,
//            'staffposition_id' => $newposition->id]);
//    }
}
