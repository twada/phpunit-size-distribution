<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Subscriber;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber as ExecutionFinishedSubscriberInterface;
use Twada\PHPUnitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

/**
 * Subscriber that outputs the test size distribution report after test execution.
 *
 * This subscriber listens to TestRunner\ExecutionFinished events and outputs
 * the collected test size statistics to the console.
 */
final class ExecutionFinishedSubscriber implements ExecutionFinishedSubscriberInterface
{
    /**
     * @param TestSizeCollector $collector The collector containing test size statistics
     * @param ConsoleReporter   $reporter  The reporter to generate the output
     */
    public function __construct(
        private readonly TestSizeCollector $collector,
        private readonly ConsoleReporter $reporter,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function notify(ExecutionFinished $event): void
    {
        print "\n\n" . $this->reporter->generate($this->collector) . "\n";
    }
}
