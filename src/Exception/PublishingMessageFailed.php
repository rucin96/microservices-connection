<?php

declare(strict_types=1);

namespace Vehis\Msc\Exception;

final class PublishingMessageFailed extends MscException
{
    /**
     * @var string[]
     */
    public array $stoppedRoutingKeys;
}
