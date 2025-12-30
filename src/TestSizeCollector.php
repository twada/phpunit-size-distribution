<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio;

final class TestSizeCollector
{
    private int $smallCount = 0;

    public function incrementSmall(): void
    {
        $this->smallCount++;
    }

    public function getSmallCount(): int
    {
        return $this->smallCount;
    }

    public function getMediumCount(): int
    {
        return 0;
    }

    public function getLargeCount(): int
    {
        return 0;
    }

    public function getNoneCount(): int
    {
        return 0;
    }

    public function getTotalCount(): int
    {
        return $this->smallCount;
    }
}
