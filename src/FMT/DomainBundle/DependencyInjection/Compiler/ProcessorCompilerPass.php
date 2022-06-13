<?php
/**
 * Author: Anton Orlov
 * Date: 04.05.2018
 * Time: 13:36
 */

namespace FMT\DomainBundle\DependencyInjection\Compiler;

use FMT\DomainBundle\Service\PaymentProcessor\ProcessorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProcessorCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = "fmt.payment_processors";

    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Definition $factory */
        if (!($factory = $container->getDefinition(ProcessorFactory::class))) {
            return;
        }

        $services = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($services as $service) {
            $factory->addMethodCall('addProcessor', [$container->getDefinition($service)]);
        }
    }
}
