<?php

declare(strict_types=1);

namespace Vehis\Msc\Message\Validator;

use Vehis\Msc\Message\Message;

interface ValidatorInterface
{
    public function validate(Message $message): bool;
}
