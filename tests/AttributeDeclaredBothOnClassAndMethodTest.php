<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Tests;

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[UseCassette("on-class.yml")]
class AttributeDeclaredBothOnClassAndMethodTest extends TestCase
{
    #[Test]
    #[UseCassette("on-methods.yml")]
    public function itUsesCassetteFromMethodWhenDeclaredOnBothPlaces(): void
    {
        $content = file_get_contents("https://example.com");

        $this->assertSame("Example body.", $content);
    }
}
