<?php

declare(strict_types=1);

namespace Twada\PHPUnitSizeDistribution;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Twada\PHPUnitSizeDistribution\Reporter\ConsoleReporter;
use Twada\PHPUnitSizeDistribution\Subscriber\ExecutionFinishedSubscriber;
use Twada\PHPUnitSizeDistribution\Subscriber\TestErroredSubscriber;
use Twada\PHPUnitSizeDistribution\Subscriber\TestFailedSubscriber;
use Twada\PHPUnitSizeDistribution\Subscriber\TestPassedSubscriber;

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
