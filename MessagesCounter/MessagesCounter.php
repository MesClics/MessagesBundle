<?php
namespace MesClics\MessagesBundle\MessagesCounter;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException; 

class MessagesCounter{
    private $em;
    private $token_storage;
    private $counter_names;

    public function __construct(EntityManager $em, TokenStorage $token_storage){
        $this->em = $em;
        $this->token_storage = $token_storage;
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
        $result = $messages_repo->$method_name($this->token_storage->getToken()->getUser());

        return $result;
    }
}