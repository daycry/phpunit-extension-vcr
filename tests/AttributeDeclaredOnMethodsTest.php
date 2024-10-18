<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AttributeDeclaredOnMethodsTest extends TestCase
{
    #[Test]
    #[UseCassette("on-methods.yml")]
    public function itUsesVcrOnMethodsWithAttribute(): void
    {
        $content = file_get_contents("https://example.com");

        $this->assertSame("Example body.", $content);
    }

    #[Test]
    #[UseCassette("with-data-provider.yml")]
    #[DataProvider("urls")]
    public function itUsesVcrOnMethodsWithDataProvider(string $url): void
    {
        $content = file_get_contents($url);

        $this->assertSame(sprintf("Example body for \"%s\"", $url), $content);
    }

    /** @return iterable<list<string>> */
    public static function urls(): iterable
    {
        yield ["https://example.com"];
        yield ["https://example.org"];
    }
}
