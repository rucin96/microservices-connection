<?php

declare(strict_types=1);

namespace Vehis\Msc\Publisher\Amqp;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vehis\Msc\Collection\PackageCollection;
use Vehis\Msc\Exception\CannotGenerateRoutingKeyException;
use Vehis\Msc\Exception\DispatchMessagesStoppedException;
use Vehis\Msc\Exception\InvalidRoutingKeyProvided;
use Vehis\Msc\Exception\MessageIsNoLongerSupportedException;
use Vehis\Msc\Exception\PublishingMessageFailed;
use Vehis\Msc\Exception\DispatchMessageStoppedException;
use Vehis\Msc\Generator\RoutingKeyGenerator;
use Vehis\Msc\Message\BackwardCompatibleInterface;
use Vehis\Msc\Message\Message;
use Vehis\Msc\Message\Package;
use Vehis\Msc\Message\Validator\MessageSupportedValidator;
use Vehis\Msc\Publisher\MessagePublisherInterface;
use Vehis\Msc\VO\RoutingKey;

final readonly class AmqpMessagePublisher implements MessagePublisherInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private RoutingKeyGenerator $routingKeyGenerator,
        private MessageSupportedValidator $supportedValidator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function publish(Message $message): void
    {
        $packages = new PackageCollection();

        try {
            $packages->add($this->preparePackage($message));
        } catch (MessageIsNoLongerSupportedException $e) {
            throw new PublishingMessageFailed('Publisher was unable to prepare package', previous: $e);
        }

        if ($message instanceof BackwardCompatibleInterface) {
            try {
                $packages->add($this->preparePackage($message->getPreviousVersion()));
            } catch (MessageIsNoLongerSupportedException $e) {
                throw new PublishingMessageFailed('Publisher was unable to prepare backward compatible package', previous: $e);
            }
        }

        try {
            $this->sendPackages($packages);
        } catch (DispatchMessagesStoppedException $e) {
            throw new PublishingMessageFailed('Publisher was unable to sent packages', previous: $e);
        }
    }

    /**
     * @throws PublishingMessageFailed
     */
    private function getRoutingKey(Message $message): RoutingKey
    {
        try {
            return $message->hasRoutingKey()
                ? $this->routingKeyGenerator->decorateKey($message->getRoutingKey())
                : $this->routingKeyGenerator->generateKey($message::class);
        } catch (CannotGenerateRoutingKeyException|InvalidRoutingKeyProvided $e) {
            throw new PublishingMessageFailed('Unable to get the routing key for the requested message', previous: $e);
        }
    }

    /**
     * @throws PublishingMessageFailed
     * @throws MessageIsNoLongerSupportedException
     */
    private function preparePackage(Message $message): Package
    {
        if (false === $this->supportedValidator->validate($message)) {
            throw new MessageIsNoLongerSupportedException($message);
        }

        $routingKey = $this->getRoutingKey($message);

        return new Package($message, $routingKey);
    }

    /**
     * @throws DispatchMessagesStoppedException
     */
    private function sendPackages(PackageCollection $packages): void
    {
        foreach ($packages as $package) {
            $envelope = new Envelope($package->message, [new AmqpStamp((string) $package->routingKey)]);

            try {
                $this->sendMessage($envelope, $package->routingKey);
            } catch (DispatchMessageStoppedException $e) {
                throw new DispatchMessagesStoppedException($packages->getRoutingKeysAsStringList(), previous: $e);
            }
        }
    }

    /**
     * @throws DispatchMessageStoppedException
     */
    private function sendMessage(Envelope $envelope, RoutingKey $routingKey): void
    {
        try {
            $this->bus->dispatch($envelope);
        } catch (ExceptionInterface $e) {
            throw new DispatchMessageStoppedException((string) $routingKey, previous: $e);
        }
    }
}
