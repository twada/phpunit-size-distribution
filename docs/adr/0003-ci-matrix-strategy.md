# ADR 0003: CI Matrix Strategy for Multi-Version PHP and PHPUnit Support

## Status

Accepted

## Context

This project supports multiple PHP versions (8.1+) and PHPUnit versions (10.5+, 11.x, 12.x). To ensure compatibility across all supported combinations, we implemented a CI matrix that tests various PHP and PHPUnit version pairs.

However, the initial CI configuration encountered dependency resolution failures in 8 out of 10 matrix combinations during the "Install dependencies" step.

### Root Cause Analysis

The failures were caused by two distinct issues:

#### Issue 1: phpcov Version Constraints

The `require-dev` section specified `phpunit/phpcov: ^11.0`, but phpcov 11.x has strict requirements:
- PHP >= 8.3
- PHPUnit ^12.0

This caused failures for:
- PHP 8.1/8.2: phpcov 11.x requires PHP >= 8.3
- PHPUnit 10.5/11.5: phpcov 11.x requires PHPUnit ^12.0

#### Issue 2: PHPUnit PHP Version Requirements

PHPUnit versions have minimum PHP version requirements:
- PHPUnit 10.x: PHP >= 8.1
- PHPUnit 11.x: PHP >= 8.2
- PHPUnit 12.x: PHP >= 8.3

The matrix included invalid combinations (e.g., PHP 8.2 with PHPUnit 12.1).

### phpcov Version Compatibility

Investigation revealed that phpcov versions are tightly coupled to PHPUnit major versions:

| phpcov | Required PHP | Required PHPUnit |
|--------|--------------|------------------|
| 9.x | >= 8.1 | ^10.0 |
| 10.x | >= 8.2 | ^11.0 |
| 11.x | >= 8.3 | ^12.0 |

## Decision

### 1. Broaden phpcov Version Constraint

**Decision:** Change phpcov constraint from `^11.0` to `^9.0 || ^10.0 || ^11.0`

**Rationale:**

By allowing multiple major versions, Composer automatically selects the appropriate phpcov version based on the installed PHPUnit version:
- PHPUnit 10.x environments get phpcov 9.x
- PHPUnit 11.x environments get phpcov 10.x
- PHPUnit 12.x environments get phpcov 11.x

This approach was preferred over removing phpcov from `composer.json` because:
- Local development environments can run `composer coverage` regardless of PHP/PHPUnit version
- No need for separate phpcov installation in CI
- Single source of truth for dependencies

### 2. Exclude Invalid PHP/PHPUnit Combinations

**Decision:** Add explicit exclusions to the CI matrix for unsupported combinations

```yaml
exclude:
  # PHPUnit 11.x requires PHP 8.2+
  - php-version: '8.1'
    phpunit-version: '11.5'
  # PHPUnit 12.x requires PHP 8.3+
  - php-version: '8.1'
    phpunit-version: '12.1'
  - php-version: '8.2'
    phpunit-version: '12.1'
```

**Rationale:**

Explicit exclusions are clearer and more maintainable than complex include rules. The comments document why each exclusion exists.

### 3. Add PHP 8.5 to Test Matrix

**Decision:** Include PHP 8.5 (released November 2025) in the CI matrix

**Rationale:**

- Ensures early detection of compatibility issues with the latest PHP version
- PHP 8.5 meets all PHPUnit version requirements, so no additional exclusions needed
- Aligns with the project's goal of supporting current PHP versions

### 4. CI Workflow Optimizations

**Decision:** Run test jobs with `--no-coverage` flag and code-style job on PHP 8.3

**Rationale:**

- Coverage collection adds overhead; only the dedicated coverage job needs it
- PHP 8.3 is a stable, well-supported version for static analysis tools

## Final CI Matrix

| PHP | PHPUnit 10.5 | PHPUnit 11.5 | PHPUnit 12.1 |
|-----|:------------:|:------------:|:------------:|
| 8.1 | ✅ | ❌ | ❌ |
| 8.2 | ✅ | ✅ | ❌ |
| 8.3 | ✅ | ✅ | ✅ |
| 8.4 | ✅ | ✅ | ✅ |
| 8.5 | ✅ | ✅ | ✅ |

**Total: 11 test combinations**

## Consequences

### Positive

- All CI matrix combinations now pass dependency resolution
- Automatic phpcov version selection simplifies maintenance
- PHP 8.5 compatibility is continuously verified
- Clear documentation of version requirements via matrix exclusions

### Negative

- Multiple phpcov major versions may have slightly different interfaces (not observed in practice)
- Matrix exclusions must be updated when adding new PHP or PHPUnit versions

### Maintenance Notes

When adding new PHPUnit major versions:
1. Check PHP version requirements and add exclusions if needed
2. Verify phpcov compatibility; add new major version to constraint if available

When adding new PHP versions:
1. Check if it meets minimum requirements for all PHPUnit versions in matrix
2. Add exclusions for incompatible PHPUnit versions if needed

## Related Decisions

- [ADR 0009: E2E Testing Strategy with PHPT](./0009-e2e-testing-with-phpt.md) - E2E tests now run in all CI matrix configurations

## References

- [PHPUnit Version Support](https://phpunit.de/supported-versions.html)
- [phpcov on Packagist](https://packagist.org/packages/phpunit/phpcov)
- [Composer Version Constraints](https://getcomposer.org/doc/articles/versions.md)
