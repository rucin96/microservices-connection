<?php

declare(strict_types=1);

namespace Vehis\Msc\DependencyInjection;

use Exception;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as DIExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension extends DIExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        try {
            $loader->load('services.yaml');
        } catch (Exception $e) {
            throw new RuntimeException('Unable to load services.yaml file', previous: $e);
        }

        $configuration = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('msc.publisher_name', $configuration['publisher_name']);
        $container->setParameter('msc.topics', $configuration['topics']);
    }
}
