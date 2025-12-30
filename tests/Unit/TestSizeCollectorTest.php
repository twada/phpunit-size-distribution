<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\TestSizeCollector;

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
}
