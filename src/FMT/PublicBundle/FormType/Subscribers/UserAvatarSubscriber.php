<?php

namespace FMT\PublicBundle\FormType\Subscribers;

use FMT\DataBundle\Entity\UserProfile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class UserAvatarSubscriber
 * @package FMT\PublicBundle\FormType\Subscribers
 */
class UserAvatarSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $form->get('avatar')->remove('comment');
    }
}
