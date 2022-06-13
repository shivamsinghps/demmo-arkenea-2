<?php

namespace FMT\InfrastructureBundle\Service\AwsLambda;

/**
 * Class ExecutorProvider
 * @package FMT\DomainBundle\Service\AwsLambda
 */
class ExecutorProvider
{
    /**
     * @var AwsExecutor
     */
    private $awsExecutor;

    /**
     * @var LocalExecutor
     */
    private $localExecutor;

    /**
     * @var string
     */
    private $environment;

    /**
     * ExecutorProvider constructor.
     * @param AwsExecutor $awsExecutor
     * @param LocalExecutor $localExecutor
     * @param string $environment
     */
    public function __construct(AwsExecutor $awsExecutor, LocalExecutor $localExecutor, string $environment)
    {
        $this->awsExecutor = $awsExecutor;
        $this->localExecutor = $localExecutor;
        $this->environment = $environment;
    }

    /**
     * @return AwsExecutor|LocalExecutor|ExecutorInterface
     */
    public function getExecutor()
    {
        if ($this->environment === 'prod') {
            return $this->awsExecutor;
        }

        return $this->localExecutor;
    }
}
