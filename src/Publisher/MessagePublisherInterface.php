<?php

declare(strict_types=1);

namespace Vehis\Msc\Publisher;

use Vehis\Msc\Exception\PublishingMessageFailed;
use Vehis\Msc\Message\Message;

interface MessagePublisherInterface
{
    /**
     * @throws PublishingMessageFailed
     */
    public function publish(Message $message): void;
}
