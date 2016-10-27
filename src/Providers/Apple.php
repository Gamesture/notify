<?php

namespace Gamesture\Notify\Providers;

use Gamesture\Notify\Message;

class Apple implements IProvider
{
    private $pushStream = null;
    private $feedbackStream = null;

    private $certificate_path;

    const PUSH_URL = 'ssl://gateway.push.apple.com:2195';
    const FEEDBACK_URL = 'ssl://feedback.push.apple.com:2196';

    /**
     * Apple constructor.
     * @param string $certificate_path
     */
    public function __construct(string $certificate_path)
    {
        $this->certificate_path = $certificate_path;
    }

    public function __destruct()
    {
        if ($this->pushStream) {
            fclose($this->pushStream);
        }
        if ($this->feedbackStream) {
            fclose($this->feedbackStream);
        }
    }

    private function getPushStream()
    {
        if (!$this->pushStream) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->certificate_path);
            $this->pushStream = stream_socket_client(static::PUSH_URL, $errno, $errstr, 5, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$this->pushStream) {
                throw new \Exception("Connection to APNS failed: $errno - $errstr");
            }
        }
        return $this->pushStream;
    }

    private function getFeedbackStream()
    {
        if (!$this->feedbackStream) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->certificate_path);
            $this->feedbackStream = stream_socket_client(static::FEEDBACK_URL, $errno, $errstr, 5, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$this->feedbackStream) {
                throw new \Exception("Connection to feedback api failed: $errno - $errstr");
            }
        }
        return $this->feedbackStream;

    }

    public function batchSend(Message $message, array $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->send($message, $recipient);
        }
    }

    /**
     * https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/TheNotificationPayload.html
     * @param Message $message
     * @param string $recipient
     * @throws \Exception
     */
    public function send(Message $message, string $recipient)
    {
        $payload = $message->custom_fields;

        $payload['aps'] = [
            'alert' => $message->body,
            'badge' => $message->badge ? $message->badge : 1,
            'sound' => $message->sound ? $message->sound : 'default'
        ];

        $payload = json_encode($payload);


        $msg = chr(0) . pack('n', 32) . pack('H*', $recipient) . pack('n', strlen($payload)) . $payload;

        $result = fwrite($this->getPushStream(), $msg, strlen($msg));

        if (!$result) {
            throw new \Exception('Socket write failed');
        }
    }


    /**
     * https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Appendixes/BinaryProviderAPI.html
     */
    public function readFeedback()
    {
        $feedback = [];
        while (strlen($bytes = fread($this->getFeedbackStream(), 38)) == 38) {
            $time = unpack('N', substr($bytes, 0, 4));
            //$length = substr($bytes, 4, 2);
            $token = unpack('H*', substr($bytes, 6, 32));
            $feedback[] = [$time, $token];
        }
        return $feedback;
    }

}