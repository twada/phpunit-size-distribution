<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeDistribution\Subscriber\TestPassedSubscriber;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

#[Small]
final class TestPassedSubscriberTest extends TestCase
{
    use CreatesTelemetryInfo;

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
}
