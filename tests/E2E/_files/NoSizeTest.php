<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\E2E;

use PHPUnit\Framework\TestCase;

final class NoSizeTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }
}
