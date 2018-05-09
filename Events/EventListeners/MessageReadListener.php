<?php

namespace MC\MessagesBundle\Events\EventListeners;

use MC\MessagesBundle\MessageReader\MessageReader;
use MC\MessagesBundle\Events\MessageReadEvent;

class MessageReadListener{
    private $message_reader;

    public function __construct(MessageReader $message_reader){
        $this->message_reader = $message_reader;
    }

    public function process(MessageReadEvent $event){
        $this->message_reader->setAsRead($event->getMessage());
    }
}