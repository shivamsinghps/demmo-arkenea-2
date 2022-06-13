<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\DependencyInjection;

use FMT\InfrastructureBundle\Helper\StringHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class InfrastructureExtension
 */
class InfrastructureExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $mapperDefinition = new Definition($config['mapper']['class']);
        $mapperDefinition->addMethodCall('setMappers', [$this->getMappers($config['mapper']['namespaces'])]);

        $dwollaConfig = $config['dwolla'];

        if (!$container->hasParameter('fmt.payments.dwolla.webhooks_route')) {
            $container->setParameter('fmt.payments.dwolla.webhooks_route', $dwollaConfig['webhooks_route']);
        }

        if (!$container->hasParameter('fmt.payments.dwolla.webhooks_token')) {
            $container->setParameter('fmt.payments.dwolla.webhooks_token', $dwollaConfig['webhooks_token']);
        }

        if (!$container->hasParameter('fmt.payments.dwolla.webhooks_controller')) {
            $container->setParameter('fmt.payments.dwolla.webhooks_controller', $dwollaConfig['webhooks_controller']);
        }

        $container->setDefinition($config['mapper']['class'], $mapperDefinition);
    }

    /**
     * @param array $namespaces
     *
     * @return array
     * @throws ReflectionException
     */
    private function getMappers(array $namespaces): array
    {
        $result = [];

        foreach ($this->getPotentialMappers($namespaces) as $potentialMapper) {
            $reflection = new ReflectionClass($potentialMapper);
            if (!$reflection->isSubclassOf(AbstractMapper::class) || $reflection->isAbstract()) {
                continue;
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!StringHelper::strStartsWith($method->getName(), 'map') || count($method->getParameters()) !== 1) {
                    continue;
                }

                $sourceType = (string) $method->getParameters()[0]->getType();
                $targetType = (string) $method->getReturnType();

                if (!empty($sourceType) && !empty($targetType)) {
                    $result[$sourceType][$targetType] = [$potentialMapper, $method->getName()];
                }
            }
        }

        return $result;
    }
    
    /**
     * @param array $namespaces
     *
     * @return array
     */
    private function getPotentialMappers(array $namespaces): array
    {
        return array_filter(get_declared_classes(), function ($class) use ($namespaces) {
            $pos = strrpos($class, '\\');

            if ($pos === false) {
                return false;
            }

            $classNamespace = substr($class, 0, $pos + 1);

            return in_array($classNamespace, $namespaces);
        });
    }
}
