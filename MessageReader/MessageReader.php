<?php
namespace MesClics\MessagesBundle\MessageReader;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\MessagesBundle\Entity\Message;
use Symfony\Component\Security\Core\Security;

class MessageReader{
    private $em;
    private $security;
    private $reader;

    public function __construct(EntityManagerInterface $em, Security $security){
        $this->em = $em;
        $this->security = $security;
        $this->reader = $this->security->getUser();
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