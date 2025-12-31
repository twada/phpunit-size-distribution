<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use ReflectionClass;
use ReflectionNamedType;
use Twada\PhpunitSizeDistribution\TestSizeReporterExtension;

#[Small]
final class TestSizeReporterExtensionTest extends TestCase
{
    #[Test]
    public function extensionImplementsCorrectInterface(): void
    {
        $extension = new TestSizeReporterExtension();

        $this->assertInstanceOf(Extension::class, $extension);
    }

    #[Test]
    public function bootstrapMethodHasCorrectSignature(): void
    {
        $reflection = new ReflectionClass(TestSizeReporterExtension::class);
        $method = $reflection->getMethod('bootstrap');

        $this->assertTrue($method->isPublic());

        $returnType = $method->getReturnType();
        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame('void', $returnType->getName());

        $parameters = $method->getParameters();
        $this->assertCount(3, $parameters);

        $param0Type = $parameters[0]->getType();
        $this->assertInstanceOf(ReflectionNamedType::class, $param0Type);
        $this->assertSame(Configuration::class, $param0Type->getName());

        $param1Type = $parameters[1]->getType();
        $this->assertInstanceOf(ReflectionNamedType::class, $param1Type);
        $this->assertSame(Facade::class, $param1Type->getName());

        $param2Type = $parameters[2]->getType();
        $this->assertInstanceOf(ReflectionNamedType::class, $param2Type);
        $this->assertSame(ParameterCollection::class, $param2Type->getName());
    }
}
