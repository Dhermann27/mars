<?php

namespace Tests;

use App\Console\Kernel;
use App\Models\Year;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Faker\Factory;
use Laravel\Dusk\TestCase as BaseTestCase;
use NumberFormatter;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    protected static $hasSetupRun = false;
    protected static $year, $lastyear, $years;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
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
    protected function getChildBirthdate() {
        $year = date('Y') - self::$year->year;
        $faker = Factory::create();
        return $faker->dateTimeBetween('-' . (17 + $year) . ' years', '-' . (1 + $year) . ' years')->format('Y-m-d');
    }
}
