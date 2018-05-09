<?php

namespace MesClics\MessagesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MesClics\UserBundle\Repository\UserRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MessageType extends AbstractType
{
    /**- 
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client = $options['client'];
        $builder
        ->add('title', TextType::class, array(
            'label' => 'titre du message'
        ))
        ->add('content', TextareaType::class, array(
            'label' => 'corps du message'
        ));
        if($options['isAdmin']){
            $builder
            ->add('recipients', EntityType::class, array(
                'class' => 'MesClics\UserBundle\Entity\User',
                'label' => 'sélectionner le(s) destinataire(s) du message',
                'choice_label' => 'labelRecepient',
                'multiple' => true,
                'query_builder' => function(UserRepository $user_repo){
                    return $user_repo->getUsersListOrderedByClientsQB();
                })
            );
        } else if($options['isClient']){
            $builder
            ->add('recipients', EntityType::class, array(
            'class' => 'MesClics\UserBundle\Entity\User',
            'label' => 'sélectionner le(s) destinataire(s) du message',
            'choice_label' => 'labelRecepient',
            'multiple' => true,
            'query_builder' => function(UserRepository $user_repo){
                return $user_repo->getUsersListOfClientQB($client);
                })
            );
        }
        $builder
        ->add('draft', CheckboxType::class, array(
            'label' => 'Enregistrer comme brouillon',
            'required' => false
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'envoyer' 
        ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesClics\MessagesBundle\Entity\Message',
            'isAdmin' => null,
            'isClient' => null,
            'client' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_messagesbundle_message';
    }


}
