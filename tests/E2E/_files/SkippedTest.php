<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\E2E;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class SkippedTest extends TestCase
{
    public function testSkipped(): void
    {
        $this->markTestSkipped('This test is intentionally skipped');
    }

    public function testAnotherSkipped(): void
    {
        $this->markTestSkipped('This test is also skipped');
    }
}
