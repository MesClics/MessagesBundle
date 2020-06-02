<?php
namespace MesClics\MessagesBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\MessagesBundle\Widget\UnreadMessagesWidget;
use MesClics\MessagesBundle\Repository\MessageRepository;
use MesClics\AdminBundle\Widget\Handler\BasicWidgetHandler;
use MesClics\MessagesBundle\MessagesCounter\MessagesCounter;
use MesClics\MessagesBundle\MessagesRetriever\MessagesRetriever;

class MessagesWidgets extends WidgetsContainer{
    protected $basic_handler;

    public function __construct(BasicWidgetHandler $bh){
        parent::__construct();
        $this->basic_handler = $bh;
    }

    public function initialize($params = array()){
        $this->addWidget(new UnreadMessagesWidget($params['user'], $this->basic_handler));
        $unread_messages_widget = $this->getWidget('unread_messages');
        $unread_messages_widget
            ->addClasses(['unread-messages', 'medium']);
            
            if(sizeof($unread_messages_widget->getMessages()) > 1){
                $unread_messages_widget->setTitle(sizeof($unread_messages_widget->getMessages()) . ' messages non lus');
            } else{
                $unread_messages_widget->setTitle(sizeof($unread_messages_widget->getMessages()) . ' message non lu');
            };

        if(sizeof($unread_messages_widget->getMessages()) > 0){
            $unread_messages_widget->addClass('highlight');
        }
    }
}