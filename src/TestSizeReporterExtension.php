<?php

declare(strict_types=1);

namespace Twada\PhpunitSizeDistribution;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Twada\PhpunitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PhpunitSizeDistribution\Subscriber\ExecutionFinishedSubscriber;
use Twada\PhpunitSizeDistribution\Subscriber\TestErroredSubscriber;
use Twada\PhpunitSizeDistribution\Subscriber\TestFailedSubscriber;
use Twada\PhpunitSizeDistribution\Subscriber\TestPassedSubscriber;

final class TestSizeReporterExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        $collector = new TestSizeCollector();
        $reporter = new ConsoleReporter();

        $facade->registerSubscribers(
            new TestPassedSubscriber($collector),
            new TestFailedSubscriber($collector),
            new TestErroredSubscriber($collector),
            new ExecutionFinishedSubscriber($collector, $reporter),
        );
    }
}
