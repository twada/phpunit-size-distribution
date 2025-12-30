<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

#[Small]
final class TestSizeCollectorTest extends TestCase
{
    #[Test]
    public function initialStateHasZeroCounts(): void
    {
        $collector = new TestSizeCollector();

        $this->assertSame(0, $collector->getSmallCount());
        $this->assertSame(0, $collector->getMediumCount());
        $this->assertSame(0, $collector->getLargeCount());
        $this->assertSame(0, $collector->getNoneCount());
        $this->assertSame(0, $collector->getTotalCount());
    }

    #[Test]
    public function incrementSmallIncreasesSmallCount(): void
    {
        $collector = new TestSizeCollector();

        $collector->incrementSmall();

        $this->assertSame(1, $collector->getSmallCount());
        $this->assertSame(1, $collector->getTotalCount());
    }

    #[Test]
    public function incrementMediumIncreasesMediumCount(): void
    {
        $collector = new TestSizeCollector();

        $collector->incrementMedium();

        $this->assertSame(1, $collector->getMediumCount());
        $this->assertSame(1, $collector->getTotalCount());
    }

    #[Test]
    public function incrementLargeIncreasesLargeCount(): void
    {
        $collector = new TestSizeCollector();

        $collector->incrementLarge();

        $this->assertSame(1, $collector->getLargeCount());
        $this->assertSame(1, $collector->getTotalCount());
    }

    #[Test]
    public function incrementNoneIncreasesNoneCount(): void
    {
        $collector = new TestSizeCollector();

        $collector->incrementNone();

        $this->assertSame(1, $collector->getNoneCount());
        $this->assertSame(1, $collector->getTotalCount());
    }

    #[Test]
    public function totalCountSumsAllCategories(): void
    {
        $collector = new TestSizeCollector();

        $collector->incrementSmall();
        $collector->incrementSmall();
        $collector->incrementMedium();
        $collector->incrementLarge();
        $collector->incrementNone();

        $this->assertSame(2, $collector->getSmallCount());
        $this->assertSame(1, $collector->getMediumCount());
        $this->assertSame(1, $collector->getLargeCount());
        $this->assertSame(1, $collector->getNoneCount());
        $this->assertSame(5, $collector->getTotalCount());
    }
}
