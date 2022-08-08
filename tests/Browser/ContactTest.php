<?php

namespace Tests\Browser;

use App\Models\Camper;
use App\Models\Contactbox;
use App\Models\User;
use Faker\Factory;
use Laravel\Dusk\Browser;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Tests\DuskTestCase;
use Tests\MailTrap;

/**
 * @group Contact
 */
class ContactTest extends DuskTestCase
{
    use MailTrap;

    // Can only send 2 emails per 10 seconds
    public function testNewVisitor()
    {
//        RecaptchaV3::shouldReceive('verify')->once()->andReturn(1.0);

        $faker = Factory::create();
        $fakedName = $faker->name;
        $fakedEmail = $faker->safeEmail;
        $fakedGraph = $faker->paragraph;
        $box = Contactbox::factory()->create();
        $this->browse(function (Browser $browser) use ($box, $fakedName, $fakedEmail, $fakedGraph) {
            $browser->visitRoute('contact.index')
                ->assertSee('Contact Us')
                ->assertSeeIn('select#mailbox', $box->name)
                ->type('yourname', $fakedName)
                ->type('email', $fakedEmail)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph);
//                ->type('captcha', 'TEST');
            $this->submitSuccess($browser, 0, 'Send Message');

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($fakedName . " <" . $fakedEmail . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }

//    public function testRefreshButton()
//    {
//        $faker = Factory::create();
//        $fakedName = $faker->name;
//        $fakedEmail = $faker->safeEmail;
//        $fakedGraph = $faker->paragraph;
//        $box = Contactbox::factory()->create();
//        $this->browse(function (Browser $browser) use ($box, $fakedName, $fakedEmail, $fakedGraph) {
//            $browser->visitRoute('contact.index')
//                ->assertSee('Contact Us')
//                ->type('yourname', $fakedName)
//                ->type('email', $fakedEmail)
//                ->select('mailbox', $box->id)
//                ->type('message', $fakedGraph)
//                ->clickAndWaitForReload('#refreshcaptcha')
//                ->assertSee('Contact Us')
//                ->assertMissing('div.alert');
//                ->assertValue('#yourname', $fakedName) // TODO: Stopped working?
//                ->assertValue('#email', $fakedEmail)
//                ->assertSelected('#mailbox', $box->id)
//                ->assertSeeIn('#message', $fakedGraph);
//
//        });
//    }
    public function testBadWords()
    {
        $faker = Factory::create();
        $fakedName = $faker->name;
        $fakedEmail = $faker->safeEmail;
        $fakedGraph = "Howdy ho there neighbor. Have you heard the Good News about the scriptures, in the words of Christ?";
        $box = Contactbox::factory()->create();
        $this->browse(function (Browser $browser) use ($box, $fakedName, $fakedEmail, $fakedGraph) {
            $browser->visitRoute('contact.index')
                ->assertSee('Contact Us')
                ->assertSeeIn('select#mailbox', $box->name)
                ->type('yourname', $fakedName)
                ->type('email', $fakedEmail)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph);
//                ->type('captcha', 'TEST');
            $this->submitError($browser, 0, 'Send Message');

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertNotEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringNotContainsString($fakedName . " <" . $fakedEmail . ">", $body);
    }

    // Can only send 2 emails per 10 seconds via Mailtrap
    public function testRandomEmailTst()
    {
        $code = rand(0, 2); // Number of tst functions
        switch ($code) {
            case 0:
                $this->tstMultipleContactboxEmails();
                break;
            case 1:
                $this->tstReturningCamper();
                break;
            case 2:
                $this->tstAccountButNoCamper();
                break;
        }
    }

    private function tstMultipleContactboxEmails()
    {
        $faker = Factory::create();
        $fakedName = $faker->name;
        $fakedEmail = $faker->safeEmail;
        $fakedGraph = $faker->paragraph;
        $box = Contactbox::factory()->create(['emails' => $faker->safeEmail . ',' . $faker->safeEmail]);
        $this->browse(function (Browser $browser) use ($box, $fakedName, $fakedEmail, $fakedGraph) {
            $browser->visitRoute('contact.index')
                ->assertSee('Contact Us')
                ->assertSeeIn('select#mailbox', $box->name)
                ->type('yourname', $fakedName)
                ->type('email', $fakedEmail)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph);
//                ->type('captcha', 'TEST');
            $this->submitSuccess($browser, 0, 'Send Message');

        });

        $lastEmail = $this->fetchInbox()[0];
        $emails = explode(', ', $lastEmail['to_email']);
        foreach ($emails as $email) {
            $this->assertStringContainsString($email, $box->emails);
        }
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($fakedName . " <" . $fakedEmail . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }

    private function tstReturningCamper()
    {
        $faker = Factory::create();
        $user = User::factory()->create();
        $camper = Camper::factory()->create(['email' => $user->email]);

        $fakedGraph = $faker->paragraph;
        $box = Contactbox::factory()->create();
        $this->browse(function (Browser $browser) use ($box, $user, $camper, $fakedGraph) {
            $browser->loginAs($user->id)->visitRoute('contact.index')
                ->assertSee('Contact Us')
                ->assertValue('#yourname', $camper->firstname . ' ' . $camper->lastname)
                ->assertValue('#email', $camper->email)
//            $this->assertEquals($browser->attribute('#yourname', 'value'), $camper->firstname . ' ' . $camper->lastname);
//            $this->assertEquals($browser->attribute('#email', 'value'), $camper->email);
                ->assertSeeIn('select#mailbox', $box->name)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph);
//                ->type('captcha', 'TEST');
            $this->submitSuccess($browser, 0, 'Send Message')->logout();

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($camper->firstname . " " . $camper->lastname . " <" . $camper->email . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }

    private function tstAccountButNoCamper()
    {
        $faker = Factory::create();
        $user = User::factory()->create();

        $fakedName = $faker->name;
        $fakedGraph = $faker->paragraph;
        $box = Contactbox::factory()->create();
        $this->browse(function (Browser $browser) use ($box, $user, $fakedName, $fakedGraph) {
            $browser->loginAs($user->id)->visitRoute('contact.index')
                ->assertSee('Contact Us')
                ->type('yourname', $fakedName);
            $this->assertEquals($browser->attribute('#email', 'value'), $user->email);
            $browser->assertSeeIn('select#mailbox', $box->name)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph);
//                ->type('captcha', 'TEST');
            $this->submitSuccess($browser, 0, 'Send Message')->logout();

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($fakedName . " <" . $user->email . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }
}
