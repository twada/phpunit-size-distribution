<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Reporter;

use Twada\PHPUnitSizeDistribution\TestSizeCollector;

final class ConsoleReporter
{
    public function generate(TestSizeCollector $collector): string
    {
        $total = $collector->getTotalCount();
        $smallCount = $collector->getSmallCount();
        $mediumCount = $collector->getMediumCount();
        $largeCount = $collector->getLargeCount();
        $noneCount = $collector->getNoneCount();

        $smallPercent = $total > 0 ? ($smallCount / $total) * 100 : 0.0;
        $mediumPercent = $total > 0 ? ($mediumCount / $total) * 100 : 0.0;
        $largePercent = $total > 0 ? ($largeCount / $total) * 100 : 0.0;
        $nonePercent = $total > 0 ? ($noneCount / $total) * 100 : 0.0;

        return sprintf(
            <<<'TEXT'
Test Size Distribution
======================
Small:  %2d tests (%5.1f%%)
Medium: %2d tests (%5.1f%%)
Large:  %2d tests (%5.1f%%)
None:   %2d tests (%5.1f%%)
----------------------
Total:  %2d tests
TEXT,
            $smallCount,
            $smallPercent,
            $mediumCount,
            $mediumPercent,
            $largeCount,
            $largePercent,
            $noneCount,
            $nonePercent,
            $total,
        );
    }
}
