# ADR 0009: E2E Testing Strategy with PHPT

## Status

Accepted

## Context

The existing test infrastructure had a gap: Fixture-based integration tests were only executed in the `coverage` job (PHP 8.3 only), not in the CI matrix (11 patterns covering PHP 8.1-8.4 and PHPUnit 10.5/11.x/12.x). This meant the extension's actual behavior across different PHP/PHPUnit versions was not verified.

### Problem

- `tests/Fixtures/` tests were executed via a separate `phpunit.xml` configuration
- CI matrix ran only Unit tests (`vendor/bin/phpunit`)
- No verification that the extension's output format works correctly across all supported versions

### PHPUnit's E2E Testing Approach

PHPUnit itself uses PHPT format tests in `tests/end-to-end/` to verify CLI behavior. Key characteristics:

1. **PHPT format**: Standard PHP test format with `--TEST--`, `--FILE--`, and `--EXPECTF--` sections
2. **Output verification**: Tests compare actual stdout output against expected patterns
3. **Failure handling**: Internal test failures/errors don't affect PHPT test result—only output matching matters
4. **Flexible matching**: `%s`, `%d`, `%i` placeholders absorb version-specific variations

Example from PHPUnit's `failure.phpt`:
```
--TEST--
phpunit ../../_files/FailureTest.php
--FILE--
<?php
$_SERVER['argv'][] = '--do-not-cache-result';
...
(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);
--EXPECTF--
...13 test failures with detailed output...
ERRORS!
Tests: 13, Assertions: 15, Failures: 13.
```

## Decision

### 1. Adopt PHPT Format for E2E Testing

**Decision:** Create E2E tests using PHPT format in `tests/E2E/`

**Rationale:**

- PHPT is PHPUnit's own approach for E2E testing
- Automatically included in test matrix via standard `vendor/bin/phpunit`
- Output comparison naturally handles test failures/errors as expected behavior
- Placeholders (`%s`, `%d`) absorb PHPUnit version differences

### 2. Migrate Fixtures to E2E Directory

**Decision:** Move all fixture tests from `tests/Fixtures/` to `tests/E2E/_files/`

**Structure:**
```
tests/E2E/
├── _files/
│   ├── phpunit.xml           # E2E PHPUnit config with extension enabled
│   ├── SmallTest.php         # Test fixtures (all sizes)
│   ├── MediumTest.php
│   ├── LargeTest.php
│   ├── NoSizeTest.php
│   ├── SkippedTest.php
│   ├── DataProviderTest.php
│   ├── FailedSmallTest.php   # Intentional failures (all sizes)
│   ├── FailedMediumTest.php
│   ├── FailedLargeTest.php
│   ├── FailedNoSizeTest.php
│   ├── ErroredSmallTest.php  # Intentional errors (all sizes)
│   ├── ErroredMediumTest.php
│   ├── ErroredLargeTest.php
│   └── ErroredNoSizeTest.php
└── report-output.phpt        # PHPT test verifying extension output
```

### 3. Include All Fixture Types in E2E Tests

**Decision:** Include failing and erroring tests in E2E fixtures, not just passing tests

**Rationale:**

PHPT tests verify output, not exit codes. PHPUnit's own E2E tests (e.g., `failure.phpt`, `exception-stack.phpt`) demonstrate that internal test failures are valid test scenarios. Our extension must correctly count and report all test outcomes:

- Passed tests: counted by TestPassedSubscriber
- Failed tests: counted by TestFailedSubscriber
- Errored tests: counted by TestErroredSubscriber
- Skipped tests: excluded from count (by design)

### 4. Configure E2E Testsuite in phpunit.xml.dist

**Decision:** Add E2E testsuite with `.phpt` suffix and `_files` exclusion

```xml
<testsuite name="E2E">
    <directory suffix=".phpt">tests/E2E</directory>
    <exclude>tests/E2E/_files</exclude>
</testsuite>
```

**Rationale:**

- `suffix=".phpt"` ensures only PHPT files are treated as tests
- `<exclude>` prevents fixture PHP files from being executed directly
- E2E tests now run automatically with `vendor/bin/phpunit`

### 5. Update Coverage Strategy

**Decision:** Merge Unit and E2E coverage using existing phpcov approach

```json
{
  "coverage": [
    "phpunit --testsuite Unit --coverage-php build/coverage/unit.cov",
    "phpunit --testsuite E2E --coverage-php build/coverage/e2e.cov || true",
    "phpcov merge build/coverage --clover build/coverage/clover.xml"
  ]
}
```

**Rationale:**

PHPT tests run `(new PHPUnit\TextUI\Application)->run()` in the same process, so coverage from the inner PHPUnit execution is captured by the outer coverage driver. This maintains the coverage merge strategy from ADR 0002.

## Consequences

### Positive

- E2E tests now run in all 11 CI matrix configurations
- Extension output verified across PHP 8.1-8.4 and PHPUnit 10.5/11.x/12.x
- Follows PHPUnit's own testing patterns
- Single `vendor/bin/phpunit` command runs both Unit and E2E tests
- No fixture duplication—same files used for E2E testing and coverage

### Negative

- PHPT format is less familiar to developers than standard PHPUnit tests
- `--EXPECTF--` patterns must be maintained when output format changes
- PHPUnit version differences may require placeholder adjustments

### Migration Summary

| Before | After |
|--------|-------|
| `tests/Fixtures/*.php` | `tests/E2E/_files/*.php` |
| `tests/Fixtures/phpunit.xml` | `tests/E2E/_files/phpunit.xml` |
| Run via `phpunit -c tests/Fixtures/phpunit.xml` | Run via `vendor/bin/phpunit --testsuite E2E` |
| Coverage job only (PHP 8.3) | All CI matrix patterns |

## References

- [PHPT File Format](https://qa.php.net/phpt_details.php)
- [PHPUnit E2E Tests](https://github.com/sebastianbergmann/phpunit/tree/main/tests/end-to-end)
- [ADR 0002: Code Coverage Strategy](./0002-code-coverage-strategy-for-phpunit-extension.md)
- [ADR 0003: CI Matrix Strategy](./0003-ci-matrix-strategy.md)
