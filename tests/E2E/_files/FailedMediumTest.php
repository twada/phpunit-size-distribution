<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\E2E;

use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;

#[Medium]
final class FailedMediumTest extends TestCase
{
    public function testFailed(): void
    {
        $this->fail('This test is intentionally failed for coverage testing');
    }
}
