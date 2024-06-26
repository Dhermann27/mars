<?php

namespace Tests;

use Exception;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use RuntimeException;

trait MailTrap
{

    /**
     * The MailTrap configuration.
     *
     * @var integer
     */
    protected $mailTrapInboxId;

    /**
     * The MailTrap API Key.
     *
     * @var string
     */
    protected $mailTrapApiKey;

    /**
     * The Guzzle client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Get the configuration for MailTrap.
     *
     * @param integer|null $inboxId
     * @throws Exception
     */
    protected function applyMailTrapConfiguration($inboxId = null)
    {
        if (is_null($config = Config::get('services.mailtrap'))) {
            throw new Exception(
                'Set "secret" and "default_inbox" keys for "mailtrap" in "config/services.php."'
            );
        }

        $this->mailTrapInboxId = $inboxId ?: $config['default_inbox'];
        $this->mailTrapApiKey = $config['secret'];
    }

    /**
     * Fetch a MailTrap inbox.
     *
     * @param integer|null $inboxId
     * @return mixed
     * @throws RuntimeException
     */
    protected function fetchInbox($inboxId = null)
    {
        if (!$this->alreadyConfigured()) {
            $this->applyMailTrapConfiguration($inboxId);
        }

        $body = $this->requestClient()
            ->get($this->getMailTrapMessagesUrl())
            ->getBody();

        return $this->parseJson($body);
    }

    /**
     *
     * Empty the MailTrap inbox.
     *
     * @AfterScenario @mail
     */
    public function emptyInbox()
    {
        $this->requestClient()->patch($this->getMailTrapCleanUrl());
    }

    /**
     * Get the MailTrap messages endpoint.
     *
     * @return string
     */
    protected function getMailTrapMessagesUrl()
    {
        return "/api/v1/inboxes/{$this->mailTrapInboxId}/messages";
    }

    /**
     * Get the MailTrap "empty inbox" endpoint.
     *
     * @return string
     */
    protected function getMailTrapCleanUrl()
    {
        return "/api/v1/inboxes/{$this->mailTrapInboxId}/clean";
    }

    /**
     * Determine if MailTrap config has been retrieved yet.
     *
     * @return boolean
     */
    protected function alreadyConfigured()
    {
        return $this->mailTrapApiKey;
    }

    /**
     * Request a new Guzzle client.
     *
     * @return Client
     */
    protected function requestClient()
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => 'https://mailtrap.io',
                'headers' => ['Api-Token' => $this->mailTrapApiKey]
            ]);
        }

        return $this->client;
    }

    /**
     * @param $body
     * @return array|mixed
     * @throws RuntimeException
     */
    protected function parseJson($body)
    {
        $data = json_decode((string)$body, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Unable to parse response body into JSON: ' . json_last_error());
        }

        return $data === null ? array() : $data;
    }


    /**
     * Get the body of the message from an inbox.
     *
     * @param integer|null $inboxId
     * @param integer|null $messageId
     * @return string
     */
    protected function fetchBody($inboxId = null, $messageId = null)
    {
        return $this->requestClient()->get($this->getMailTrapBodyUrl($messageId))->getBody();
    }

    /**
     * Get the MailTrap "body" endpoint.
     *
     * @param integer|null $messageId
     * @return string
     */
    protected function getMailTrapBodyUrl($messageId = null)
    {
        return "/api/v1/inboxes/{$this->mailTrapInboxId}/messages/" . $messageId . "/body.txt";
    }
}
