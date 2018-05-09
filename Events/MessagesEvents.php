<?php
namespace MC\MessagesBundle\Events;

final class MessagesEvents{
    //on définit les noms de nos événements sur les messages
    const POST_MESSAGE = 'mc_messages.post_message';
    const READ_MESSAGE = 'mc_messages.read_message';
}