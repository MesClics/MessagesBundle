<?php

namespace MesClics\MessagesBundle\Widget;

use MesClicsBundle\Entity\MesClicsUser;
use Doctrine\ORM\EntityManagerInterface;
use MesClics\MessagesBundle\Entity\Message;
use Symfony\Component\Security\Core\Security;
use MesClics\MessagesBundle\Widget\MessagesListWidget;
use MesClics\MessagesBundle\MessagesCounter\MessagesCounter;
use MesClics\MessagesBundle\MessagesRetriever\MessagesRetriever;

class UnreadMessagesWidget extends MessagesListWidget{
    public function hydrate(){
        $this->messages = $this->handler->getRepository(Message::class)->getUnreadMessages($this->user);
    }

    public function getName(){
        return 'unread_messages';
    }

    public function getTemplate(){
        return 'MesClicsMessagesBundle:Widget:unread-messages.html.twig';
    }

    public function getVariables(){
        return null;
    }

    public function isActive(){
        if(sizeof($this->messages) > 0){
            return true;
        }
        return false;
    }
}