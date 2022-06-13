<?php

namespace FMT\PublicBundle\FormType\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class RegistrationDonorSubscriber
 * @package FMT\PublicBundle\FormType\Subscribers
 */
class RegistrationDonorSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $form->get('profile')->remove('studentId');
        $form->get('profile')->remove('school');
    }
}
