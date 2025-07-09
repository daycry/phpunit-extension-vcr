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
    public function needsRecordingMethodExists(): void
    {
        $this->assertTrue(method_exists($this, 'needsRecording'));
    }

    #[Test]
    public function getCassetteNameMethodExists(): void
    {
        $this->assertTrue(method_exists($this, 'getCassetteName'));
    }

    #[Test]
    public function getAttributeMethodExists(): void
    {
        $this->assertTrue(method_exists($this, 'getAttribute'));
    }
}

// Test classes for attribute testing
#[UseCassette("class-level.yml")]
class ClassWithUseCassetteAttribute extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        $this->assertTrue(true);
    }

    #[Test]
    #[UseCassette("method-level.yml")]
    public function testWithMethodAttribute(): void
    {
        $this->assertTrue(true);
    }
}

class ClassWithoutUseCassetteAttribute extends TestCase
{
    #[Test]
    public function someTest(): void
    {
        $this->assertTrue(true);
    }
}
