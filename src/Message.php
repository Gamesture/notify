<?php

namespace Notify;

class Message
{

    /**
     * @var string
     */
    public $body;

    /**
     * @var string
     */
    public $sound = null;

    /**
     * @var string
     */
    public $badge = null;

    /**
     * @var array
     */
    public $custom_fields = [];

    /**
     * Message constructor.
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

}