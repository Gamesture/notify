<?php

namespace Notify\Providers;

use Notify\Message;

interface IProvider
{
    public function send(Message $message, string $recipient);
    public function batchSend(Message $message, array $recipients);
}