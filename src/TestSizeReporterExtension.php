<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeRatio;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Twada\PhpunitSizeRatio\Reporter\ConsoleReporter;
use Twada\PhpunitSizeRatio\Subscriber\ExecutionFinishedSubscriber;
use Twada\PhpunitSizeRatio\Subscriber\TestFinishedSubscriber;

final class TestSizeReporterExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters
    ): void {
        $collector = new TestSizeCollector();
        $reporter = new ConsoleReporter();

        $facade->registerSubscribers(
            new TestFinishedSubscriber($collector),
            new ExecutionFinishedSubscriber($collector, $reporter),
        );
    }
}
