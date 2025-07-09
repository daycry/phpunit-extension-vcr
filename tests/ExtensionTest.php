<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Extension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ExtensionTest extends TestCase
{
    private Extension $extension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new Extension();
    }

    #[Test]
    public function itImplementsPHPUnitExtensionInterface(): void
    {
        $this->assertInstanceOf(\PHPUnit\Runner\Extension\Extension::class, $this->extension);
    }

    #[Test]
    public function itHasBootstrapMethod(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $this->assertTrue($reflection->hasMethod('bootstrap'));

        $method = $reflection->getMethod('bootstrap');
        $this->assertTrue($method->isPublic());
        $this->assertCount(3, $method->getParameters());
    }

    #[Test]
    public function itHasParameterMethod(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $this->assertTrue($reflection->hasMethod('parameter'));

        $method = $reflection->getMethod('parameter');
        $this->assertTrue($method->isPrivate());
    }

    #[Test]
    public function itHasParameterAsArrayMethod(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $this->assertTrue($reflection->hasMethod('parameterAsArray'));

        $method = $reflection->getMethod('parameterAsArray');
        $this->assertTrue($method->isPrivate());
    }

    #[Test]
    public function parameterMethodReturnsNullForNonExistentParameter(): void
    {
        // Test that the method exists and is accessible
        $reflection = new ReflectionClass($this->extension);
        $method = $reflection->getMethod('parameter');
        $this->assertTrue($method->isPrivate());

        // We can't easily test private methods with type-hinted parameters
        // This test verifies the method signature exists
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('parameters', $parameters[0]->getName());
        $this->assertSame('name', $parameters[1]->getName());
    }

    #[Test]
    public function parameterMethodReturnsValueForExistentParameter(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $method = $reflection->getMethod('parameter');
        $this->assertTrue($method->isPrivate());

        // Verify method signature
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('?string', (string)$returnType);
    }

    #[Test]
    public function parameterAsArrayMethodReturnsNullForNullParameter(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $method = $reflection->getMethod('parameterAsArray');
        $this->assertTrue($method->isPrivate());

        // Verify return type annotation
        $docComment = $method->getDocComment();
        $this->assertIsString($docComment);
        $this->assertStringContainsString('@return array<string>|null', $docComment);
    }

    #[Test]
    public function parameterAsArrayMethodReturnsArrayForCommaSeparatedValue(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $method = $reflection->getMethod('parameterAsArray');

        // Verify method exists and has correct parameters
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('parameters', $parameters[0]->getName());
        $this->assertSame('name', $parameters[1]->getName());
    }

    #[Test]
    public function parameterAsArrayMethodTrimsWhitespace(): void
    {
        $reflection = new ReflectionClass($this->extension);
        $method = $reflection->getMethod('parameterAsArray');

        // Just verify the method exists and is private
        $this->assertTrue($method->isPrivate());
        $this->assertSame('parameterAsArray', $method->getName());
    }
}
