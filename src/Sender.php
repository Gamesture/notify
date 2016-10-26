<?php

namespace Notify;

use Notify\Providers\IProvider;

class Sender
{
    private $provider;

    public function __construct(IProvider $provider)
    {
        $this->provider = $provider;
    }

    public function send(Message $message, string $recipient)
    {
        return $this->provider->send($message, $recipient);
    }

    public function batchSend(Message $message, array $recipients)
    {
        return $this->provider->batchSend($message, $recipients);
    }

}