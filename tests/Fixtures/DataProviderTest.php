<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\Fixtures;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class DataProviderTest extends TestCase
{
    #[DataProvider('provideData')]
    public function testWithProvider(int $value): void
    {
        $this->assertIsInt($value);
    }

    public static function provideData(): array
    {
        return [[1], [2], [3]];
    }
}
