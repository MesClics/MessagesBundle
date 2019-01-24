<?php
namespace MesClics\MessagesBundle\MessagesCounter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException; 

class MessagesCounter{
    private $em;
    private $security;
    private $counter_names;

    public function __construct(EntityManager $em, Security $security){
        $this->em = $em;
        $this->security = $security;
        $this->counter_names = array(
            'unread',
            'draft',
            'sent',
            'received'
        );
    }

    public function count($counter_name){
        if(!$counter_name){
            throw new InvalidArgumentException('Vous devez préciser le nom d\'un compteur');
        }
        if(!in_array($counter_name, $this->counter_names)){
            throw new InvalidArgumentException('Le nom du compteur ne peut être que l\'un des suivants : '.implode(', ', $this->counter_names));
        }

        $messages_repo = $this->em->getRepository('MesClicsMessagesBundle:Message');
        $method_name = 'count'.ucfirst($counter_name).'Messages';
        $result = $messages_repo->$method_name($this->security->getUser());

        return $result;
    }
}