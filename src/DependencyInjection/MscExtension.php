<?php

declare(strict_types=1);

namespace Vehis\Msc\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MscExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters for use in services
        $container->setParameter('msc.publisher_name', $config['publisher_name']);
        $container->setParameter('msc.queue', $config['queue']);

        // Load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('msc');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $queues = $config['queue'] ?? [];

        if (empty($queues)) {
            return;
        }

        $frameworkConfigs = $container->getExtensionConfig('framework');
        $messengerConfig = $this->extractMessengerConfig($frameworkConfigs);

        $updatedMessengerConfig = $this->injectQueues($messengerConfig, $queues);

        $container->prependExtensionConfig('framework', [
            'messenger' => $updatedMessengerConfig
        ]);
    }

    public function getAlias(): string
    {
        return 'msc';
    }

    private function extractMessengerConfig(array $frameworkConfigs): array
    {
        foreach ($frameworkConfigs as $config) {
            if (isset($config['messenger'])) {
                return $config['messenger'];
            }
        }

        return [];
    }

    private function injectQueues(array $messengerConfig, array $queues): array
    {
        if (!isset($messengerConfig['transports'])) {
            return $messengerConfig;
        }

        foreach ($messengerConfig['transports'] as &$transport) {
            // Only process AMQP/RabbitMQ transports
            if (!$this->isAmqpTransport($transport)) {
                continue;
            }

            if (!isset($transport['options']['queues'])) {
                $transport['options']['queues'] = $queues;
                continue;
            }

            $existingQueues = $transport['options']['queues'];
            $transport['options']['queues'] = array_unique(
                array_merge($existingQueues, $queues)
            );
        }

        return $messengerConfig;
    }

    private function isAmqpTransport(array $transport): bool
    {
        $dsn = $transport['dsn'] ?? '';

        return str_starts_with($dsn, 'amqp://')
            || str_starts_with($dsn, 'rabbitmq://')
            || str_contains($dsn, 'MESSENGER_TRANSPORT_DSN');
    }
}
