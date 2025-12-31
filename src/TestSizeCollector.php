<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution;

final class TestSizeCollector
{
    private int $smallCount = 0;
    private int $mediumCount = 0;
    private int $largeCount = 0;
    private int $noneCount = 0;

    public function incrementSmall(): void
    {
        $this->smallCount++;
    }

    public function incrementMedium(): void
    {
        $this->mediumCount++;
    }

    public function getSmallCount(): int
    {
        return $this->smallCount;
    }

    public function getMediumCount(): int
    {
        return $this->mediumCount;
    }

    public function incrementLarge(): void
    {
        $this->largeCount++;
    }

    public function getLargeCount(): int
    {
        return $this->largeCount;
    }

    public function incrementNone(): void
    {
        $this->noneCount++;
    }

    public function getNoneCount(): int
    {
        return $this->noneCount;
    }

    public function getTotalCount(): int
    {
        return $this->smallCount + $this->mediumCount + $this->largeCount + $this->noneCount;
    }
}
