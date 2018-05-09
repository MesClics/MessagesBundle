<?php
namespace MC\MessagesBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use MC\MessagesBundle\Entity\Message;

class MessageReadEvent extends Event{
    protected $message;

    public function __construct(Message $message){
        $this->setMessage($message);
    }

    public function setMessage(Message $message){
        $this->message = $message;
    }

    public function getMessage(){
        return $this->message;
    }
}