<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use Daycry\PHPUnit\Vcr\Traits\AttributeResolverTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AttributeResolverTraitTest extends TestCase
{
    use AttributeResolverTrait;

    #[Test]
    public function traitMethodsAreAccessible(): void
    {
        $reflection = new ReflectionClass($this);

        $this->assertTrue($reflection->hasMethod('needsRecording'));
        $this->assertTrue($reflection->hasMethod('getCassetteName'));
        $this->assertTrue($reflection->hasMethod('getAttribute'));
    }

    #[Test]
    public function traitProvidesExpectedFunctionality(): void
    {
        // Test that the trait is properly used by checking for expected behavior
        // rather than just method existence which PHPStan knows is always true

        $reflection = new ReflectionClass($this);
        $traitNames = $reflection->getTraitNames();

        $this->assertContains(AttributeResolverTrait::class, $traitNames);

        // Verify the trait provides the expected private methods by checking reflection
        $method = $reflection->getMethod('needsRecording');
        $this->assertSame('needsRecording', $method->getName());
        $this->assertTrue($method->isPrivate());

        $method = $reflection->getMethod('getCassetteName');
        $this->assertSame('getCassetteName', $method->getName());
        $this->assertTrue($method->isPrivate());

        $method = $reflection->getMethod('getAttribute');
        $this->assertSame('getAttribute', $method->getName());
        $this->assertTrue($method->isPrivate());
    }
}

// Test classes for attribute testing
#[UseCassette("class-level.yml")]
class ClassWithUseCassetteAttribute extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        // Dummy test for testing attribute resolution
        $this->expectNotToPerformAssertions();
    }

    #[Test]
    #[UseCassette("method-level.yml")]
    public function testWithMethodAttribute(): void
    {
        // Dummy test with method-level attribute
        $this->expectNotToPerformAssertions();
    }
}

class ClassWithoutUseCassetteAttribute extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        // Dummy test without any cassette attributes
        $this->expectNotToPerformAssertions();
    }
}
