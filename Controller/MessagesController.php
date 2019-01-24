<?php
namespace MesClics\MessagesBundle\Controller;

use MesClics\UserBundle\Entity\User;
use MesClics\MessagesBundle\Entity\Message;
use MesClics\AdminBundle\Entity\Notification;
use MesClics\MessagesBundle\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use MesClics\MessagesBundle\Events\MessagesEvents;
use MesClics\MessagesBundle\Events\MessagePostEvent;
use MesClics\MessagesBundle\Events\MessageReadEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use MesClics\UtilsBundle\PrevCurrNext\MesClicsPrevCurrNext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\MessagesBundle\MessagesRetriever\MessagesRetriever;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessagesController extends Controller{
    /**
     * @Security("has_role('ROLE_CLIENT')")
     * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
     */
    public function homeAction(Request $request, Message $message = null, MesClicsPrevCurrNext $prevCurrNext, MessagesRetriever $messages_retriever){
        // if($request->query->get('message_id')){
        //     $repo = $this->getDoctrine()->getManager()->getRepository('MesClicsMessagesBundle:Message');
        //     $message = $repo->find($request->query->get('message_id'));
        // } else{
        //     $message = null;
        // }
        
        $args = $this->getHomeArgs($request, $message, $prevCurrNext, $messages_retriever);
        // var_dump($args[$args['subSection'].'Messages']);die();
        if($request->isMethod('POST')){
            return $this->redirectToRoute('mesclics_admin_messages');
        }

        return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    }

    private function getHomeArgs(Request $request, Message $message = null, MesClicsPrevCurrNext $prevCurrNext, MessagesRetriever $messages_retriever){
        //on récupère les messages non lus de l'utilisateur
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        //on récupère le filtre
        //par défaut on affiche tous les messages reçus
        if($request->query->get('filter')){
            if($request->query->get('filter') === 'reply'){
                $subSection = 'new';
            } else{
                $subSection = $request->query->get('filter');
            }
        } else{
            $subSection = 'received';
        }

        $args = array(
            'currentSection' => 'messages',
            'subSection' => $subSection
        );

        $page = $request->query->get('page');
        if($page){
            $args['page'] = $page;
        }
        //on récupère les messages selon le filtre ou la subSection sauf si la sous-section est 'new' ou 'draft'
        //TODO: messages groupés par conversation
        if($subSection !== 'new'){
            //on définit les order_params pour le messages_retriever
            $order_params = array(
                'date-creation' => 'creationDate',
                'titre' => 'title',
                'message' => 'content',
                'auteur' => 'author'
            );
            $messages_retriever->addOrderParams($order_params);

            
            //on passe les éventuels paramètres de tris de la requête au messages_retriever
            // FILTER (par défaut on veut les messages reçus)
            $messages_retriever->setFilter($subSection);
            
            if($request->query->get('order-by')){
                $messages_retriever->setOrderBy($request->query->get('order-by'));
            }
            if($request->query->get('sort')){
                $messages_retriever->setOrder($request->query->get('sort'));
            } else{
                if(preg_match('/^date-/',$messages_retriever->getOrderBy())){
                    $messages_retriever->setOrder('DESC');
                } else{
                    $messages_retriever->setOrder('ASC');
                }
            }

            $args['sort_params'] = array(
                'filter' => $messages_retriever->getFilter(),
                'order_by' => $messages_retriever->getOrderBy(),
                'sort' => $messages_retriever->getOrder()
            );

            $filter = $messages_retriever->getFilter();

            $args[$filter .'Messages'] = $messages_retriever->getMessages();

            //preview
            if($message){
                $args['message_preview'] = $this->getMessagePreview($message, $args[$subSection.'Messages'], $prevCurrNext);
            }
        } else{
            $newMessageForm = $this->newMessageForm($request, $message);
            $args['newMessageForm'] = $newMessageForm->createView();
        }
        return $args;
    }

    // /**
    //  * @Security("has_role('ROLE_CLIENT')")
    //  */
    // public function newAction(Request $request){
    //     $args = $this->getHomeArgs($request, null, 'new');

    //     $newMessageForm = $this->newMessageForm($request);
    //     $args['newMessageForm'] = $newMessageForm->createView();

    //     if($request->isMethod('POST')){
    //          return $this->redirectToRoute('mesclics_admin_messages_received');
    //     }
    //     return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    // }

    // /**
    //  * @Security("has_role('ROLE_CLIENT')")
    //  * @ParamConverter("to", options={"mapping": {"to": "id"}})
    //  */
    // public function newToAction(User $to, Request $request){
    //     $args = $this->getHomeArgs($request, null, 'new');

    //     $newMessageForm = $this->newMessageForm($request, null, $to);
    //     $args['newMessageForm'] = $newMessageForm->createView();

    //     if($request->isMethod('POST')){
    //          return $this->redirectToRoute('mesclics_admin_messages_received');
    //     }
    //     return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    // }

    // /**
    //  * @Security("has_role('ROLE_CLIENT')")
    //  * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
    //  */
    // public function replyAction(Message $message, Request $request){
    //     $args = $this->getHomeArgs($request, null);

    //     if($request->isMethod('POST')){
    //          return $this->redirectToRoute('mesclics_admin_messages_received');
    //     }
    //     return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    // }

    // /**
    //  * @Security("has_role('ROLE_CLIENT')")
    //  * @ParamConverter("message", options={"mapping": {"message_id": "id"}})
    //  */
    // public function newWithPreviewAction(Message $message, Request $request){
    //     $args = $this->getHomeArgs($request, null, 'new');
    //     $args['initialMessage'] = $message;

    //     $newMessageForm = $this->newMessageForm($request, $message);
    //     $args['newMessageForm'] = $newMessageForm->createView();

    //     if($request->isMethod('POST')){
    //          return $this->redirectToRoute('mesclics_admin_messages_received');
    //     }
    //     return $this->render('MesClicsAdminBundle:Panel:messages.html.twig', $args);
    // }


    private function getMessagePreview(Message $message, array $results, MesClicsPrevCurrNext $prevCurrNext){
         if($message){
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