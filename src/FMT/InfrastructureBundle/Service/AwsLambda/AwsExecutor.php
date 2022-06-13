<?php

namespace FMT\InfrastructureBundle\Service\AwsLambda;

use Aws\Lambda\LambdaClient;
use Aws\Sdk;

/**
 * Class AwsExecutor
 * @package FMT\DomainBundle\Service\AwsLambda
 */
class AwsExecutor implements ExecutorInterface
{
    /**
     * @var LambdaClient
     */
    private $lambda;

    /**
     * AwsExecutor constructor.
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->lambda = $sdk->createLambda();
    }

    /**
     * @param string $functionName
     * @param array $arguments
     * @return mixed
     */
    public function invoke(string $functionName, array $arguments)
    {
        $result = $this->lambda->invoke([
            'FunctionName' => $functionName,
            'InvocationType' => 'RequestResponse',
            'Payload' => json_encode($arguments),
        ]);

        return json_decode((string) $result->get('Payload'), true);
    }
}
