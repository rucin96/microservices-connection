<?php

declare(strict_types=1);

namespace Vehis\Msc\Message\Validator;

use DateTimeImmutable;
use Vehis\Msc\Message\Message;

class MessageSupportedValidator implements ValidatorInterface
{
    public function validate(Message $message): bool
    {
        $now = new DateTimeImmutable();
        $validTo = $message->supportedTo();

        if ($validTo === null) {
            return true;
        }

        return $now >= $validTo;
    }
}
