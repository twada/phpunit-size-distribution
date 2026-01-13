<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Twada\PHPUnitSizeDistribution\Subscriber\TestFailedSubscriber;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

#[Small]
final class TestFailedSubscriberTest extends TestCase
{
    use CreatesTelemetryInfo;

    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestFailedSubscriber($collector);

        $this->assertInstanceOf(
            \PHPUnit\Event\Test\FailedSubscriber::class,
            $subscriber,
        );
    }

    #[Test]
    public function notifyIgnoresNonTestMethodTests(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestFailedSubscriber($collector);

        $phptTest = new Phpt('/path/to/test.phpt');
        $throwable = new Throwable(RuntimeException::class, 'Test error', 'Test error', '', null);
        $event = new Failed($this->createTelemetryInfo(), $phptTest, $throwable, null);

        $subscriber->notify($event);

        $this->assertSame(0, $collector->getTotalCount());
    }
}
