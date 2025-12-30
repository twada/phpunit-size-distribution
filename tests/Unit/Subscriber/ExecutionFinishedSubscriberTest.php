<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Subscriber;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\Reporter\ConsoleReporter;
use Twada\PhpunitSizeRatio\Subscriber\ExecutionFinishedSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

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
}
