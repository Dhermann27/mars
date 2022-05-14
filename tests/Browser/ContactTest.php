<?php

namespace Tests\Browser;

use App\Models\Camper;
use App\Models\Contactbox;
use App\Models\User;
use Faker\Factory;
use Laravel\Dusk\Browser;
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
                ->type('message', $fakedGraph)
                ->type('captcha', 'TEST')
                ->click('button[type="submit"]')->waitFor('div.alert')->assertVisible('div.alert-success');

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($fakedName . " <" . $fakedEmail . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }

    public function testRandomEmailTst()
    {
        $code = rand(0, 3);
        switch ($code) {
            case 0:
                $this->tstMultipleContactboxEmails();
                break;
            case 1:
                $this->tstReturningCamper();
                break;
            case 2:
                $this->tstBadWords();
                break;
            case 3:
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
                ->type('message', $fakedGraph)
                ->type('captcha', 'TEST')
                ->click('button[type="submit"]')->waitFor('div.alert')->assertVisible('div.alert-success');

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


    public function tstBadWords()
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
                ->type('message', $fakedGraph)
                ->type('captcha', 'TEST')
                ->click('button[type="submit"]')->waitFor('div.alert')->assertVisible('div.alert-danger');

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertNotEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringNotContainsString($fakedName . " <" . $fakedEmail . ">", $body);
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
                ->assertSee('Contact Us');
            $this->assertEquals($browser->attribute('#yourname', 'placeholder'), $camper->firstname . ' ' . $camper->lastname);
            $this->assertEquals($browser->attribute('#email', 'placeholder'), $camper->email);
            $browser->assertSeeIn('select#mailbox', $box->name)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph)
                ->type('captcha', 'TEST')
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')->logout();

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
            $this->assertEquals($browser->attribute('#email', 'placeholder'), $user->email);
            $browser->assertSeeIn('select#mailbox', $box->name)
                ->select('mailbox', $box->id)
                ->type('message', $fakedGraph)
                ->type('captcha', 'TEST')
                ->click('button[type="submit"]')->waitFor('div.alert')
                ->assertVisible('div.alert-success')->logout();

        });

        $lastEmail = $this->fetchInbox()[0];
        $this->assertEquals($box->emails, $lastEmail['to_email']);
        $body = $this->fetchBody($lastEmail['inbox_id'], $lastEmail['id']);
        $this->assertStringContainsString($fakedName . " <" . $user->email . ">", $body);
        $this->assertStringContainsString($fakedGraph, $body);
    }
}
