<?php
namespace MC\MessagesBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use MC\MessagesBundle\Entity\Message;

class MessagePostEvent extends Event{
    protected $message;

    public function __construct(Message $message){
        $this->setMessage($message);
    }

    public function getMessage(){
        return $this->message;
    }

    public function setMessage(Message $message){
        $this->message = $message;
    }
}