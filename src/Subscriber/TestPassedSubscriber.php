<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Subscriber;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;
use PHPUnit\Metadata\Api\Groups;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

/**
 * Subscriber that counts passed tests by their size attribute.
 *
 * This subscriber listens to Test\Passed events and increments the appropriate
 * size counter in the TestSizeCollector.
 */
final class TestPassedSubscriber implements PassedSubscriber
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
