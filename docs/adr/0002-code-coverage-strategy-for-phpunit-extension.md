# ADR 0002: Code Coverage Strategy for PHPUnit Extension Testing

## Status

Accepted

## Context

While preparing `phpunit-size-distribution` for OSS release, we needed to establish comprehensive code coverage measurement. The project has two types of tests:

1. **Unit tests** (`tests/Unit/`): Traditional unit tests for individual components
2. **Fixture tests** (`tests/Fixtures/`): Sample tests that exercise the extension during actual PHPUnit execution

Initial coverage measurement showed unexpectedly low line coverage (45.45%), primarily because Subscriber classes' `notify()` methods were not covered. These methods are only called by PHPUnit's event system during test execution, not by direct unit test invocation.

### Coverage Gaps Identified

| Component | Initial Coverage | Reason |
|-----------|-----------------|--------|
| TestPassedSubscriber::notify() | Low | Called by PHPUnit events during Fixture test execution |
| TestFailedSubscriber::notify() | 0% | No failing tests in Fixtures |
| TestErroredSubscriber::notify() | 0% | No erroring tests in Fixtures |
| ExecutionFinishedSubscriber::notify() | 0% | Called after coverage collection ends |
| TestSizeReporterExtension::bootstrap() | 0% | Called before coverage collection starts |

## Decision

We adopted a multi-layered approach to achieve comprehensive coverage:

### 1. Merge Coverage from Multiple Test Runs

**Decision:** Use `phpcov` to merge coverage data from Unit tests and Fixture tests

**Rationale:**

Coverage drivers (PCOV, Xdebug) only collect coverage for code executed within a single PHPUnit run. Since Subscriber::notify() methods are called by PHPUnit's event dispatcher during Fixture test execution, we need to:

1. Run Unit tests with coverage → `build/coverage/unit.cov`
2. Run Fixture tests with coverage → `build/coverage/fixtures.cov`
3. Merge both coverage files using `phpcov merge`

```json
{
  "scripts": {
    "coverage": [
      "@php -r \"@mkdir('build/coverage', 0777, true);\"",
      "phpunit --coverage-php build/coverage/unit.cov",
      "phpunit -c tests/Fixtures/phpunit.xml --coverage-php build/coverage/fixtures.cov || true",
      "phpcov merge build/coverage --clover build/coverage/clover.xml --text php://stdout"
    ]
  }
}
```

### 2. Add Intentionally Failing/Erroring Tests to Fixtures

**Decision:** Create test fixtures that intentionally fail or error to exercise TestFailedSubscriber and TestErroredSubscriber

**Implementation:**

```php
// tests/Fixtures/FailedTest.php
#[Small]
final class FailedTest extends TestCase
{
    public function testFailed(): void
    {
        $this->fail('Intentionally failed for coverage');
    }
}
```

### 3. Prevent "Risky Test" Classification for Erroring Tests

**Decision:** Use `#[DoesNotPerformAssertions]` attribute on tests that throw exceptions before any assertions

**Rationale:**

During investigation, we discovered that ErroredTest's coverage was not being recorded despite the merge strategy. Deep analysis of PHPUnit's source code revealed the root cause:

**PHPUnit's TestRunner.php (Lines 132-136, 150):**

```php
// Tests with no assertions are marked as "risky"
if ($this->configuration->reportUselessTests() &&
    !$test->doesNotPerformAssertions() &&
    $test->numberOfAssertionsPerformed() === 0) {
    $risky = true;
}

// Risky tests have their coverage discarded
$append = !$risky && !$incomplete && !$skipped;
```

When a test throws an exception before performing any assertions:
1. PHPUnit marks it as "risky" (useless test with no assertions)
2. Coverage is collected but `$append` is set to `false`
3. The coverage data is discarded, not merged into the final report

**Solution:**

```php
// tests/Fixtures/ErroredTest.php
#[Medium]
final class ErroredTest extends TestCase
{
    #[DoesNotPerformAssertions]  // Prevents "risky" classification
    public function testErrored(): void
    {
        throw new \RuntimeException('Intentionally errored for coverage');
    }
}
```

### 4. Unit Test ExecutionFinishedSubscriber Directly

**Decision:** Write unit tests that directly invoke notify() with mock/stub events

**Rationale:**

ExecutionFinishedSubscriber::notify() is called after PHPUnit's coverage collection ends, so it cannot be covered through Fixture tests. We test it directly using output buffering:

```php
#[Test]
public function notifyOutputsReport(): void
{
    $collector = new TestSizeCollector();
    $collector->incrementSmall();
    $reporter = new ConsoleReporter();
    $subscriber = new ExecutionFinishedSubscriber($collector, $reporter);

    $event = new ExecutionFinished($this->createTelemetryInfo());

    ob_start();
    $subscriber->notify($event);
    $output = ob_get_clean();

    $this->assertStringContainsString('Test Size Distribution', $output);
}
```

### 5. Accept Limitations for bootstrap() Testing

**Decision:** Test only method signature via reflection; rely on Fixture tests for integration coverage

**Rationale:**

TestSizeReporterExtension::bootstrap() cannot be unit tested directly because:
- `Configuration` is a final class with a massive constructor (100+ parameters)
- `Facade` is a final class that cannot be extended
- `EventFacade::instance()` is sealed after PHPUnit initialization

The bootstrap() method is effectively tested through Fixture tests, which exercise the full extension lifecycle.

## Investigation Timeline

1. **Initial measurement:** 45.45% line coverage
2. **Added phpcov merge:** Coverage improved to 58.44%
3. **Added FailedTest fixture:** TestFailedSubscriber coverage improved
4. **Added ErroredTest fixture:** TestErroredSubscriber coverage remained at 0%
5. **Tried Xdebug instead of PCOV:** Same result
6. **Added debug logging to php-code-coverage:** Discovered `Append: false` for ErroredTest
7. **Analyzed PHPUnit TestRunner.php:** Found "risky test" logic discarding coverage
8. **Added DoesNotPerformAssertions:** TestErroredSubscriber coverage recorded successfully
9. **Added ExecutionFinishedSubscriber unit test:** Coverage improved to 100%
10. **Final coverage:** 74.03% line coverage

## Consequences

### Positive

- Comprehensive coverage strategy documented for future maintainers
- Coverage improved from 45.45% to 74.03%
- ExecutionFinishedSubscriber now at 100% coverage
- Understanding of PHPUnit internals prevents future debugging sessions

### Negative

- Fixture tests must tolerate intentional failures/errors (`|| true` in script)
- TestSizeReporterExtension::bootstrap() has limited direct test coverage
- Coverage merge adds complexity to CI pipeline

### Lessons Learned

1. **PHPUnit discards coverage for "risky" tests** - Tests that throw exceptions without assertions lose their coverage data
2. **Extension bootstrap/shutdown code is hard to unit test** - PHPUnit's event system seals after initialization
3. **phpcov is essential for extension testing** - Coverage from multiple test configurations must be merged
4. **Final classes in PHPUnit limit testability** - Configuration, Facade, and event classes cannot be mocked

## References

- [PHPUnit Test Risky Configuration](https://docs.phpunit.de/en/11.5/risky-tests.html)
- [phpcov - CLI frontend for php-code-coverage](https://github.com/sebastianbergmann/phpcov)
- [DoesNotPerformAssertions Attribute](https://docs.phpunit.de/en/11.5/attributes.html#doesnotperformassertions)
- PHPUnit Source: `src/Framework/TestRunner/TestRunner.php` (Lines 132-150)
