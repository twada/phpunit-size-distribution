<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Subscriber;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;
use PHPUnit\Metadata\Api\Groups;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

final class TestPassedSubscriber implements PassedSubscriber
{
    public function __construct(
        private readonly TestSizeCollector $collector,
    ) {}

    public function notify(Passed $event): void
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
