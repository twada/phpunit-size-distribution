--TEST--
Test Size Distribution report is correctly output
--FILE--
<?php
$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--no-progress';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = __DIR__ . '/_files/phpunit.xml';

require __DIR__ . '/../../vendor/autoload.php';

(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);
?>
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/tests/E2E/_files/phpunit.xml



Test Size Distribution
======================
Small:   7 tests ( 43.8%)
Medium:  3 tests ( 18.8%)
Large:   3 tests ( 18.8%)
None:    3 tests ( 18.8%)
----------------------
Total:  16 tests
Time: %s, Memory: %s

There were 4 errors:

1) Twada\PHPUnitSizeDistribution\Tests\E2E\ErroredLargeTest::testErrored
RuntimeException: This test intentionally throws an exception for coverage testing

%s/tests/E2E/_files/ErroredLargeTest.php:%d

2) Twada\PHPUnitSizeDistribution\Tests\E2E\ErroredMediumTest::testErrored
RuntimeException: This test intentionally throws an exception for coverage testing

%s/tests/E2E/_files/ErroredMediumTest.php:%d

3) Twada\PHPUnitSizeDistribution\Tests\E2E\ErroredNoSizeTest::testErrored
RuntimeException: This test intentionally throws an exception for coverage testing

%s/tests/E2E/_files/ErroredNoSizeTest.php:%d

4) Twada\PHPUnitSizeDistribution\Tests\E2E\ErroredSmallTest::testErrored
RuntimeException: This test intentionally throws an exception for coverage testing

%s/tests/E2E/_files/ErroredSmallTest.php:%d

--

There were 4 failures:

1) Twada\PHPUnitSizeDistribution\Tests\E2E\FailedLargeTest::testFailed
This test is intentionally failed for coverage testing

%s/tests/E2E/_files/FailedLargeTest.php:%d

2) Twada\PHPUnitSizeDistribution\Tests\E2E\FailedMediumTest::testFailed
This test is intentionally failed for coverage testing

%s/tests/E2E/_files/FailedMediumTest.php:%d

3) Twada\PHPUnitSizeDistribution\Tests\E2E\FailedNoSizeTest::testFailed
This test is intentionally failed for coverage testing

%s/tests/E2E/_files/FailedNoSizeTest.php:%d

4) Twada\PHPUnitSizeDistribution\Tests\E2E\FailedSmallTest::testFailed
This test is intentionally failed for coverage testing

%s/tests/E2E/_files/FailedSmallTest.php:%d

ERRORS!
Tests: 18, Assertions: 12, Errors: 4, Failures: 4, Skipped: 2.
