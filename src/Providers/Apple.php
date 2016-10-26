<?php

namespace Notify\Providers;

use Notify\Message;

class Apple implements IProvider
{
    private $fp;
    const URL = 'ssl://gateway.push.apple.com:2195';

    public function __construct(string $certificate_path)
    {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate_path);
        $this->fp = stream_socket_client(static::URL, $errno, $errstr, 5, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$this->fp) {
            throw new \Exception("Connection to APNS failed: $errno - $errstr");
        }
    }

    public function __destruct()
    {
        fclose($this->fp);
    }

    public function batchSend(Message $message, array $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->send($message, $recipient);
        }
    }

    public function send(Message $message, string $recipient)
    {
        $payload = json_encode(['aps' => [
            'badge' => 1,
            'alert' => $message->body,
            'sound' => $message->sound ? $message->sound : 'default'
        ]]);

        $msg = chr(0) . pack('n', 32) . pack('H*', $recipient) . pack('n', strlen($payload)) . $payload;

        $result = fwrite($this->fp, $msg, strlen($msg));

        if (!$result) {
            throw new \Exception('Socket write failed');
        }
    }

}