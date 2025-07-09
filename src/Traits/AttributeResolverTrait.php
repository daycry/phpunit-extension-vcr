<?php

declare(strict_types=1);

namespace Daycry\PHPUnit\Vcr\Traits;

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use Exception;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use ReflectionClass;
use ReflectionMethod;

trait AttributeResolverTrait
{
    private function needsRecording(Test $test): bool
    {
        return $this->getAttribute($test) !== null;
    }

    private function getCassetteName(Test $test): ?string
    {
        return $this->getAttribute($test)?->cassette;
    }

    private function getAttribute(Test $test): ?UseCassette
    {
        $reflection = new ReflectionClass($test);
        $class = $reflection->getProperty('className')->getValue($test);

        $method = $test instanceof TestMethod ? $test->methodName() : $test->name();

        try {
            $method = new ReflectionMethod($class, $method);
        } catch (Exception) {
            return null;
        }

        $attributes = $method->getAttributes(UseCassette::class);

        if ($attributes !== []) {
            return $attributes[0]->newInstance();
        } else {
            $class = $method->getDeclaringClass();
            $attributes = $class->getAttributes(UseCassette::class);

            if ($attributes) {
                return $attributes[0]->newInstance();
            }

            return null;
        }

    }
}
