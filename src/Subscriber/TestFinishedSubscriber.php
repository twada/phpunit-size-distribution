<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio\Subscriber;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Metadata\Api\Groups;
use Twada\PhpunitSizeRatio\TestSizeCollector;

final class TestFinishedSubscriber implements FinishedSubscriber
{
    public function __construct(
        private readonly TestSizeCollector $collector
    ) {
    }

    public function notify(Finished $event): void
    {
        $test = $event->test();

        if (!$test instanceof TestMethod) {
            return;
        }

        $size = (new Groups())->size($test->className(), $test->methodName());

        if ($size->isSmall()) {
            $this->collector->incrementSmall();
        } elseif ($size->isMedium()) {
            $this->collector->incrementMedium();
        } elseif ($size->isLarge()) {
            $this->collector->incrementLarge();
        } else {
            $this->collector->incrementNone();
        }
    }
}
