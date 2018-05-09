<?php
namespace MesClics\MessagesBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class MessageFormManager extends FormManager{
    const ERROR_NOTIFICATION_SINGULIER = "Le message n'a pas pu être envoyé. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les messages n'ont pas pu être envoyés. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le message a été envoyé.";
    const SUCCESS_NOTIFICATION_PLURIEL = "Les messages ont été envoyés.";
}