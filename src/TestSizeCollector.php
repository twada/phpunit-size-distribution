<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution;

/**
 * Collects and aggregates test size statistics.
 *
 * This class maintains counters for each test size category (Small, Medium, Large, None)
 * and provides methods to increment and retrieve these counts.
 */
final class TestSizeCollector
{
    /** @var int Count of tests marked with #[Small] attribute */
    private int $smallCount = 0;

    /** @var int Count of tests marked with #[Medium] attribute */
    private int $mediumCount = 0;

    /** @var int Count of tests marked with #[Large] attribute */
    private int $largeCount = 0;

    /** @var int Count of tests without any size attribute */
    private int $noneCount = 0;

    /**
     * Increments the counter for Small tests.
     */
    public function incrementSmall(): void
    {
        $this->smallCount++;
    }

    /**
     * Returns the count of Small tests.
     */
    public function getSmallCount(): int
    {
        return $this->smallCount;
    }

    /**
     * Increments the counter for Medium tests.
     */
    public function incrementMedium(): void
    {
        $this->mediumCount++;
    }

    /**
     * Returns the count of Medium tests.
     */
    public function getMediumCount(): int
    {
        return $this->mediumCount;
    }

    /**
     * Increments the counter for Large tests.
     */
    public function incrementLarge(): void
    {
        $this->largeCount++;
    }

    /**
     * Returns the count of Large tests.
     */
    public function getLargeCount(): int
    {
        return $this->largeCount;
    }

    /**
     * Increments the counter for tests without size attribute.
     */
    public function incrementNone(): void
    {
        $this->noneCount++;
    }

    /**
     * Returns the count of tests without size attribute.
     */
    public function getNoneCount(): int
    {
        return $this->noneCount;
    }

    /**
     * Returns the total count of all tests.
     */
    public function getTotalCount(): int
    {
        return $this->smallCount + $this->mediumCount + $this->largeCount + $this->noneCount;
    }
}
