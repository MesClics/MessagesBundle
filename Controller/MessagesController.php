<?php
namespace MesClics\MessagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MesClics\UserBundle\Entity\User;
use MesClics\MessagesBundle\Entity\Message;
use MesClics\MessagesBundle\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\AdminBundle\Entity\Notification;
use MesClics\MessagesBundle\Events\MessagesEvents;
use MesClics\MessagesBundle\Events\MessagePostEvent;
use MesClics\MessagesBundle\Events\MessageReadEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MessagesController extends Controller{
    /**
     * @Security("has_role('ROLE_CLIENT')")
     */
    public function homeAction(Request $request){        
        $args = $this->getHomeArgs($request);

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     */
    public function newAction(Request $request){
        $args = $this->getHomeArgs($request, null, 'new');

        $newMessageForm = $this->newMessageForm($request);
        $args['newMessageForm'] = $newMessageForm->createView();

        if($request->isMethod('POST')){
             return $this->redirectToRoute('mesclics_admin_messages_received');
        }
        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("to", options={"mapping": {"to": "id"}})
     */
    public function newToAction(User $to, Request $request){
        $args = $this->getHomeArgs($request, null, 'new');

        $newMessageForm = $this->newMessageForm($request, null, $to);
        $args['newMessageForm'] = $newMessageForm->createView();

        if($request->isMethod('POST')){
             return $this->redirectToRoute('mesclics_admin_messages_received');
        }
        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function replyAction(Message $message, Request $request){
        $args = $this->getHomeArgs($request, null, 'new');

        $newMessageForm = $this->newMessageForm($request, $message);

        if($request->isMethod('POST')){
             return $this->redirectToRoute('mesclics_admin_messages_received');
        }
        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function newWithPreviewAction(Message $message, Request $request){
        $args = $this->getHomeArgs($request, null, 'new');
        $args['initialMessage'] = $message;

        $newMessageForm = $this->newMessageForm($request, $message);
        $args['newMessageForm'] = $newMessageForm->createView();

        if($request->isMethod('POST')){
             return $this->redirectToRoute('mesclics_admin_messages_received');
        }
        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     */
    public function unreadMessagesAction(Request $request){
        $args = $this->getHomeArgs($request, null, 'unread');

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function unreadMessagesWithPreviewAction(Message $message, Request $request){
        if($message){
            $em = $this->getDoctrine()->getManager();
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $message->addReader($user);
            $em->flush();
        }
        $args = $this->getHomeArgs($request, $message, 'unread');

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }
    
    /**
     * @Security("has_role('ROLE_CLIENT')")
     */
    public function receivedMessagesAction($page, Request $request){
        $args = $this->getHomeArgs($request, null, 'received');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function receivedMessagesWithPreviewAction($page, Message $message, Request $request){
        $args = $this->getHomeArgs($request, $message, 'received');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * 
     */
    public function sentMessagesAction($page, Request $request){
        $args = $this->getHomeArgs($request, null, 'sent');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function sentMessagesWithPreviewAction($page, Message $message, Request $request){
        $args = $this->getHomeArgs($request, $message, 'sent');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     */
    public function draftMessagesAction($page, Request $request){
        $args = $this->getHomeArgs($request, null, 'draft');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function draftMessagesWithPreviewAction($page, Message $message, Request $request){
        $args = $this->getHomeArgs($request, $message, 'draft');
        $args['page'] = $page;

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    //MESSAGES
    private function getHomeArgs(Request $request, Message $message = null, $subSection = null){
        //on récupère les messages non lus de l'utilisateur
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $messages_retriever = $this->get('mesclics_messages.retriever');
        // $messagesRepo = $em->getRepository('MesClicsMessagesBundle:Message');
        //TODO: messages groupés par conversation
        //messages non lus
        //on définit les order_params pour le messages_retriever
        $order_params = array(
            'date-creation' => 'creationDate',
            'titre' => 'title',
            'message' => 'content',
            'auteur' => 'author'
        );
        
        $messages_retriever->addOrderParams($order_params);
        $unreadMessages = $messages_retriever->setFilter('unread')->getMessages();
        $receivedMessages = $messages_retriever->setFilter('received')->getMessages();
        $sentMessages = $messages_retriever->setFilter('sent')->getMessages();
        $draftMessages = $messages_retriever->setFilter('draft')->getMessages();

        // $unreadMessages = $messagesRepo->getUnreadMessages($user);
        // //tous les messages
        // $receivedMessages = $messagesRepo->getReceivedMessages($user);
        // //derniers messages envoyés
        // $sentMessages = $messagesRepo->getSentMessages($user);
        // //brouillons
        // $draftMessages = $messagesRepo->getDraftMessages($user);
        //nouveau message
        if($message){
            $newMessage = $message->getId();
        } else{
            $newMessage = null;
        }

        $args = array(
            'unread' => $unreadMessages,
            'received' => $receivedMessages,
            'sent' => $sentMessages,
            'draft' => $draftMessages,
            'new' => $newMessage,
            'currentSection' => 'messages',
            'subSection' => $subSection,
        );

        //on récupère l'éventuel message passé en argument
        if($message && $subSection){
            $args['message_preview'] = $this->getMessagePreview($message, $args[$subSection]);
        }
        return $args;
    }

    private function getMessagePreview(Message $message, $results){
         if($message){
            $prevCurrNext = $this->get('mesclics_utils.prevcurrnext');
            $prevCurrNext->handle($message, $results, true);
            $message_preview = $prevCurrNext;
            
            if(!$prevCurrNext->getCurrent()->isDraft()){
                //on crée un événement MessageReadEvent
                $event = new MessageReadEvent($prevCurrNext->getCurrent());
                //on déclenche l'événement read_message
                $this->get('event_dispatcher')->dispatch(MessagesEvents::READ_MESSAGE, $event);
            }
        } else{
            $message_preview = null;
        }
        return $message_preview;
    }

    private function newMessageForm(Request $request, Message $msg = null, User $to = null){
        $em = $this->getDoctrine()->getManager();
        $message = new Message();

        //si un message est passé en paramètre, alors il s'agit d'une réponse ou d'un brouillon à modifier
        if($msg){
            if(!$msg->isDraft()){
                $message->setParent($msg);
                $message->setTitle('RE: '. $msg->getTitle());
                //répondre à tous
                $message->addRecipient($msg->getAuthor());
                foreach($msg->getRecipients() as $recpt){
                    $message->addRecipient($recpt);
                }

                $initial_content = "\t" . $msg->getContent();
                $content_header = "\r\n \r\n" . ' .................................... ' . "\r\n \t" . 'Le '. date('d/m/Y',$msg->getCreationDate()->getTimestamp()) . ', ' . $msg->getAuthor() .  ' a écrit :'."\r\n";
                $message->setContent($content_header . $initial_content);
            } else{
                $message = $msg;
            }
        }
        //si un destinataire est passé en paramètre, 
        if($to){
            $recipts = $message->getRecipients();
            foreach($recipts as $recpt){
                if($recpt->getId() !== $to){
                    $message->removeRecipient($recpt);
                }
            }
            $message->addRecipient($to);
        }

        //on préparamètre l'auteur des nouveaux messages comme étant l'utilisateur courant
        $message->setAuthor($this->get('security.token_storage')->getToken()->getUser());
        
        $options = array(
            'isAdmin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
            'isClient' => $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT'),
            'client' => $this->get('security.token_storage')->getToken()->getUser()->getClient()
        );

        $messageForm = $this->createForm(MessageType::class, $message, $options);
        
        //si la requête est de type POST on traite le formulaire
        if($request->isMethod('POST')){
            $messageFormManager = $this->get('mesclics_messages.form_manager.new');
            if($messageFormManager->handle($messageForm)->hasSucceeded()){
                if(!$messageFormManager->getForm()->getData()->isDraft()){
                    //on ajoute un événement MessagePostEvent
                    $event = new MessagePostEvent($messageFormManager->getForm()->getData());
                    //on déclenche l'événement
                    $this->get('event_dispatcher')->dispatch(MessagesEvents::POST_MESSAGE, $event);
                }
            }
        }

        return $messageForm;
    }
}