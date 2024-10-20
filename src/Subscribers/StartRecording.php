<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Subscribers;

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;
use VCR\VCR;
use Daycry\PHPUnit\Vcr\Traits\AttributeResolverTrait;

class StartRecording implements PreparedSubscriber
{
    use AttributeResolverTrait;

    public function notify(Prepared $event): void
    {
        $test = $event->test();

        if (!$this->needsRecording($test)) {
            return;
        }

        $cassetteName = $this->getCassetteName($test);
        assert($cassetteName !== null);

        VCR::turnOn();
        VCR::insertCassette($cassetteName);
    }
}
