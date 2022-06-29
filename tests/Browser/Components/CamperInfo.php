<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class CamperInfo extends BaseComponent
{
    const WAIT = 400;

    /**
     * Assert that the browser page contains the component.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return 'form#camperinfo div.tab-content div.active';
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@pronoun' => 'select[name="pronoun_id[]"]',
            '@first' => 'input[name="firstname[]"]',
            '@last' => 'input[name="lastname[]"]',
            '@email' => 'input[name="email[]"]',
            '@phone' => 'input[name="phonenbr[]"]',
            '@bday' => 'input[name="birthdate[]"]',
            '@prog' => 'select[name="program_id[]"]',
            '@room' => 'input[name="roommate[]"]',
            '@spon' => 'input[name="sponsor[]"]',
            '@churchid' => 'input[name="churchid[]"]',
            '@churchname' => 'input[name="churchname[]"]',
            '@ih' => 'input[name="is_handicap[]"]',
            '@food' => 'select[name="foodoption_id[]"]',
        ];
    }

    public function changeCamper(Browser $browser, $from, $to)
    {
        $browser->assertSelected('@pronoun', $from[0]->pronoun_id)->select('@pronoun', $to[0]->pronoun_id)
            ->assertInputValue('@first', $from[0]->firstname)->type('@first', $to[0]->firstname)
            ->assertInputValue('@last', $from[0]->lastname)->type('@last', $to[0]->lastname)
            ->assertInputValue('@email', $from[0]->email)->type('@email', $to[0]->email)
            ->assertInputValue('@phone', $this->formatPhone($from[0]->phonenbr))->type('@phone', $to[0]->phonenbr)
            ->assertInputValue('@bday', $from[0]->birthdate)->clear('@bday')->keys('@bday', $to[0]->birthdate);
        if ($browser->element('@prog')) {
            $browser->assertSelected('@prog', $from[1]->program_id)->select('@prog', $to[1]->program_id);
        }
        $browser->assertInputValue('@room', $from[0]->roommate)->type('@room', $to[0]->roommate)
            ->assertInputValue('@spon', $from[0]->sponsor)->type('@spon', $to[0]->sponsor)
            ->assertInputValue('@churchid', $from[0]->church_id)
            ->clear('@churchname')->type('@churchname', substr($to[0]->church->name, 0, -1))
            ->pause(self::WAIT)->keys('@churchname', '{arrow_down}', '{tab}');
        if ($from[0]->is_handicap == 1) $browser->assertChecked('@ih'); else $browser->assertNotChecked('@ih');
        if ($to[0]->is_handicap == 1) $browser->check('@ih'); else $browser->uncheck('@ih');
        $browser->assertSelected('@food', $from[0]->foodoption_id)->select('@food', $to[0]->foodoption_id);
    }

    public function viewCamper(Browser $browser, $camper, $ya)
    {
        $browser->assertSelected('@pronoun', $camper->pronoun_id)->assertDisabled('@pronoun')
            ->assertInputValue('@first', $camper->firstname)->assertDisabled('@first')
            ->assertInputValue('@last', $camper->lastname)->assertDisabled('@last')
            ->assertInputValue('@email', $camper->email)->assertDisabled('@email')
            ->assertInputValue('@phone', $this->formatPhone($camper->phonenbr))->assertDisabled('@phone')
            ->assertInputValue('@bday', $camper->birthdate)->assertDisabled('@bday')
            ->assertSelected('@prog', $ya->program_id)->assertDisabled('@prog')
            ->assertInputValue('@room', $camper->roommate)->assertDisabled('@room')
            ->assertInputValue('@spon', $camper->sponsor)->assertDisabled('@spon')
            ->assertInputValue('@churchid', $camper->church_id);
        if ($camper->is_handicap == 1) $browser->assertChecked('@ih'); else $browser->assertNotChecked('@ih');
        $browser->assertSelected('@food', $camper->foodoption_id)->assertDisabled('@food');

    }

    private function formatPhone($nbr)
    {
        return substr($nbr, 0, 3) . '-' . substr($nbr, 3, 3) . '-' . substr($nbr, 6);
    }
}
