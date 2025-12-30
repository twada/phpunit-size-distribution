<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution\Subscriber;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber as ExecutionFinishedSubscriberInterface;
use Twada\PhpunitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PhpunitSizeDistribution\TestSizeCollector;

final class ExecutionFinishedSubscriber implements ExecutionFinishedSubscriberInterface
{
    public function __construct(
        private readonly TestSizeCollector $collector,
        private readonly ConsoleReporter $reporter
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        print "\n\n" . $this->reporter->generate($this->collector) . "\n";
    }
}
