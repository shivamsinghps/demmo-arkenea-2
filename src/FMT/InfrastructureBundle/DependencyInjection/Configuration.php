<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\DependencyInjection;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Controller\WebhooksController;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('infrastructure')
            ->append($this->getMapperNode())
            ->append($this->getDwollaNode())
        ;

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    public function getMapperNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('mapper');

        $node->children()
                ->scalarNode('class')->end()
                ->arrayNode('namespaces')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return NodeDefinition
     */
    public function getDwollaNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('dwolla');

        $node
            ->children()
                ->scalarNode('webhooks_route')
                    ->defaultValue('/payments/dwolla/webhooks')
                ->end()
                ->scalarNode('webhooks_token')->end()
                ->scalarNode('webhooks_controller')
                    ->defaultValue(WebhooksController::class)
                ->end()
            ->end()
        ;

        return $node;
    }
}
