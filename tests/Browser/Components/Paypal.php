<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Paypal extends BaseComponent
{
    private const WAIT = 400;
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@buttons' => 'div.paypal-buttons-context-iframe',
            '@paypill' => 'div.paypal-button-number-0',
            '@loginfield' => 'input#email',
            '@nextbutton' => '#btnNext',
            '@loginbutton' => '#btnLogin',
            '@passfield' => 'input#password',
            '@paybutton' => '#payment-submit-btn',
            '@acceptall' => '#acceptAllButton'

        ];
    }

    public function pay(Browser $browser, $amount) {
        $browser->waitFor('@buttons');
        $windowHandle = $browser->driver->getWindowHandle();

        $browser->driver->switchTo()->frame(0);

        $browser->driver->manage()->deleteAllCookies();
        $browser->script("localStorage.clear()");

        $browser//->scrollIntoView('@paypill')->pause(self::WAIT) Odd but not terrifying
            ->click('@paypill')->pause(self::WAIT);

        $window = collect($browser->driver->getWindowHandles())->last();
        $browser->driver->switchTo()->window($window);

        $browser->waitForText('Pay with PayPal');
        $browser->type('@loginfield', config('paypal.test_user'))
            ->click('@nextbutton')->waitFor('@loginbutton')
            ->type('@passfield', config('paypal.test_password'))
            ->click('@loginbutton')->waitForText('Ship to', 30)
            ->assertSee('$' . number_format($amount, 2))->scrollIntoView('@paybutton')
            ->pause(self::WAIT)->click('@acceptall')
            ->waitUntilMissingText('cookies')->pause(self::WAIT)
            ->click('@paybutton');

        $browser->driver->switchTo()->window($windowHandle);
        $browser->pause(self::WAIT);
    }
}
