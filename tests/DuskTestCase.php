<?php

namespace Tests;

use App\Console\Kernel;
use App\Models\Contactbox;
use App\Models\Year;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Faker\Factory;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use NumberFormatter;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $hasSetupRun = false;
    protected static $year, $lastyear, $years, $registrar;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (!static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!self::$hasSetupRun) {
            $app = require __DIR__ . '/../bootstrap/app.php';
            $kernel = $app->make(Kernel::class);
            $kernel->bootstrap();
            echo "Database migrate:refresh --seed\n";
            $kernel->call('migrate:refresh --seed');
            self::$hasSetupRun = true;
            self::$year = Year::factory()->create(['is_current' => 1]);
            self::$lastyear = Year::factory()->create(['is_current' => 0, 'year' => self::$year->year - 1]);
            self::$years = array(self::$year, self::$lastyear,
                Year::factory()->create(['is_current' => 0, 'year' => self::$lastyear->year - rand(1, 50)]));
            self::$registrar = Contactbox::factory()->create(['name' => 'Registrar']); // Confirm Email BCC's Registrar
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }


    /**
     * Return the number formatted into currency
     *
     * @param float $float
     * @return string
     */
    protected function moneyFormat($float)
    {
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::GROUPING_USED, 0);
        return $fmt->formatCurrency($float, "USD");
    }

    /**
     * Return a birthdate within 18 years of the first date of camp
     *
     * @return string
     */
    protected function getChildBirthdate()
    {
        $faker = Factory::create();
        return $faker->dateTimeBetween(self::$year->checkin . ' -18 years', self::$year->checkin . ' -1 day')->format('Y-m-d');
    }

    /**
     * Return a birthdate within 18 years of the first date of camp
     *
     * @return string
     */
    protected function getYABirthdate()
    {
        $faker = Factory::create();
        return $faker->dateTimeBetween(self::$year->checkin . ' -21 years', self::$year->checkin . ' -18 years')->format('Y-m-d');
    }

    /**
     * Submit a form but get an error back
     *
     * @param Browser $browser
     * @param int $wait
     * @param string $buttontext
     * @return Browser
     */
    protected function submitError(Browser $browser, $wait, string $buttontext = 'Save Changes')
    {
        $browser->pause($wait)->scrollIntoView('button[type=submit]')->pause($wait)
            ->press($buttontext)->waitFor('div.alert')->assertVisible('div.alert-danger')
            ->assertPresent('span.muusa-invalid-feedback');
        return $browser;
    }


    /**
     * Submit a form and get a successful response
     *
     * @param Browser $browser
     * @param int $wait
     * @param string $buttontext
     * @return Browser
     */
    protected function submitSuccess(Browser $browser, $wait, $buttontext = 'Save Changes')
    {
        $browser->pause($wait)->scrollIntoView('button[type=submit]')->pause($wait)
            ->press($buttontext)->waitUntilMissing('div.alert-danger')
            ->waitFor('div.alert')->assertVisible('div.alert-success');
        return $browser;
    }


    /**
     * Press the navtab with the parameter id and wait until the new tab is shown
     *
     * @param Browser $browser
     * @param int $id
     * @param int $wait
     * @return Browser
     */
    protected function pressTab(Browser $browser, $id, $wait)
    {
        $browser->script('window.scrollTo(0,0)');
        $browser->pause($wait);
        if (!str_contains($browser->attribute('#tablink-' . $id, 'class'), 'active')) {
            $browser->press('#tablink-' . $id)->waitForEvent('shown.mdb.tab', '#nav-tab');
        }
        return $browser;
    }

    /**
     * Assert if an element matched by $selector doesn't have $class in its classList
     *
     * @param Browser $browser
     * @param string $selector
     * @param string $class
     * @return void
     */
    protected function assertMissingClass(Browser $browser, $selector, $class)
    {
        $this->assertStringNotContainsString($class, $browser->attribute($selector, 'class'));
    }

}
