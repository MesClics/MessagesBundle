<?php

namespace MC\MessagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MCMessagesBundle:Default:index.html.twig');
    }
}
