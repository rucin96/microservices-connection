<?php

declare(strict_types=1);

namespace Vehis\Msc\Publisher;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vehis\Msc\Exception\CannotGenerateRoutingKeyException;
use Vehis\Msc\Exception\PublishingMessageFailed;
use Vehis\Msc\Generator\RoutingKeyGenerator;
use Vehis\Msc\Message\Message;

final readonly class MessagePublisher
{
    public function __construct(
        private string $publisherName,
        private MessageBusInterface $bus,
        private RoutingKeyGenerator $routingKeyGenerator,
    ) {
    }

    /**
     * @throws PublishingMessageFailed
     */
    public function publish(Message $message): void
    {
        try {
            $routingKey = $message->getRoutingKey() ?? $this->routingKeyGenerator->generateKey($message::class, $this->publisherName);
        } catch (CannotGenerateRoutingKeyException $e) {
            throw new PublishingMessageFailed('Unable to get the routing key for requested event', previous: $e);
        }

        $envelope = new Envelope($message, [new AmqpStamp($routingKey)]);

        try {
            $this->bus->dispatch($envelope);
        } catch (ExceptionInterface $e) {
            throw new PublishingMessageFailed('Unable to publish event on the event bus', previous: $e);
        }
    }
}
