<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Small]
final class ErroredSmallTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testErrored(): void
    {
        throw new RuntimeException('This test intentionally throws an exception for coverage testing');
    }
}
