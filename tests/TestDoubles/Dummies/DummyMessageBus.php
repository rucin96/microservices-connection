<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\TestDoubles\Dummies;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\RuntimeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Vehis\Msc\Message\Message;

class DummyMessageBus implements MessageBusInterface
{
    public const FAILING_MESSAGE_KEY = 'will.fail';
    private array $publishedMessages = [];

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        if ($this->isMessageContainsFailingKey($message)) {
            throw new RuntimeException();
        }

        $this->publishedMessages[] = $message instanceof Envelope
            ? $message->getMessage()
            : $message;

        return new Envelope($message);
    }

    public function getPublishedMessages(): array
    {
        return $this->publishedMessages;
    }

    public function reset(): void
    {
        $this->publishedMessages = [];
    }

    private function isMessageContainsFailingKey(object $message): bool
    {
        if ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        if (false === $message instanceof Message) {
            return false;
        }

        $routingKey = $message->getRoutingKey();

        return self::FAILING_MESSAGE_KEY === (string) $routingKey;
    }
}
