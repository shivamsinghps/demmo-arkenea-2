<?php

namespace FMT\PublicBundle\Twig;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\PublicBundle\FormType\Transaction\ThanksType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Extension;

class ThanksExtension extends Twig_Extension
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactoryInstance(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_thanks_form', [$this, 'getThanksFormView']),
        ];
    }

    public function getThanksFormView(UserTransaction $transaction)
    {
        return $this->formFactory->create(ThanksType::class, $transaction)->createView();
    }
}
