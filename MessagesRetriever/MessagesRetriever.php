<?php
namespace MesClics\MessagesBundle\MessagesRetriever;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class MessagesRetriever{
    private $em;
    private $repository;
    private $order_params;
    private $order_by;
    private $order;
    private $filter;
    private $limit;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security){
        $this->em = $em;
        $this->security = $security;
        $this->repository = $this->em->getRepository('MesClicsMessagesBundle:Message');
        $this->limit = false; //par défaut on retourne un nb infini de résutlats.
        $this->order_by = 'creationDate'; //par défaut on trie les messaages par date de création.
        if(preg_match('/Date/m', $this->order_by)){//par défaut le critère de tri @order est ascendant sauf lorsque le critère de tri contient Date
            $this->order = 'DESC';
        } else{
            $this->order = 'ASC';
        }
        $this->filter = null; //par défaut on ne filtre pas les posts.
    }

    public function addOrderParams(Array $array){
        $this->order_params = $array;
        return $this;
    }

    public function getOrderBy(){
        return $this->order_by;
    }

    public function setOrderBy($order_by){
        if(!$this->order_params[$order_by]){
            throw new InvalidArgumentException('critère de tri @order_by (' . $order_by . ') non reconnu. Le critère doit être l\'un des suivants : ' . implode(', ', array_keys($this->order_params)));
        }

        // $this->order_by = $this->order_params[$order_by];
        $this->order_by = $order_by;
        return $this;
    }

    public function getOrder(){
        return $this->order;
    }

    public function setOrder($order){
        if(!$order == 'ASC' || !$order == 'DESC'){
            throw new InvalidArgumentException("le critère de tri @order (" . $order . ") ne peut être que l'un des deux suivants : ASC ou DESC");
        }
        $this->order = $order;
        return $this;
    }

    public function getFilter(){
        return $this->filter;
    }

    public function setFilter($filter){
        $this->filter = $filter;
        return $this;
    }

    public function setLimit(int $limit){
        $this->limit = $limit;
        return $this;
    }

    public function getMessages(){
        $filter_camel = explode('-', $this->filter);
        foreach($filter_camel as $k => $v){
            $filter_camel[$k] = ucfirst($v);
        }
        $filter_camel = implode('', $filter_camel);
        $method_name = 'get' . $filter_camel . 'Messages';
        // var_dump($method_name);
        return $this->repository->$method_name($this->security->getUser(), $this->order_params[$this->order_by], $this->order, $this->limit);
    }
}