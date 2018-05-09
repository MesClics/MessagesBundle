<?php

namespace MesClics\MessagesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MesClics\UserBundle\Repository\UserRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserMessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('recepients');
        ;
    }

    public function getParent(){
        return 'MesClics\MessagesBundle\Form\MessageType';
    }
  
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_messagesbundle_message';
    }


}
