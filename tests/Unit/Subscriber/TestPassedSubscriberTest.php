<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\Subscriber\TestPassedSubscriber;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

#[Small]
final class TestPassedSubscriberTest extends TestCase
{
    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestPassedSubscriber($collector);

        $this->assertInstanceOf(
            \PHPUnit\Event\Test\PassedSubscriber::class,
            $subscriber
        );
    }

    #[Test]
    public function notifyIgnoresNonTestMethodTests(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestPassedSubscriber($collector);

        $phptTest = new Phpt('/path/to/test.phpt');
        $event = new Passed($this->createTelemetryInfo(), $phptTest);

        $subscriber->notify($event);

        $this->assertSame(0, $collector->getTotalCount());
    }

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
