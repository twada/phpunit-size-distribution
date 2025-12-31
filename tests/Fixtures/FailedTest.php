<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class FailedTest extends TestCase
{
    public function testFailed(): void
    {
        $this->fail('This test is intentionally failed for coverage testing');
    }
}
