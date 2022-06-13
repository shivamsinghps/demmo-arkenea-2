<?php

namespace FMT\PublicBundle\FormType;

use FMT\DataBundle\Entity\UserAvatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class UserAvatarType
 * @package FMT\PublicBundle\FormType
 */
class UserAvatarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filename', FileType::class, [
                'label' => $options['isLabelNeeded'] ? 'fmt.form.avatar' : false,
                'required' => true,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'minWidth' => UserAvatar::AVATAR_MIN_WIDTH,
                        'minHeight' => UserAvatar::AVATAR_MIN_HEIGHT,
                        'mimeTypes' => UserAvatar::AVATAR_ALLOWED_TYPE,
                        'maxSize' => sprintf('%dM', UserAvatar::AVATAR_MAX_SIZE_MB),
                        'minWidthMessage' => 'fmt.upload_avatar.dimension.min.width_error',
                        'minHeightMessage' => 'fmt.upload_avatar.dimension.min.height_error',
                        'maxSizeMessage' => 'fmt.upload_avatar.size_error',
                    ]),
                    new Callback([$this, 'validateUploadedFile']),
                ],
            ])
            ->add('comment', TextType::class, [
                'required' => false,
                'label' => $options['isLabelNeeded'] ? 'fmt.form.comment' : false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserAvatar::class,
                'validation_groups' => ['Default'],
                'attr' => ['novalidate' => true],
                'isLabelNeeded' => false,
                'oldAvatarName' => null,
            ]
        );
        $resolver->setAllowedTypes('isLabelNeeded', ['boolean']);
        $resolver->setAllowedTypes('oldAvatarName', ['string', 'null']);
    }

    /**
     * @param UploadedFile|null $file
     * @param ExecutionContextInterface $context
     */
    public function validateUploadedFile(UploadedFile $file = null, ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $formOptions = $form->getConfig()->getOptions();
        $oldAvatarName = isset($formOptions['oldAvatarName']) ? $formOptions['oldAvatarName'] : null;

        if (!$file instanceof UploadedFile && !$oldAvatarName) {
            $context->buildViolation('fmt.upload_avatar.not_blank')
                ->atPath('filename')
                ->addViolation();

            return;
        }
    }
}
