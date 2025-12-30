<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Fixtures;

use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;

#[Medium]
final class MediumTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }
}
