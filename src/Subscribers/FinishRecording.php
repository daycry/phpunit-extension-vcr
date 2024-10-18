<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Subscribers;

use PHPUnit\Event\Test\FinishedSubscriber;
use VCR\VCR;
use Daycry\PHPUnit\Vcr\Traits\AttributeResolverTrait;
use PHPUnit\Event\Test\Finished;

class FinishRecording implements FinishedSubscriber
{
    use AttributeResolverTrait;

    public function notify(Finished $event): void
    {
        $test = $event->test();

        if (!$this->needsRecording($test)) {
            return;
        }

        VCR::eject();
        VCR::turnOff();
    }
}
