<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\TestCase;

final class FailedNoSizeTest extends TestCase
{
    public function testFailed(): void
    {
        $this->fail('This test is intentionally failed for coverage testing');
    }
}
