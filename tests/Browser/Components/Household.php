<?php

namespace Tests\Browser\Components;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Household extends BaseComponent
{
    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function selector()
    {
        return 'form#household';
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@adr1' => 'input[name="address1"]',
            '@adr2' => 'input[name="address2"]',
            '@city' => 'input[name="city"]',
            '@state' => 'select[name="province_id"]',
            '@zip' => 'input[name="zipcd"]',
            '@country' => 'input[name="country"]',
            '@iac' => 'input[type="checkbox"][name="is_address_current"]',
            '@ie' => 'input[type="checkbox"][name="is_ecomm"]',
            '@is' => 'input[type="checkbox"][name="is_scholar"]'
        ];
    }

    /**
     * @throws TimeOutException
     */
    public function createHousehold(Browser $browser, $hh)
    {
        $browser->type('@adr1', $hh->address1)
            ->type('@adr2', $hh->address2)
            ->type('@city', $hh->city)
            ->select('@state', $hh->province_id)
            ->type('@zip', $hh->zipcd)
            ->type('@country', $hh->country);
        if ($hh->is_ecomm == 1) $browser->check('@ie', $hh->is_ecomm); else $browser->uncheck('@ie', $hh->is_ecomm);
        if ($hh->is_scholar == 1) $browser->check('@is', $hh->is_scholar); else $browser->uncheck('@is', $hh->is_scholar);
    }


    /**
     * @throws TimeOutException
     */
    public function changeHousehold(Browser $browser, $from, $to)
    {
        $browser->assertInputValue('@adr1', $from->address1)->type('@adr1', $to->address1)
            ->assertInputValue('@adr2', $from->address2)->type('@adr2', $to->address2)
            ->assertInputValue('@city', $from->city)->type('@city', $to->city)
            ->assertSelected('@state', $from->province_id)->select('@state', $to->province_id)
            ->assertInputValue('@zip', $from->zipcd)->type('@zip', $to->zipcd)
            ->assertInputValue('@country', $from->country)->type('country', $to->country);
        if ($from->is_ecomm) $browser->assertChecked('@ie'); else $browser->assertNotChecked('@ie');
        if ($to->is_ecomm) $browser->check('@ie'); else $browser->uncheck('@ie');
        if ($from->is_scholar) $browser->assertChecked('@is'); else $browser->assertNotChecked('@is');
        if ($to->is_scholar) $browser->check('@is'); else $browser->uncheck('@is');
    }

    public function viewHousehold(Browser $browser, $hh)
    {
        $browser->assertInputValue('@adr1', $hh->address1)
            ->assertAttributeContains('@adr1', 'readonly', 'true')
            ->assertInputValue('@adr2', $hh->address2)
            ->assertAttributeContains('@adr1', 'readonly', 'true')
            ->assertInputValue('@city', $hh->city)
            ->assertAttributeContains('@adr1', 'readonly', 'true')
            ->assertSelected('@state', $hh->province_id)->assertDisabled('@state')
            ->assertInputValue('@zip', $hh->zipcd)
            ->assertAttributeContains('@adr1', 'readonly', 'true')
            ->assertInputValue('@country', $hh->country)
            ->assertAttributeContains('@adr1', 'readonly', 'true');
        if ($hh->is_address_current) $browser->assertChecked('@iac'); else $browser->assertNotChecked('@iac');
        if ($hh->is_ecomm) $browser->assertChecked('@ie'); else $browser->assertNotChecked('@ie');
        if ($hh->is_scholar) $browser->assertChecked('@is'); else $browser->assertNotChecked('@is');
        $browser->assertDisabled('@iac')->assertDisabled('@ie')->assertDisabled('@is')
            ->assertMissing('button[type=submit]');
    }
}
