<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test to verify that VCR state is properly cleaned between tests
 * This addresses the issue where a test without cassette followed by a test with cassette doesn't work
 */
class MixedCassetteUsageTest extends TestCase
{
    #[Test]
    public function testWithoutCassette(): void
    {
        // This test intentionally does NOT use a cassette
        // It should not interfere with subsequent tests
        $this->expectNotToPerformAssertions();
    }

    #[Test]
    #[UseCassette("mixed-usage.yml")]
    public function testWithCassetteAfterNormalTest(): void
    {
        // This test uses a cassette and should work correctly
        // even though the previous test didn't use one

        $content = file_get_contents("https://example.com");
        $this->assertSame("Example body for \"https://example.com\"", $content);
    }

    #[Test]
    public function testWithoutCassetteAgain(): void
    {
        // Another test without cassette
        $this->expectNotToPerformAssertions();
    }

    #[Test]
    #[UseCassette("mixed-usage-2.yml")]
    public function testWithCassetteAfterNormalTestAgain(): void
    {
        // This should also work correctly
        $content = file_get_contents("https://httpbin.org/json");
        $this->assertIsString($content);
        $this->assertStringContainsString("slideshow", $content);
    }
}
