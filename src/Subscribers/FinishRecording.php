<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Subscribers;

use Daycry\PHPUnit\Vcr\Traits\AttributeResolverTrait;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use VCR\VCR;

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
