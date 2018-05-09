<?php

namespace MesClics\MessagesBundle\Events\EventListeners;

use MesClics\MessagesBundle\MessageReader\MessageReader;
use MesClics\MessagesBundle\Events\MessageReadEvent;

class MessageReadListener{
    private $message_reader;

    public function __construct(MessageReader $message_reader){
        $this->message_reader = $message_reader;
    }

    public function process(MessageReadEvent $event){
        $this->message_reader->setAsRead($event->getMessage());
    }
}