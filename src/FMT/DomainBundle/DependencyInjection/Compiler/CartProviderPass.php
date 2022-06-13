<?php

namespace FMT\DomainBundle\DependencyInjection\Compiler;

use FMT\DomainBundle\Service\Manager\CartManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CartProviderPass
 * @package FMT\DomainBundle\DependencyInjection\Compiler
 */
class CartProviderPass implements CompilerPassInterface
{
    const CART_PROVIDER_TAG = 'fmt.cart.provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(CartManager::class)) {
            return;
        }

        $definition = $container->findDefinition(CartManager::class);

        $taggedServices = $container->findTaggedServiceIds(self::CART_PROVIDER_TAG);

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall('addProvider', [new Reference($id), $tag['alias']]);
            }
        }

        $definition->addMethodCall('initCart');
    }
}
