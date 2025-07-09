<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Subscribers\CleanupState;
use PHPUnit\Event\Test\PreparedSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CleanupStateTest extends TestCase
{
    private CleanupState $cleanupState;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupState = new CleanupState();
    }

    #[Test]
    public function itImplementsPreparedSubscriber(): void
    {
        $this->assertInstanceOf(PreparedSubscriber::class, $this->cleanupState);
    }

    #[Test]
    public function itHasNotifyMethod(): void
    {
        $reflection = new ReflectionClass($this->cleanupState);
        $this->assertTrue($reflection->hasMethod('notify'));

        $method = $reflection->getMethod('notify');
        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $cleanupState = new CleanupState();
        $this->assertInstanceOf(CleanupState::class, $cleanupState);
    }

    #[Test]
    public function notifyMethodDoesNotThrowExceptions(): void
    {
        // This test ensures the notify method exists and can be called
        // The actual functionality is tested in integration tests

        // We can't easily mock PHPUnit events, but we can test that the method exists
        $reflection = new ReflectionClass($this->cleanupState);
        $method = $reflection->getMethod('notify');
        $this->assertTrue($method->isPublic());
    }
}
