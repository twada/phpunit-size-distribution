<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Subscriber;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use Twada\PhpunitSizeRatio\TestSizeCollector;

final class TestFinishedSubscriber implements FinishedSubscriber
{
    public function __construct(
        private readonly TestSizeCollector $collector
    ) {
    }

    public function notify(Finished $event): void
    {
    }
}
