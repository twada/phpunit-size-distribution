<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Fixtures;

use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[Large]
final class LargeTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }
}
