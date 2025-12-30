<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Reporter;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

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

    #[Test]
    public function reportWithMixedTests(): void
    {
        $collector = new TestSizeCollector();
        $collector->incrementSmall();
        $collector->incrementSmall();
        $collector->incrementSmall();
        $collector->incrementSmall();
        $collector->incrementSmall();
        $collector->incrementMedium();
        $collector->incrementMedium();
        $collector->incrementMedium();
        $collector->incrementLarge();
        $collector->incrementNone();

        $reporter = new ConsoleReporter();

        $output = $reporter->generate($collector);

        $expected = <<<'TEXT'
Test Size Distribution
======================
Small:   5 tests ( 50.0%)
Medium:  3 tests ( 30.0%)
Large:   1 tests ( 10.0%)
None:    1 tests ( 10.0%)
----------------------
Total:  10 tests
TEXT;

        $this->assertSame($expected, $output);
    }
}
