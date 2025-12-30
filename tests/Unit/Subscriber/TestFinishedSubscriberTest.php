<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Tests\Unit\Subscriber;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Api\Groups;
use Twada\PhpunitSizeRatio\Subscriber\TestFinishedSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

#[Small]
final class TestFinishedSubscriberTest extends TestCase
{
    #[Test]
    public function subscriberImplementsCorrectInterface(): void
    {
        $collector = new TestSizeCollector();
        $subscriber = new TestFinishedSubscriber($collector);

        $this->assertInstanceOf(
            \PHPUnit\Event\Test\FinishedSubscriber::class,
            $subscriber
        );
    }
}
