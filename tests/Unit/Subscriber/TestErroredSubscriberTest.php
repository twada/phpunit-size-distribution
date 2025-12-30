<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Subscriber;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\Subscriber\TestErroredSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

#[Small]
final class TestErroredSubscriberTest extends TestCase
{
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
}
