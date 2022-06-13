<?php

namespace FMT\PublicBundle\Twig;

use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\FormType\Security\RegistrationDonorType;
use FMT\PublicBundle\FormType\Security\RegistrationStudentType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RegistrationExtension
 * @package FMT\PublicBundle\Twig
 */
class RegistrationExtension extends \Twig_Extension
{
    /** @var Request */
    private $request;

    /** @var UserManagerInterface */
    private $manager;

    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * UserExtension constructor.
     * @param RequestStack $requestStack
     * @param UserManagerInterface $manager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        RequestStack $requestStack,
        UserManagerInterface $manager,
        FormFactoryInterface $formFactory
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->manager = $manager;
        $this->formFactory = $formFactory;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_donor_registration_form', [$this, 'getDonorRegistrationForm']),
            new \Twig_SimpleFunction('get_student_registration_form', [$this, 'getStudentRegistrationForm']),
        ];
    }

    /**
     * @return mixed
     */
    public function getDonorRegistrationForm()
    {
        $user = $this->manager->makeDonor();
        $form = $this->createForm(RegistrationDonorType::class, $user);
        $form->handleRequest($this->request);
        return $form->createView();
    }

    /**
     * @return mixed
     */
    public function getStudentRegistrationForm()
    {
        $user = $this->manager->makeStudent();
        $form = $this->createForm(RegistrationStudentType::class, $user);
        $form->handleRequest($this->request);
        return $form->createView();
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type The built type of the form
     * @param mixed $data The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormInterface|Form
     */
    protected function createForm($type, $data = null, array $options = [])
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
