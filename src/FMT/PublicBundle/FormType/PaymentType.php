<?php
/**
 * Author: Anton Orlov
 * Date: 06.04.2018
 * Time: 18:51
 */

namespace FMT\PublicBundle\FormType;

use FMT\PublicBundle\FormType\DataTransformer\PaymentProcessorTransformer;
use FMT\PublicBundle\FormType\Subscribers\PaymentSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PaymentType
 * @package FMT\PublicBundle\FormType
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentType extends AbstractType
{
    /** @var PaymentSubscriber */
    private $subscriber;

    /** @var PaymentProcessorTransformer */
    private $transformer;

    public function __construct(PaymentSubscriber $subscriber, PaymentProcessorTransformer $transformer)
    {
        $this->subscriber = $subscriber;
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isCheckout = $options["attr"]["data-checkout"] ?? false;

        $builder->setMethod("POST");
        $builder->add('email', EmailType::class, [
            "label" => false,
            "required" => false,
            "attr" => [
                "placeholder" => "E-mail"
            ],
            "constraints" => [
                new NotBlank(),
                new Email()
            ]
        ]);
        $builder->add('anonymous', ChoiceType::class, [
            "data" => 1,
            "choices" => ["fmt.payment_form.fields.anonymous" => 1, "fmt.payment_form.fields.named" => 0],
            "label" => false,
            "multiple" => false,
            "expanded" => true,
        ]);
        $builder->add('first_name', TextType::class, [
            "label" => false,
            "attr" => [
                "placeholder" => "fmt.payment_form.fields.first_name"
            ],
            "constraints" => [
                new NotBlank(),
                new Length([
                    'min' => 2,
                    'max' => 255
                ])
            ]
        ]);
        $builder->add('last_name', TextType::class, [
            "label" => false,
            "attr" => [
                "placeholder" => "fmt.payment_form.fields.last_name"
            ],
            "constraints" => [
                new NotBlank(),
                new Length([
                    'min' => 2,
                    'max' => 255
                ])
            ]
        ]);
        $builder->add('payment_amount', HiddenType::class, [
            "constraints" => [
                new NotBlank()
            ],
            "empty_data" => 0,
            "error_bubbling" => false
        ]);
        $builder->add('payment_processor', HiddenType::class, [
            "constraints" => [
                new NotBlank(),
            ]
        ]);

        $builder->add('cancel', ButtonType::class, [
            "label" => "fmt.payment_form.buttons.cancel"
        ]);
        $builder->add('proceed', SubmitType::class, [
            "label" => $isCheckout ? "fmt.payment_form.buttons.checkout" : "fmt.payment_form.buttons.donate"
        ]);

        $builder->get("payment_processor")->addModelTransformer($this->transformer);

        $builder->addEventSubscriber($this->subscriber);
    }
}
