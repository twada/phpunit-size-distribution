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

### 2. Add Intentionally Failing/Erroring Tests for All Sizes

**Decision:** Create test fixtures that intentionally fail or error for each test size (Small, Medium, Large, None) to exercise all branches in TestFailedSubscriber and TestErroredSubscriber

**Rationale:**

Each Subscriber's `notify()` method has four branches based on test size:

```php
if ($size->isSmall()) {
    $this->collector->incrementSmall();
} elseif ($size->isMedium()) {
    $this->collector->incrementMedium();
} elseif ($size->isLarge()) {
    $this->collector->incrementLarge();
} else {
    $this->collector->incrementNone();
}
```

To cover all branches, we need fixtures for each size:

| Fixture | Size | Purpose |
|---------|------|---------|
| FailedSmallTest | Small | Cover isSmall() branch in TestFailedSubscriber |
| FailedMediumTest | Medium | Cover isMedium() branch |
| FailedLargeTest | Large | Cover isLarge() branch |
| FailedNoSizeTest | None | Cover else branch |
| ErroredSmallTest | Small | Cover isSmall() branch in TestErroredSubscriber |
| ErroredMediumTest | Medium | Cover isMedium() branch |
| ErroredLargeTest | Large | Cover isLarge() branch |
| ErroredNoSizeTest | None | Cover else branch |

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
// tests/Fixtures/ErroredMediumTest.php
#[Medium]
final class ErroredMediumTest extends TestCase
{
    #[DoesNotPerformAssertions]  // Prevents "risky" classification
    public function testErrored(): void
    {
        throw new \RuntimeException('Intentionally errored for coverage');
    }
}
```

### 4. Unit Test Subscribers with Phpt Objects for Early Return Coverage

**Decision:** Write unit tests that invoke notify() with `Phpt` objects to cover the early return branch

**Rationale:**

Each Subscriber has an early return for non-TestMethod tests:

```php
if (!$test instanceof TestMethod) {
    return;
}
```

This branch is triggered when PHPT tests (`.phpt` files) are executed, as they use `Phpt` objects instead of `TestMethod`. Rather than adding PHPT fixtures (which would add complexity and noise to test results), we test this directly:

```php
#[Test]
public function notifyIgnoresNonTestMethodTests(): void
{
    $collector = new TestSizeCollector();
    $subscriber = new TestPassedSubscriber($collector);

    $phptTest = new Phpt('/path/to/test.phpt');
    $event = new Passed($this->createTelemetryInfo(), $phptTest);

    $subscriber->notify($event);

    $this->assertSame(0, $collector->getTotalCount());
}
```

This approach is preferred over PHPT fixtures because:
- Unit tests provide direct, isolated coverage
- No test count mismatch in reports (PHPT tests would not appear in Test Size Distribution)
- Simpler test infrastructure

### 5. Unit Test ExecutionFinishedSubscriber Directly

**Decision:** Write unit tests that directly invoke notify() with real event objects

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

### 6. Share Test Utilities via Trait

**Decision:** Extract common test helper methods into a shared trait

**Rationale:**

The `createTelemetryInfo()` method was duplicated across four Subscriber test classes. We extracted it into `CreatesTelemetryInfo` trait:

```php
trait CreatesTelemetryInfo
{
    private function createTelemetryInfo(): Info
    {
        $gcStatus = new GarbageCollectorStatus(0, 0, 0, 0, 0.0, 0.0, 0.0, 0.0, false, false, false, 0);
        $memoryUsage = MemoryUsage::fromBytes(0);
        $hrTime = HRTime::fromSecondsAndNanoseconds(0, 0);
        $snapshot = new Snapshot($hrTime, $memoryUsage, $memoryUsage, $gcStatus);
        $duration = Duration::fromSecondsAndNanoseconds(0, 0);

        return new Info($snapshot, $duration, $memoryUsage, $duration, $memoryUsage);
    }
}
```

### 7. Accept Limitations for bootstrap() Testing

**Decision:** Rely solely on Fixture tests for bootstrap() coverage; no unit tests

**Rationale:**

TestSizeReporterExtension::bootstrap() cannot be unit tested directly because:
- `Configuration` is a final class with a massive constructor (100+ parameters)
- `Facade` is a final class that cannot be extended
- `EventFacade::instance()` is sealed after PHPUnit initialization

Testing only the method signature via reflection was considered but rejected as it provides no real value—implementing the `Extension` interface already guarantees the correct signature at compile time.

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
9. **Added ExecutionFinishedSubscriber unit test:** Coverage improved to 74.03%
10. **Expanded Failed/Errored fixtures to all sizes:** Coverage improved to 85.71%
11. **Added Phpt-based unit tests for early return:** All Subscribers reached 100%
12. **Removed PHPT fixtures:** Unit tests sufficient, cleaner test output
13. **Extracted CreatesTelemetryInfo trait:** Reduced code duplication
14. **Removed redundant bootstrap signature test:** Interface guarantees signature
15. **Final coverage:** 89.61% line coverage (69/77 lines)

## Final Coverage Results

| Component | Coverage |
|-----------|----------|
| ConsoleReporter | 100% |
| TestSizeCollector | 100% |
| TestPassedSubscriber | 100% |
| TestFailedSubscriber | 100% |
| TestErroredSubscriber | 100% |
| ExecutionFinishedSubscriber | 100% |
| TestSizeReporterExtension | 0% (bootstrap untestable) |
| **Overall** | **89.61%** |

## Consequences

### Positive

- Comprehensive coverage strategy documented for future maintainers
- Coverage improved from 45.45% to 89.61%
- All Subscriber classes at 100% coverage
- Understanding of PHPUnit internals prevents future debugging sessions
- Clean separation between unit tests and integration tests (Fixtures)

### Negative

- Fixture tests must tolerate intentional failures/errors (`|| true` in script)
- TestSizeReporterExtension::bootstrap() cannot be unit tested (0% direct coverage)
- Coverage merge adds complexity to CI pipeline

### Lessons Learned

1. **PHPUnit discards coverage for "risky" tests** - Tests that throw exceptions without assertions lose their coverage data
2. **Extension bootstrap/shutdown code is hard to unit test** - PHPUnit's event system seals after initialization
3. **phpcov is essential for extension testing** - Coverage from multiple test configurations must be merged
4. **Final classes in PHPUnit limit testability** - Configuration, Facade, and event classes cannot be mocked
5. **Prefer unit tests over integration fixtures for edge cases** - Testing Phpt handling via unit tests is cleaner than adding PHPT fixtures
6. **Test size fixtures need all sizes** - Each size (Small, Medium, Large, None) requires separate fixtures to cover all branches

## Related Decisions

- [ADR 0009: E2E Testing Strategy with PHPT](./0009-e2e-testing-with-phpt.md) - Migrated fixture tests to PHPT-based E2E tests

## References

- [PHPUnit Test Risky Configuration](https://docs.phpunit.de/en/11.5/risky-tests.html)
- [phpcov - CLI frontend for php-code-coverage](https://github.com/sebastianbergmann/phpcov)
- [DoesNotPerformAssertions Attribute](https://docs.phpunit.de/en/11.5/attributes.html#doesnotperformassertions)
- PHPUnit Source: `src/Framework/TestRunner/TestRunner.php` (Lines 132-150)
