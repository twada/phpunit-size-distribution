# ADR 0001: PHPUnit Test Size Reporter Extension Design

## Status

Accepted

## Context

Maintaining healthy, sustainable test suites requires visibility into test composition. PHPUnit supports test size classification through `#[Small]`, `#[Medium]`, and `#[Large]` attributes, which affect execution timeouts and coverage reporting. However, there is no built-in way to measure and report the distribution of test sizes across a test suite.

Understanding test size distribution helps teams:
- Identify over-reliance on slow, large tests
- Track progress toward healthier test pyramids
- Make informed decisions about test refactoring

### Key PHPUnit Specifications

- Size attributes (`#[Small]`, `#[Medium]`, `#[Large]`) are **class-level only**
- All test methods in a class inherit the class's size
- DataProvider-generated test cases inherit the class's size

## Decision

We will develop `phpunit-size-ratio`, a PHPUnit extension that measures and reports test size distribution.

### Phase 1 Scope

**Included:**
- Count tests by size category (Small, Medium, Large, None)
- Report counts and percentages to stdout in text format
- Support PHPUnit 10.5+, 11.x, and 12.x
- Support PHP 8.1+

**Excluded (deferred to Phase 2+):**
- JSON/XML output formats
- File output
- Threshold checks for CI integration
- Test shape pattern detection (pyramid, trophy, etc.)

### Technical Decisions

#### 1. Test Size Detection Method

**Decision:** Use `PHPUnit\Metadata\Api\Groups::size(string $className, string $methodName): TestSize`

**Rationale:** This is the official PHPUnit API for retrieving test size metadata. It returns a `TestSize` object that provides reliable size classification.

#### 2. Event Subscription Strategy

**Decision:** Subscribe to `Test\Finished` event and filter by test outcome

**Rationale:**
- `Test\Finished` fires after every test completion, regardless of outcome
- Allows filtering out skipped and incomplete tests
- More accurate than `Test\Prepared` which fires before execution (would include subsequently skipped tests)

**Counting rules:**
- **Include:** Passed, Failed, Errored tests
- **Exclude:** Skipped, MarkedIncomplete tests

#### 3. Report Trigger

**Decision:** Output report on `TestRunner\ExecutionFinished` event

**Rationale:** This event fires once after all tests complete, ensuring the report appears at the end of test output.

#### 4. Architecture

```
TestSizeReporterExtension (Entry point)
├── TestSizeCollector (Aggregates test size data)
├── Subscriber/
│   ├── TestFinishedSubscriber (Captures each test's size)
│   └── ExecutionFinishedSubscriber (Triggers report output)
└── Reporter/
    └── ConsoleReporter (Formats text output)
```

### Output Format

```
Test Size Distribution
======================
Small:  45 tests (45.0%)
Medium: 30 tests (30.0%)
Large:  15 tests (15.0%)
None:   10 tests (10.0%)
----------------------
Total: 100 tests
```

### Package Information

- **Composer package:** `twada/phpunit-size-ratio`
- **Namespace:** `Twada\PhpunitSizeRatio`
- **PHP version:** 8.1+
- **PHPUnit versions:** 10.5+, 11.x, 12.x

## Consequences

### Positive

- Teams gain visibility into test size distribution
- Encourages adoption of test size attributes
- Foundation for future features (CI thresholds, pattern detection)
- Simple initial scope reduces development risk

### Negative

- Tests without size attributes are counted as "None" rather than flagged as warnings
- No immediate CI integration (Phase 2)
- Text-only output limits automation possibilities initially

### Risks

- PHPUnit internal API changes across major versions may require compatibility code
- `Groups::size()` method signature must be verified across supported PHPUnit versions

## References

- [PHPUnit Attributes Documentation](https://docs.phpunit.de/en/11.5/attributes.html)
- [PHPUnit Event System](https://docs.phpunit.de/en/11.5/extending-phpunit.html)
- [Test Pyramid concept](https://martinfowler.com/articles/practical-test-pyramid.html)
