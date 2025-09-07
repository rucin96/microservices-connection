<?php

declare(strict_types=1);

namespace Tests\Vehis\Msc\TestDoubles\Dummies\Message;

use Vehis\Msc\Message\Message;

class DummyEvent extends Message
{
    public function __construct(public ?string $id = null)
    {
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(4)));
    }
}
