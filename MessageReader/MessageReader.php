<?php
namespace MesClics\MessagesBundle\MessageReader;

use MesClics\MessagesBundle\Entity\Message;
use MesClics\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManager;

class MessageReader{
    private $em;
    private $token_storage;
    private $reader;

    public function __construct(EntityManager $em, TokenStorage $token_storage){
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->reader = $this->token_storage->getToken()->getUser();
    }

    public function setAsRead(Message $message){
        if($message->getReaders()->contains($this->reader)){
            return;
        }

        $message->addReader($this->reader);
        $this->em->flush($message);
    }

    public function setAsUnread(Message $message){
        if(!$message->getReaders()->contains($this->reader)){
            return;
        }

        $message->removeReader($this->reader);
        $this->em->flush($message);
    }
}