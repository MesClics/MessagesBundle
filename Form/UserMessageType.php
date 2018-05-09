<?php

namespace MC\MessagesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MC\UserBundle\Repository\UserRepository;

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
        return 'MC\MessagesBundle\Form\MessageType';
    }
  
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mc_messagesbundle_message';
    }


}
