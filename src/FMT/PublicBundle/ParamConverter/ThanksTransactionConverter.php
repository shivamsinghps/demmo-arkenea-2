<?php

namespace FMT\PublicBundle\ParamConverter;

use FMT\DomainBundle\Repository\UserTransactionRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ThanksTransactionConverter implements ParamConverterInterface
{
    /**
     * @var UserTransactionRepositoryInterface
     */
    private $userTransactionRepository;

    const FORM_NAME = 'thanks';

    public function __construct(UserTransactionRepositoryInterface $userTransactionRepository)
    {
        $this->userTransactionRepository = $userTransactionRepository;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $thanksFormData = $request->request->get(self::FORM_NAME, null);
        if (empty($thanksFormData)) {
            $userTransactionId = 0;
        } else {
            $userTransaction = $this->userTransactionRepository->findById($thanksFormData['id'] ?? null);
            $userTransactionId = $userTransaction ? $userTransaction->getId() : 0;
        }

        $request->attributes->set($configuration->getName(), $userTransactionId);
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() === 'thanksTransactionId';
    }
}
