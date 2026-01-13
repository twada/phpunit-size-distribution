<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PHPUnitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PHPUnitSizeDistribution\Subscriber\ExecutionFinishedSubscriber;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

#[Small]
final class ExecutionFinishedSubscriberTest extends TestCase
{
    use CreatesTelemetryInfo;

    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $reporter = new ConsoleReporter();
        $subscriber = new ExecutionFinishedSubscriber($collector, $reporter);

        $this->assertInstanceOf(
            \PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber::class,
            $subscriber,
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
}
