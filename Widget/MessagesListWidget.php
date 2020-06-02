<?php
namespace MesClics\MessagesBundle\Widget;

use MesClics\UserBundle\Entity\User;
use MesClics\UtilsBundle\Widget\Widget;
use MesClics\MessagesBundle\Entity\Message;
use Symfony\Component\Security\Core\Security;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\AdminBundle\Widget\Handler\BasicWidgetHandler;

abstract class MessagesListWidget extends Widget{
    protected $messages;
    protected $user;

    public function __construct(User $user, BasicWidgetHandler $handler){
        $this->user = $user;
        $this->handler = $handler;
        $this->hydrate();
    }

    public function setMessages(ArrayCollection $messages){
        $this->messages = $messages;
        return $this;
    }

    public function getMessages(){
        return $this->messages;
    }

    public function getUser(){
        return $this->user;
    }

    abstract public function hydrate();
}