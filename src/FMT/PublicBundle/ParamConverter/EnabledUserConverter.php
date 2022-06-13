<?php

namespace FMT\PublicBundle\ParamConverter;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\PublicBundle\ParamConverter\Traits\InheritanceTrait;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class EnabledUserConverter
 * @package FMT\PublicBundle\ParamConverter
 */
class EnabledUserConverter implements ParamConverterInterface
{
    use InheritanceTrait;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var User|null
     */
    private $currentUser;

    /**
     * EnabledUserConverter constructor.
     * @param UserManager $manager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->userManager = $manager;
        $token = $tokenStorage->getToken();
        $this->currentUser = $token ? $token->getUser() : null;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $id = $request->attributes->get('id');
        if (!$id) {
            return false;
        }

        $user = $this->userManager->findUserBy([
            'id' => $id,
            'enabled' => true,
        ]);

        if (!$this->isUserInstance($user)) {
            throw new NotFoundHttpException();
        }

        $options = $configuration->getOptions();
        if (!array_key_exists('invitation', $options) || !$options['invitation']) {
            switch (true) {
                case $user->getProfile()->getVisible() === UserProfile::VISIBILITY_NON:
                case $user->getProfile()->getVisible() === UserProfile::VISIBILITY_REGISTRED &&
                    !$this->isUserInstance($this->currentUser):
                    throw new NotFoundHttpException();
            }
        }

        $request->attributes->set($name, $user);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $this->isInstanceOf($configuration->getClass(), User::class);
    }

    /**
     * @param $user
     * @return bool
     */
    private function isUserInstance($user)
    {
        return $user instanceof User;
    }
}
