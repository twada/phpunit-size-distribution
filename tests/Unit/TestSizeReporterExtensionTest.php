<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Extension\Extension;
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
}
