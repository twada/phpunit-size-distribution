<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio;

final class TestSizeCollector
{
    private int $smallCount = 0;
    private int $mediumCount = 0;
    private int $largeCount = 0;

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

    public function getNoneCount(): int
    {
        return 0;
    }

    public function getTotalCount(): int
    {
        return $this->smallCount + $this->mediumCount + $this->largeCount;
    }
}
