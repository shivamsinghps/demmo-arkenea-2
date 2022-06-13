<?php
/**
 * Author: Anton Orlov
 * Date: 14.04.2018
 * Time: 16:36
 */

namespace FMT\PublicBundle\FormType\Subscribers;

use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Type\Payment\Donation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentSubscriber implements EventSubscriberInterface
{
    /** @var AuthorizationCheckerInterface */
    private $checker;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var PaymentManagerInterface
     */
    private $paymentManager;

    /**
     * PaymentSubscriber constructor.
     * @param AuthorizationCheckerInterface $authChecker
     * @param TranslatorInterface $translator
     * @param PaymentManagerInterface $paymentManager
     */
    public function __construct(
        AuthorizationCheckerInterface $authChecker,
        TranslatorInterface $translator,
        PaymentManagerInterface $paymentManager
    ) {
        $this->checker = $authChecker;
        $this->translator = $translator;
        $this->paymentManager = $paymentManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => "onDataPreSet",
            FormEvents::PRE_SUBMIT => "onDataPreSubmit"
        ];
    }

    /**
     * This event removes unnecessary fields for authenticated user
     *
     * @param FormEvent $event
     */
    public function onDataPreSet(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($this->checker->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            $form->remove("email")->remove("first_name")->remove("last_name");
        }

        $options = $form->getConfig()->getOptions();

        $dataCheckout = isset($options["attr"]) && isset($options["attr"]["data-checkout"]) && $options["attr"]["data-checkout"];

        if ($dataCheckout) {
            $form->remove("payment_amount");
        }

        if ($data instanceof Donation && !$dataCheckout) {
            $this->addMaxAllowedDonateConstraint($form, $data);
        }
    }

    /**
     * This event removes NotBlank constraint in the case when anonymous button selected
     *
     * @param FormEvent $event
     */
    public function onDataPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $isAnonymous = isset($data["anonymous"]) && in_array($data["anonymous"], [1, 'true', true]);
        if ($isAnonymous) {
            $this->removeNameConstraints($form);
        }
    }

    /**
     * @param FormInterface $form
     */
    private function removeNameConstraints(FormInterface $form)
    {
        $fields = array_filter(["first_name", "last_name"], function ($field) use ($form) {
            return $form->has($field);
        });

        foreach ($fields as $field) {
            $fieldConfig = $form->get($field)->getConfig();
            $configOptions = $fieldConfig->getOptions();
            $configOptions["constraints"] = array_filter($configOptions["constraints"], function ($constraint) {
                return !$constraint instanceof NotBlank;
            });

            $form->add($field, get_class($fieldConfig->getType()->getInnerType()), $configOptions);
        }
    }

    /**
     * @param FormInterface $form
     * @param Donation $donation
     */
    private function addMaxAllowedDonateConstraint(FormInterface $form, Donation $donation)
    {
        $campaign = $donation->getStudent()->getUnfinishedCampaign();
        if (empty($campaign)) {
            return;
        }
        $leftAmount = $campaign->getAllowedDonateAmount();
        $fees = $this->paymentManager->getDonationFees($leftAmount);
        $limit = $leftAmount + array_sum($fees);
        $field = "payment_amount";
        $fieldConfig = $form->get($field)->getConfig();
        $configOptions = $fieldConfig->getOptions();
        $configOptions["constraints"][] = new LessThanOrEqual([
            'value' => $limit / 100,
            'message' => $this->translator->trans('fmt.campaign.too_much_amount', [
                '${amount}' => $campaign->getAllowedDonateAmountPrice()
            ])
        ]);
        $form->add($field, get_class($fieldConfig->getType()->getInnerType()), $configOptions);
    }
}
