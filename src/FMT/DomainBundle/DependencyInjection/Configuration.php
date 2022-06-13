<?php

declare(strict_types=1);

namespace FMT\DomainBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package FMT\DomainBundle\DependencyInjection\Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('domain')
            ->append($this->getOrderNode())
            ->append($this->getBookstorePaymentNode())
        ;

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    public function getOrderNode(): NodeDefinition
    {
        $builder = new TreeBuilder();
        $node = $builder->root('order');

        return $node->children()
            ->arrayNode('returns')
            ->children()
            ->scalarNode('return_window')->end()
            ->scalarNode('chunk_size')->end()
            ->end()
            ->end()
            ->arrayNode('monitor')
            ->children()
            ->scalarNode('chunk_size')->end()
            ->end()
            ->end()
            ->end();
    }

    private function getBookstorePaymentNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('bookstore_payment');

        $node
            ->children()
            ->scalarNode('pause')->example('14 days')->end()
            ->scalarNode('send_date')->example('every monday')->end()
            ->scalarNode('send_time')->example('03:00')->end()
            ->integerNode('send_timezone')->example(-7)->end()
            ->scalarNode('error_time')->example('5 minutes')->end()
            ->end();

        return $node;
    }
}
