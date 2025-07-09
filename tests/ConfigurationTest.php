<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Subscribers\Configuration;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConfigurationTest extends TestCase
{
    #[Test]
    public function itImplementsExecutionStartedSubscriber(): void
    {
        $configuration = new Configuration(null, null, null, null, null, null, null);

        $this->assertInstanceOf(ExecutionStartedSubscriber::class, $configuration);
    }

    #[Test]
    public function itCanBeInstantiatedWithAllNullParameters(): void
    {
        $configuration = new Configuration(null, null, null, null, null, null, null);
        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    #[Test]
    public function itCanBeInstantiatedWithAllParameters(): void
    {
        $configuration = new Configuration(
            'tests/fixtures',
            'yaml',
            ['curl', 'stream_wrapper'],
            ['method', 'url'],
            ['/allowed'],
            ['/blocked'],
            'new_episodes'
        );

        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    #[Test]
    public function itHasNotifyMethod(): void
    {
        $configuration = new Configuration(null, null, null, null, null, null, null);

        $reflection = new ReflectionClass($configuration);
        $this->assertTrue($reflection->hasMethod('notify'));

        $method = $reflection->getMethod('notify');
        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
    }

    #[Test]
    public function itAcceptsEmptyArrays(): void
    {
        $configuration = new Configuration(
            '',
            '',
            [],
            [],
            [],
            [],
            ''
        );

        $this->assertInstanceOf(Configuration::class, $configuration);
    }
}
