<?php

namespace MesClics\MessagesBundle\Repository;

use MesClics\UserBundle\Entity\User;
/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends \Doctrine\ORM\EntityRepository
{
    public function getUnreadMessagesQB(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this
        ->createQueryBuilder('messages')
        ->andWhere('messages.draft != true')
        ->orderBy('messages.' . $order_by, $order);
        //TEST RECIPIENTS
        $qb
        ->andWhere(':user MEMBER OF messages.recipients')
            ->setParameter('user', $user)
        //TEST READERS
            ->andWhere(':user NOT MEMBER OF messages.readers')
            ->setParameter('user', $user)
        ;
        
        if($limit){
            $qb->setMaxResult($limit);
        }
        return $qb;
    }

    public function getUnreadMessages(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->getUnreadMessagesQB($user, $order_by, $order, $limit = null);
        return $qb->getQuery()->getResult();
    }

    public function countUnreadMessages(User $user){
        $qb = $this->getUnreadMessagesQB($user);
        $qb->select('COUNT(messages)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getSentMessagesQB(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->createQueryBuilder('messages')
        ->andWhere('messages.author = :author')
            ->setParameter('author', $user)
        ->andWhere('messages.draft = :draft')
            ->setParameter('draft', false)
        ->orderBy('messages.' . $order_by, $order);

        if($limit){
            $qb
            ->setMaxResults($limit);
        }

        return $qb;
    }

    public function getSentMessages(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->getSentMessagesQB($user, $order_by, $order, $limit = null);
        return $qb->getQuery()->getResult();
    }

    public function countSentMessages(User $user){
        $qb = $this->getSentMessagesQB($user);
        $qb->select('COUNT(messages)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getDraftMessagesQB(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->createQueryBuilder('messages');
        $qb
        ->andWhere('messages.author = :user')
            ->setParameter('user', $user)
        ->andWhere('messages.draft = :draft')
            ->setParameter('draft', true)
        ->orderBy('messages.' . $order_by, $order);

        if($limit){
            $qb
            ->setMaxResults($limit);
        }

        return $qb;
    }

    public function getDraftMessages(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->getDraftMessagesQB($user, $order_by, $order, $limit);
        return $qb->getQuery()->getResult();
    }

    public function countDraftMessages(User $user){
        $qb = $this->getDraftMessagesQB($user);
        $qb->select('COUNT(messages)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getReceivedMessagesQB(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this
        ->createQueryBuilder('messages')
        ->andWhere(':user MEMBER OF messages.recipients')
            ->setParameter('user', $user)
        ->andWhere('messages.draft = :draft')
            ->setParameter('draft', false)
        ->orderBy('messages.' . $order_by, $order);

        if($limit){
            $qb->setMaxResults($limit);
        }
       
        return $qb;
    }

    public function getReceivedMessages(User $user, $order_by = 'creationDate', $order = 'ASC', $limit = null){
        $qb = $this->getReceivedMessagesQB($user, $order_by, $order, $limit);
        return $qb->getQuery()->getResult();
    }

    public function getReceivedMessagesGroupedByConversation(User $user, $order_by = 'creationDate', $order = 'DESC', $limit = null){
        $qb = $this->getReceivedMessagesQB($user, $order_by, $order, $limit);
        //$qb
        // ->andWhere('messages.parent != :parent')
        //     ->setParameter('parent', null);
        //->andWhere($qb->expr()->isNull('messages.parent'));
        $messages =  $qb->getQuery()->getResult();

        $results = array(
            $messages
        );
        foreach($messages as $message){
            $conversation = array();
            if($message->getParent()){
                var_dump($message->getParent()->getId());
                $childItem = $message;
                while($childItem->getParent() != null){
                    $conversation[] = $childItem;
                    $childItem = $this->find($childItem->getParent());
                }
                $conversation = array_reverse($conversation);
                $results[] = $conversation;
            } else{
                $conversation[] = $message;
                $results[] = $conversation;
            }
        }
        return $results;
    }

    public function countReceivedMessages(User $user){
        $qb = $this->getReceivedMessagesQB($user);
        $qb->select('COUNT(messages)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
