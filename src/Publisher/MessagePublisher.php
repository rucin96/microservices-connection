<?php

declare(strict_types=1);

namespace Vehis\MsC\Publisher;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vehis\MsC\Exception\CannotGenerateRoutingKeyException;
use Vehis\MsC\Exception\PublishingMessageFailed;
use Vehis\MsC\Generator\RoutingKeyGenerator;
use Vehis\MsC\Message\Message;

final readonly class MessagePublisher
{
    public function __construct(
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
            $routingKey = $message->hasRoutingKey()
                ? $this->routingKeyGenerator->decorateKey($message->getRoutingKey())
                : $this->routingKeyGenerator->generateKey($message::class);
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
