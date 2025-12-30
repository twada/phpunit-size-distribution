<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Subscriber;

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twada\PhpunitSizeRatio\Subscriber\TestPassedSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

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
}
