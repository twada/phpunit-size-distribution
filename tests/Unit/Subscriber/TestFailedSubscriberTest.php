<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Subscriber;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\Subscriber\TestFailedSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

#[Small]
final class TestFailedSubscriberTest extends TestCase
{
    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestFailedSubscriber($collector);

        $this->assertInstanceOf(
            \PHPUnit\Event\Test\FailedSubscriber::class,
            $subscriber
        );
    }
}
