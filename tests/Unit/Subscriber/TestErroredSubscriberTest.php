<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\Subscriber\TestErroredSubscriber;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

#[Small]
final class TestErroredSubscriberTest extends TestCase
{
    use CreatesTelemetryInfo;

    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestErroredSubscriber($collector);

        $this->assertInstanceOf(
            \PHPUnit\Event\Test\ErroredSubscriber::class,
            $subscriber
        );
    }

    #[Test]
    public function notifyIgnoresNonTestMethodTests(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestErroredSubscriber($collector);

        $phptTest = new Phpt('/path/to/test.phpt');
        $throwable = new Throwable(\RuntimeException::class, 'Test error', 'Test error', '', null);
        $event = new Errored($this->createTelemetryInfo(), $phptTest, $throwable);

        $subscriber->notify($event);

        $this->assertSame(0, $collector->getTotalCount());
    }
}
