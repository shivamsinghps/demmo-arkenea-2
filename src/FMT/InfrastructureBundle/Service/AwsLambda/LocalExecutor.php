<?php

namespace FMT\InfrastructureBundle\Service\AwsLambda;

use FMT\InfrastructureBundle\Exception\EnvironmentConfigurationException;
use Symfony\Component\Process\Process;

/**
 * Class LocalExecutor
 * @package FMT\DomainBundle\Service\AwsLambda
 */
class LocalExecutor implements ExecutorInterface
{
    /**
     * @var string
     */
    private $nodeBinary;

    /**
     * @var string
     */
    private $lambdaDir;

    /**
     * @var string
     */
    private $functions;

    public function __construct(array $lambdaFunctionLocalOptions, array $lambdaFunctions, string $kernelEnvironment)
    {
        if (!in_array($kernelEnvironment, ['dev', 'test'])) {
            throw new EnvironmentConfigurationException('LocalExecutor must not be executed on production');
        }

        $this->nodeBinary = $lambdaFunctionLocalOptions['nodeBinary'];
        $this->lambdaDir = $lambdaFunctionLocalOptions['lambdaDir'];
        $this->functions = $lambdaFunctions;
    }

    /**
     * @param string $functionName
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function invoke(string $functionName, array $arguments)
    {
        if (!array_key_exists($functionName, $this->functions)) {
            throw new \Exception(sprintf('Unsupported lambda function %s', $functionName));
        }

        $command = sprintf("%s %sLocal.js '%s'", $this->nodeBinary, $functionName, json_encode($arguments));

        $process = new Process(
            $command,
            $this->lambdaDir,
            $this->functions[$functionName]['environmentVariables']
        );
        $process->inheritEnvironmentVariables(true);
        $process->run();

        $output = $process->getOutput();

        return json_decode($output, true);
    }
}
