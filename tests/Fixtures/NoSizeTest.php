<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Fixtures;

use PHPUnit\Framework\TestCase;

final class NoSizeTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }
}
