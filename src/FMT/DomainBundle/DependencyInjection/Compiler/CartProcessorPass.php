<?php

namespace FMT\DomainBundle\DependencyInjection\Compiler;

use FMT\DomainBundle\EventSubscriber\Cart\CartActionSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CartProcessorPass
 * @package FMT\DomainBundle\DependencyInjection\Compiler
 */
class CartProcessorPass implements CompilerPassInterface
{
    const CART_PROCESSOR_TAG = 'fmt.cart.processor';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(CartActionSubscriber::class)) {
            return;
        }

        $definition = $container->findDefinition(CartActionSubscriber::class);

        $taggedServices = $container->findTaggedServiceIds(self::CART_PROCESSOR_TAG);

        // TODO: Figure out why compiler does not sort service definition by "priority" tag attribute
        uasort($taggedServices, function ($service1, $service2) {
            return $service2[0]['priority'] <=> $service1[0]['priority'];
        });

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall('addProcessor', [new Reference($id), $tag['alias']]);
            }
        }
    }
}
