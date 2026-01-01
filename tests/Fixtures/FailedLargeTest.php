<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

#[Large]
final class FailedLargeTest extends TestCase
{
    public function testFailed(): void
    {
        $this->fail('This test is intentionally failed for coverage testing');
    }
}
