<?php

declare(strict_types=1);

namespace Vehis\Msc\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MessengerQueuePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('msc.queues')) {
            return;
        }

        $queues = $container->getParameter('msc.queues');

        if (empty($queues)) {
            return;
        }

        $frameworkConfigs = $container->getExtensionConfig('framework');

        if (empty($frameworkConfigs)) {
            return;
        }

        $messengerConfig = $this->extractMessengerConfig($frameworkConfigs);

        if (empty($messengerConfig)) {
            return;
        }

        $updatedMessengerConfig = $this->injectQueues($messengerConfig, $queues);

        // Prepend only the messenger configuration
        $container->prependExtensionConfig('framework', [
            'messenger' => $updatedMessengerConfig
        ]);
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

                return $messengerConfig;
            }

            $existingQueues = $transport['options']['queues'];
            $transport['options']['queues'] = array_merge(array_unique($queues, $existingQueues));
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