<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PhpunitSizeDistribution\Subscriber\ExecutionFinishedSubscriber;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

#[Small]
final class ExecutionFinishedSubscriberTest extends TestCase
{
    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $reporter = new ConsoleReporter();
        $subscriber = new ExecutionFinishedSubscriber($collector, $reporter);

        $this->assertInstanceOf(
            \PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber::class,
            $subscriber
        );
    }

    #[Test]
    public function notifyOutputsReport(): void
    {
        $collector = new TestSizeCollector();
        $collector->incrementSmall();
        $collector->incrementMedium();
        $reporter = new ConsoleReporter();
        $subscriber = new ExecutionFinishedSubscriber($collector, $reporter);

        $event = new ExecutionFinished($this->createTelemetryInfo());

        ob_start();
        $subscriber->notify($event);
        $output = ob_get_clean();
        $this->assertIsString($output);

        $this->assertStringContainsString('Test Size Distribution', $output);
        $this->assertStringContainsString('Small:', $output);
        $this->assertStringContainsString('Medium:', $output);
        $this->assertStringContainsString('Total:', $output);
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
