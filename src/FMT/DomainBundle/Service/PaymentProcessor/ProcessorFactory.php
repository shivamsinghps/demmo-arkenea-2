<?php
/**
 * Author: Anton Orlov
 * Date: 04.05.2018
 * Time: 13:07
 */

namespace FMT\DomainBundle\Service\PaymentProcessor;

class ProcessorFactory
{
    /** @var ProcessorInterface[]|array */
    private $processors = [];

    /**
     * This method will be invoked by ProcessorCompilerPass to add processors into factory
     * @see FMT\DomainBundle\DependencyInjection\Compiler\ProcessorCompilerPass
     *
     * @param ProcessorInterface $processor
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $hasInstance = 0 < count(array_filter($this->processors, function (ProcessorInterface $item) use ($processor) {
            return $item === $processor;
        }));
        if (!$hasInstance) {
            $this->processors[] = $processor;
        }
    }

    /**
     * @param string $descriptor
     * @return ProcessorInterface|null
     */
    public function getInstance(string $descriptor)
    {
        $result = null;
        foreach ($this->processors as $processor) {
            if ($processor->isSupport($descriptor)) {
                $result = $processor;
                break;
            }
        }
        return $result;
    }
}
