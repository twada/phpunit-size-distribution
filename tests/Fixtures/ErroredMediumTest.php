<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Medium]
final class ErroredMediumTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testErrored(): void
    {
        throw new RuntimeException('This test intentionally throws an exception for coverage testing');
    }
}
