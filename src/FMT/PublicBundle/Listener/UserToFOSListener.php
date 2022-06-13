<?php
/**
 * Author: Anton Orlov
 * Date: 23.03.2018
 * Time: 13:45
 */

namespace FMT\PublicBundle\Listener;

use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Event\UserEvent;
use FMT\PublicBundle\FormType\Security\RegistrationDonorType;
use FMT\PublicBundle\FormType\Security\RegistrationStudentType;
use FMT\PublicBundle\FormType\Security\UserPasswordType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserToFOSListener
 * @package FMT\PublicBundle\Listener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class UserToFOSListener
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var FormFactoryInterface */
    private $factory;

    /** @var RequestStack */
    private $stack;

    /**
     * UserToFOSListener constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param FormFactoryInterface $factory
     * @param RequestStack $stack
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        FormFactoryInterface $factory,
        RequestStack $stack
    ) {
        $this->dispatcher = $dispatcher;
        $this->factory = $factory;
        $this->stack = $stack;
    }

    /**
     * @param UserEvent $event
     */
    public function onUserSubmitted(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user->isAnyStudent() && !$user->isAnyDonor()) {
            return;
        }

        $event = new GetResponseUserEvent($user, $this->stack->getCurrentRequest());
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        $event = new FormEvent($this->getUserForm($user), $this->stack->getCurrentRequest());
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
    }

    /**
     * @param UserEvent $event
     */
    public function onUserCreated(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user->isAnyStudent() && !$user->isAnyDonor()) {
            return;
        }

        $event = new FilterUserResponseEvent($user, $this->stack->getCurrentRequest(), new Response());
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, $event);
    }

    /**
     * @param UserEvent $event
     */
    public function onUserFailed(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user->isAnyStudent() && !$user->isAnyDonor()) {
            return;
        }

        $event = new FormEvent($this->getUserForm($user), $this->stack->getCurrentRequest());
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
    }

    /**
     * @param UserEvent $event
     */
    public function onResetSuccess(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user->isAnyStudent() && !$user->isAnyDonor()) {
            return;
        }

        $form = $this->factory->create(UserPasswordType::class, $user);
        $formEvent = new FormEvent($form, $this->stack->getCurrentRequest());
        $this->dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $formEvent);
    }

    /**
     * @param UserEvent $event
     */
    public function onConfirmationSuccess(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user->isAnyStudent() && !$user->isAnyDonor()) {
            return;
        }

        $fosEvent = new GetResponseUserEvent($user, $this->stack->getCurrentRequest());
        $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $fosEvent);
    }

    /**
     * @param User $user
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getUserForm(User $user)
    {
        $class = $user->isAnyStudent() ? RegistrationStudentType::class : RegistrationDonorType::class;

        return $this->factory->create($class, $user);
    }
}
