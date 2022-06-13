<?php

namespace FMT\DomainBundle;

use FMT\DomainBundle\DependencyInjection\Compiler\CartProcessorPass;
use FMT\DomainBundle\DependencyInjection\Compiler\CartProviderPass;
use FMT\DomainBundle\DependencyInjection\Compiler\OrderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FMT\DomainBundle\DependencyInjection\Compiler\ProcessorCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class DomainBundle
 * @package FMT\DomainBundle
 */
class DomainBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CartProviderPass());
        $container->addCompilerPass(new CartProcessorPass());
        $container->addCompilerPass(new ProcessorCompilerPass());

        parent::build($container);
    }
}
