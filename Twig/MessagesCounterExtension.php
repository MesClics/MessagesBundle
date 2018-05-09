<?php
namespace MC\MessagesBundle\Twig;

use MC\MessagesBundle\MessagesCounter\MessagesCounter;

class MessagesCounterExtension extends \Twig_Extension{
    private $counter;

    public function __construct(MessagesCounter $counter){
        $this->counter = $counter;
    }

    public function getMessagesCount($counter_name){
        return $this->counter->count($counter_name);
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('countMessages', array($this, 'getMessagesCount'))
        );
    }

    public function getName(){
        return 'MCMessagesCounter';
    }
}