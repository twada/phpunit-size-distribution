<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution\Subscriber;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber as ExecutionFinishedSubscriberInterface;
use Twada\PHPUnitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PHPUnitSizeDistribution\TestSizeCollector;

final class ExecutionFinishedSubscriber implements ExecutionFinishedSubscriberInterface
{
    public function __construct(
        private readonly TestSizeCollector $collector,
        private readonly ConsoleReporter $reporter,
    ) {}

    public function notify(ExecutionFinished $event): void
    {
        print "\n\n" . $this->reporter->generate($this->collector) . "\n";
    }
}
