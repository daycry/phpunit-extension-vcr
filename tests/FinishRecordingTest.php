<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Subscribers\FinishRecording;
use Daycry\PHPUnit\Vcr\Traits\AttributeResolverTrait;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FinishRecordingTest extends TestCase
{
    private FinishRecording $finishRecording;

    protected function setUp(): void
    {
        parent::setUp();
        $this->finishRecording = new FinishRecording();
    }

    #[Test]
    public function itImplementsFinishedSubscriber(): void
    {
        $this->assertInstanceOf(FinishedSubscriber::class, $this->finishRecording);
    }

    #[Test]
    public function itUsesAttributeResolverTrait(): void
    {
        $reflection = new ReflectionClass($this->finishRecording);
        $traits = $reflection->getTraitNames();

        $this->assertContains(AttributeResolverTrait::class, $traits);
    }

    #[Test]
    public function itHasNotifyMethod(): void
    {
        $reflection = new ReflectionClass($this->finishRecording);
        $this->assertTrue($reflection->hasMethod('notify'));

        $method = $reflection->getMethod('notify');
        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
    }

    #[Test]
    public function itHasMethodsFromTrait(): void
    {
        $reflection = new ReflectionClass($this->finishRecording);

        // Methods that should be available from AttributeResolverTrait
        $this->assertTrue($reflection->hasMethod('needsRecording'));
        $this->assertTrue($reflection->hasMethod('getCassetteName'));
        $this->assertTrue($reflection->hasMethod('getAttribute'));
    }
}
