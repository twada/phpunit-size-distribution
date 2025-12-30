<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Reporter;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\Reporter\ConsoleReporter;
use Twada\PhpunitSizeRatio\TestSizeCollector;

#[Small]
final class ConsoleReporterTest extends TestCase
{
    #[Test]
    public function reportWithZeroTests(): void
    {
        $collector = new TestSizeCollector();
        $reporter = new ConsoleReporter();

        $output = $reporter->generate($collector);

        $expected = <<<'TEXT'
Test Size Distribution
======================
Small:   0 tests (  0.0%)
Medium:  0 tests (  0.0%)
Large:   0 tests (  0.0%)
None:    0 tests (  0.0%)
----------------------
Total:   0 tests
TEXT;

        $this->assertSame($expected, $output);
    }
}
