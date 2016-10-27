<?php

namespace Gamesture\Notify\Providers;

use Gamesture\Notify\Message;

interface IProvider
{
    public function send(Message $message, string $recipient);
    public function batchSend(Message $message, array $recipients);
}