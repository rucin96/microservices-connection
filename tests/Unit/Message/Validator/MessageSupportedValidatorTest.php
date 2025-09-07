<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\Unit\Message\Validator;

use DateTimeImmutable;
use Tests\Vehis\Msc\Unit\MscTestCase;
use Vehis\Msc\Message\Validator\MessageSupportedValidator;

class MessageSupportedValidatorTest extends MscTestCase
{
    public function testValidateReturnsTrueIfSupportedToIsNull(): void
    {
        $message = $this->getAnonymousMessage(supportedTo: null);
        $validator = new MessageSupportedValidator();

        $result = $validator->validate($message);

        $this->assertTrue($result);
    }

    public function testValidateReturnsTrueIfSupportedToIsInFuture(): void
    {
        $future = (new DateTimeImmutable())->modify('+1 day');
        $message = $this->getAnonymousMessage(supportedTo: $future);
        $validator = new MessageSupportedValidator();

        $result = $validator->validate($message);

        $this->assertTrue($result);
    }

    public function testValidateReturnsFalseIfSupportedToIsInPast(): void
    {
        $past = (new DateTimeImmutable())->modify('-1 day');
        $message = $this->getAnonymousMessage(supportedTo: $past);
        $validator = new MessageSupportedValidator();

        $result = $validator->validate($message);

        $this->assertFalse($result);
    }
}
