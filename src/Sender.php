<?php

namespace Gamesture\Notify;

use Gamesture\Notify\Providers\IProvider;

class Sender
{
    /**
     * @var IProvider
     */
    private $provider;

    /**
     * Sender constructor.
     * @param IProvider $provider
     */
    public function __construct(IProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param Message $message
     * @param string $recipient
     * @return mixed
     */
    public function send(Message $message, string $recipient)
    {
        return $this->provider->send($message, $recipient);
    }

    /**
     * @param Message $message
     * @param array $recipients
     * @return mixed
     */
    public function batchSend(Message $message, array $recipients)
    {
        return $this->provider->batchSend($message, $recipients);
    }

}