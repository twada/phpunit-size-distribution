<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Tests\Unit\Subscriber;

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
}
