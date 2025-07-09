<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Subscribers;

use PHPUnit\Event\Test\PreparedSubscriber;
use PHPUnit\Event\Test\Prepared;
use VCR\VCR;

/**
 * Ensures VCR is in a clean state before each test
 * This prevents issues when a test without cassette is followed by a test with cassette
 */
class CleanupState implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        // Always ensure VCR is in a clean state before each test
        try {
            // Eject any existing cassette
            VCR::eject();
        } catch (\Exception) {
            // Ignore if no cassette was inserted
        }
        
        try {
            // Turn off VCR to ensure clean state
            VCR::turnOff();
        } catch (\Exception) {
            // Ignore if VCR was already off
        }
    }
}
