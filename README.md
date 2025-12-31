# phpunit-size-distribution

A PHPUnit extension that measures and reports test size distribution (Small/Medium/Large).

## Overview

PHPUnit supports test size classification through `#[Small]`, `#[Medium]`, and `#[Large]` attributes. This extension analyzes your test suite and reports the distribution of test sizes, helping you maintain a healthy test pyramid.

## Requirements

- PHP 8.1+
- PHPUnit 10.5+ / 11.x / 12.x

## Installation

```bash
composer require --dev twada/phpunit-size-distribution
```

## Configuration

Register the extension in your `phpunit.xml`:

```xml
<phpunit>
    <!-- ... -->
    <extensions>
        <bootstrap class="Twada\PhpunitSizeDistribution\TestSizeReporterExtension"/>
    </extensions>
</phpunit>
```

## Usage

Run your tests as usual:

```bash
vendor/bin/phpunit
```

After test execution, you'll see a report like this:

```
Test Size Distribution
======================
Small:   5 tests ( 62.5%)
Medium:  1 tests ( 12.5%)
Large:   1 tests ( 12.5%)
None:    1 tests ( 12.5%)
----------------------
Total:   8 tests
```

## Test Size Categories

| Category | Description |
|----------|-------------|
| Small | Tests marked with `#[Small]` attribute |
| Medium | Tests marked with `#[Medium]` attribute |
| Large | Tests marked with `#[Large]` attribute |
| None | Tests without any size attribute |

### Example

```php
<?php

use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class UserTest extends TestCase
{
    public function testName(): void
    {
        // This test is counted as "Small"
    }
}
```

## Counting Rules

- **Counted:** Passed, Failed, and Errored tests
- **Not counted:** Skipped and Incomplete tests

## License

MIT License. See [LICENSE](LICENSE) for details.

## Author

Takuto Wada
