<?php

namespace FMT\InfrastructureBundle\Service\AwsLambda;

/**
 * Interface ExecutorInterface
 * @package FMT\DomainBundle\Service\AwsLambda
 */
interface ExecutorInterface
{
    public function invoke(string $functionName, array $arguments);
}
