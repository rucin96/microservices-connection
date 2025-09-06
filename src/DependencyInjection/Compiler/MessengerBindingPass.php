<?php

declare(strict_types=1);

namespace Vehis\Msc\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MessengerBindingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('msc.binding_keys')) {
            return;
        }

        $bindingKeys = $container->getParameter('msc.binding_keys');

        if (empty($bindingKeys)) {
            return;
        }

        // Get all framework extension configs
        $frameworkConfigs = $container->getExtensionConfig('framework');
        $updatedConfigs = [];

        foreach ($frameworkConfigs as $config) {
            if (isset($config['messenger']['transports'])) {
                $config = $this->injectBindingKeys($config, $bindingKeys);
            }
            $updatedConfigs[] = $config;
        }

        // Replace the extension config
        $container->prependExtensionConfig('framework', $updatedConfigs);
    }

    private function injectBindingKeys(array $config, array $bindingKeys): array
    {
        foreach ($config['messenger']['transports'] as $transportName => &$transport) {
            // Only process AMQP/RabbitMQ transports
            if (!$this->isAmqpTransport($transport)) {
                continue;
            }

            if (!isset($transport['options']['queues'])) {
                continue;
            }

            foreach ($transport['options']['queues'] as $queueName => &$queue) {
                // Merge MSC binding keys with existing ones
                $existingKeys = $queue['binding_keys'] ?? [];
                $queue['binding_keys'] = array_unique(array_merge($existingKeys, $bindingKeys));
            }
        }

        return $config;
    }

    private function isAmqpTransport(array $transport): bool
    {
        $dsn = $transport['dsn'] ?? '';

        return str_starts_with($dsn, 'amqp://')
            || str_starts_with($dsn, 'rabbitmq://')
            || str_contains($dsn, 'MESSENGER_TRANSPORT_DSN');
    }
}