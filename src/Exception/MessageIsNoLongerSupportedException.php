<?php

declare(strict_types=1);

namespace Vehis\Msc\Exception;

use Throwable;
use Vehis\Msc\Message\Message;

class MessageIsNoLongerSupportedException extends MscException
{
    public function __construct(Message $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Message `%s` is no longer supported. Valid to date is: %s', $message::class, $message->supportedTo()?->format(DATE_ATOM)),
            $code,
            $previous);
    }
}
