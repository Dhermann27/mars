<?php

namespace Tests\Browser;

use App\Enums\Pctype;
use App\Enums\Programname;
use App\Enums\Usertype;
use App\Jobs\GenerateCharges;
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
    private const WAIT = 400;

    public function testCognoscentiNoStaff()
    {
        $user = User::factory()->create(['usertype' => Usertype::Admin]);
        for ($i = 0; $i < 5; $i++) {
            $position = Staffposition::factory()->create(['pctype' => $i]);
            $camper = Camper::factory()->create(['email' => User::factory()->create()->email]);
            $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
            YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $position->id]);
            $positions[] = $position;
            $campers[] = $camper;
        }
        GenerateCharges::dispatchSync(self::$year->id);

        $this->browse(function (Browser $browser) use ($user, $positions, $campers) {
            $browser->loginAs($user)->visitRoute('tools.cognoscenti')->assertDontSee($positions[0]->name)
                ->assertDontSee($campers[0]->lastname . ', ' . $campers[0]->firstname)
                ->assertDontSee($campers[0]->email);

            for ($i = 1; $i < 5; $i++) {
                $browser->assertSee($positions[$i]->name)->assertSee($campers[$i]->firstname)
                    ->assertSee($campers[$i]->lastname)->assertSee($campers[$i]->email);
            }
        });

        $this->assertDatabaseHas('users', ['email' => $campers[0]->email, 'usertype' => Usertype::Camper]);
        $this->assertDatabaseHas('users', ['email' => $campers[1]->email, 'usertype' => Usertype::Pc]);
        $this->assertDatabaseHas('users', ['email' => $campers[2]->email, 'usertype' => Usertype::Pc]);
        $this->assertDatabaseHas('users', ['email' => $campers[3]->email, 'usertype' => Usertype::Pc]);
        $this->assertDatabaseHas('users', ['email' => $campers[4]->email, 'usertype' => Usertype::Admin]);
    }


    public function testPositions()
    {
        $user = User::factory()->create(['usertype' => Usertype::Pc]);
        $staffposition = Staffposition::factory()->create(['program_id' => Programname::Adult, 'name' => 'Treasurer',
            'pctype' => Pctype::Xc]);
        $camper = Camper::factory()->create(['email' => User::factory()
            ->create(['usertype' => Usertype::Admin])->email]);
        $ya = Yearattending::factory()->create(['camper_id' => $camper->id, 'year_id' => self::$year->id]);
        YearattendingStaff::factory()->create(['yearattending_id' => $ya->id, 'staffposition_id' => $staffposition->id]);
        $newposition = Staffposition::factory()->create(['program_id' => Programname::Burt, 'pctype' => Pctype::Program]);
        $newcamper = Camper::factory()->create(['email' => User::factory()->create()->email]);
        $newya = Yearattending::factory()->create(['camper_id' => $newcamper->id, 'year_id' => self::$year->id]);
        $missingcamper = Camper::factory()->create(['email' => User::factory()->create()->email]);


        $this->browse(function (Browser $browser) use ($user, $staffposition, $camper, $ya, $newposition, $newcamper, $missingcamper) {
            $browser->loginAs($user)->visitRoute('tools.staff.index')
                ->waitFor('form#positions div.tab-content div.active');
            $this->pressTab($browser, Programname::Adult, self::WAIT)->assertSee($staffposition->name)
                ->assertSee($camper->firstname)->assertSee($camper->lastname)
                ->assertSee(number_format($staffposition->compensationlevel->max_compensation, 2))
                ->check('@deleteAdult' . $staffposition->name);
            $this->pressTab($browser, Programname::Burt, self::WAIT)->assertSee('No staff assigned');
            $this->submitSuccess($browser, self::WAIT);
            $this->pressTab($browser, Programname::Adult, self::WAIT)->assertSee('No staff assigned');
            $this->pressTab($browser, Programname::Burt, self::WAIT)
                ->clear('campersearch')->type('campersearch', substr($newcamper->email, 0, -1))
                ->pause(self::WAIT)->keys('#campersearch', '{arrow_down}', '{tab}')
                ->select('staffposition_id', $newposition->id);
            $this->submitSuccess($browser, self::WAIT);
            $this->pressTab($browser, Programname::Burt, self::WAIT)->assertSee($newposition->name)
                ->assertSee($newcamper->firstname)->assertSee($newcamper->lastname)
                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2))
                ->clear('campersearch')->type('campersearch', substr($missingcamper->email, 0, -1))
                ->pause(self::WAIT)->keys('#campersearch', '{arrow_down}', '{tab}')
                ->select('staffposition_id', $newposition->id);
            $this->submitSuccess($browser, self::WAIT);
            $this->pressTab($browser, Programname::Burt, self::WAIT)->assertSee($newposition->name)
                ->assertSee($missingcamper->firstname)->assertSee($missingcamper->lastname)
                ->assertSee(number_format($newposition->compensationlevel->max_compensation, 2));;
        });

        $this->assertDatabaseMissing('yearsattending__staff', ['yearattending_id' => $ya->id,
            'staffposition_id' => $staffposition->id]);
        $this->assertDatabaseHas('users', ['email' => $camper->email, 'usertype' => Usertype::Camper]);
        $this->assertDatabaseHas('yearsattending__staff', ['yearattending_id' => $newya->id,
            'staffposition_id' => $newposition->id]);
        $this->assertDatabaseHas('users', ['email' => $newcamper->email, 'usertype' => Usertype::Pc]);
        $this->assertDatabaseHas('camper__staff', ['camper_id' => $missingcamper->id,
            'staffposition_id' => $newposition->id]);
        $this->assertDatabaseHas('users', ['email' => $missingcamper->email, 'usertype' => Usertype::Pc]);
    }
}
