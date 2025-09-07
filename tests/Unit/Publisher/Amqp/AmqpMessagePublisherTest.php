<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\Publisher\Amqp;

use DateTimeImmutable;
use Tests\Vehis\Msc\TestDoubles\Dummies\DummyMessageBus;
use Tests\Vehis\Msc\Unit\MscTestCase;
use Vehis\Msc\Exception\PublishingMessageFailed;
use Vehis\Msc\Generator\RoutingKeyGenerator;
use Vehis\Msc\Message\Validator\MessageSupportedValidator;
use Vehis\Msc\Publisher\Amqp\AmqpMessagePublisher;

class AmqpMessagePublisherTest extends MscTestCase
{
    private DummyMessageBus $dummyBus;
    private AmqpMessagePublisher $publisher;

    protected function setUp(): void
    {
        $this->dummyBus = new DummyMessageBus();
        $this->publisher = new AmqpMessagePublisher(
            $this->dummyBus,
            new RoutingKeyGenerator('test'),
            new MessageSupportedValidator(),
        );
    }

    protected function tearDown(): void
    {
        $this->dummyBus->reset();
    }

    public function testPublishSendsMessage(): void
    {
        $message = $this->getAnonymousMessage('event.name.v1');

        $this->publisher->publish($message);

        $publishedMessages = $this->dummyBus->getPublishedMessages();
        $this->assertCount(1, $publishedMessages);
        $this->assertSame($message, $publishedMessages[0]);
    }

    public function testPublishSendsMessageWithBackwardCompatible(): void
    {
        $previousMessage = $this->getAnonymousMessage('event.name.v1');
        $message = $this->getAnonymousMessageWithBackwardCompatible($previousMessage, 'event.name.v2');

        $this->publisher->publish($message);

        $publishedMessages = $this->dummyBus->getPublishedMessages();
        $this->assertCount(2, $publishedMessages);
        $this->assertSame($message, $publishedMessages[0]);
        $this->assertSame($publishedMessages[0]->getRoutingKey(), $message->getRoutingKey());
        $this->assertSame($previousMessage, $publishedMessages[1]);
        $this->assertSame($publishedMessages[1]->getRoutingKey(), $previousMessage->getRoutingKey());
    }

    public function testShouldThrowExceptionIfMessageIsNotSupported(): void
    {
        $message = $this->getAnonymousMessage('event.name.v1', new DateTimeImmutable('1999-01-01'));

        $this->expectException(PublishingMessageFailed::class);
        $this->expectExceptionMessage('Publisher was unable to prepare package');

        $this->publisher->publish($message);
    }

    public function testShouldThrowExceptionIfPreviousMessageIsNotSupported(): void
    {
        $previousMessage = $this->getAnonymousMessage('event.name.v1', new DateTimeImmutable('1999-01-01'));
        $message = $this->getAnonymousMessageWithBackwardCompatible($previousMessage, 'event.name.v2');

        $this->expectException(PublishingMessageFailed::class);
        $this->expectExceptionMessage('Publisher was unable to prepare backward compatible package');

        $this->publisher->publish($message);
    }

    public function testShouldThrowExceptionIfTransportWillFail(): void
    {
        $message = $this->getAnonymousMessage(DummyMessageBus::FAILING_MESSAGE_KEY);

        $this->expectException(PublishingMessageFailed::class);
        $this->expectExceptionMessage('Publisher was unable to sent packages');

        $this->publisher->publish($message);
    }
}
