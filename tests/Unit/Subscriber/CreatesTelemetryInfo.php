<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;

trait CreatesTelemetryInfo
{
    private function createTelemetryInfo(): Info
    {
        $gcStatus = new GarbageCollectorStatus(0, 0, 0, 0, 0.0, 0.0, 0.0, 0.0, false, false, false, 0);
        $memoryUsage = MemoryUsage::fromBytes(0);
        $hrTime = HRTime::fromSecondsAndNanoseconds(0, 0);
        $snapshot = new Snapshot($hrTime, $memoryUsage, $memoryUsage, $gcStatus);
        $duration = Duration::fromSecondsAndNanoseconds(0, 0);

        return new Info($snapshot, $duration, $memoryUsage, $duration, $memoryUsage);
    }
}
