<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Subscriber;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use PHPUnit\Metadata\Api\Groups;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

/**
 * Subscriber that counts failed tests by their size attribute.
 *
 * This subscriber listens to Test\Failed events and increments the appropriate
 * size counter in the TestSizeCollector.
 */
final class TestFailedSubscriber implements FailedSubscriber
{
    /**
     * @param TestSizeCollector $collector The collector to record test sizes
     */
    public function __construct(
        private readonly TestSizeCollector $collector,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function notify(Failed $event): void
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
