<?php

declare(strict_types=1);

namespace Vehis\Msc\Message;

interface BackwardCompatibleInterface
{
    public function getPreviousVersion(): Message;
}
