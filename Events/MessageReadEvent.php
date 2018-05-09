<?php
namespace MesClics\MessagesBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use MesClics\MessagesBundle\Entity\Message;

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