# Contributing to phpunit-size-distribution

Thank you for your interest in contributing to phpunit-size-distribution! This document provides guidelines and instructions for contributing.

## Code of Conduct

This project adheres to the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## How to Contribute

### Reporting Bugs

Please use the [bug report template](.github/ISSUE_TEMPLATE/bug_report.md) when reporting bugs. Include:

- PHP version
- PHPUnit version
- Steps to reproduce
- Expected vs actual behavior

### Suggesting Features

Please use the [feature request template](.github/ISSUE_TEMPLATE/feature_request.md) for feature suggestions.

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes following the guidelines below
4. Commit using [Conventional Commits](#commit-messages)
5. Push to your fork and submit a Pull Request

## Development Setup

### Requirements

- PHP 8.1+
- Composer

### Installation

```bash
git clone https://github.com/twada/phpunit-size-distribution.git
cd phpunit-size-distribution
composer install
```

## Development Commands

```bash
# Run all tests
vendor/bin/phpunit

# Run tests without coverage
vendor/bin/phpunit --no-coverage

# Run a single test file
vendor/bin/phpunit tests/Unit/TestSizeCollectorTest.php

# Run a single test method
vendor/bin/phpunit --filter testMethodName

# Run fixture tests (integration tests)
vendor/bin/phpunit -c tests/Fixtures/phpunit.xml

# Code coverage (merges Unit + Fixture tests)
composer coverage

# Static analysis (PHPStan level 8)
composer analyse

# Code style fix
composer cs-fix

# Code style check
composer cs-check

# Validate distribution package
composer validate-dist
```

## Development Workflow

This project follows **Test-Driven Development (TDD)**:

1. **Red**: Write a failing test first
2. **Green**: Write the minimum code to make the test pass
3. **Refactor**: Improve the code while keeping tests green

## Testing Strategy

- **Unit tests (`tests/Unit/`)**: Direct component testing with mocks
- **Fixture tests (`tests/Fixtures/`)**: Integration tests that run actual PHPUnit tests through the extension
- **Coverage merging**: Two test suites are run separately and merged with phpcov

## Commit Messages

This project uses [Conventional Commits](https://www.conventionalcommits.org/). Each commit message should follow this format:

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types

- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation only changes
- `style`: Changes that do not affect the meaning of the code
- `refactor`: A code change that neither fixes a bug nor adds a feature
- `test`: Adding missing tests or correcting existing tests
- `chore`: Changes to the build process or auxiliary tools

### Examples

```
feat: add JSON output format support
fix: handle empty test suites gracefully
docs: add troubleshooting section to README
refactor: extract size detection logic to separate method
test: add tests for edge cases in ConsoleReporter
chore: update PHPStan to version 2.0
```

## Code Quality Requirements

Before submitting a Pull Request, ensure:

- [ ] All tests pass (`vendor/bin/phpunit`)
- [ ] Static analysis passes (`composer analyse`)
- [ ] Code style is correct (`composer cs-check`)
- [ ] Distribution package is valid (`composer validate-dist`)

## Architecture

For architectural decisions and design rationale, see the [ADR documentation](docs/adr/).

```
TestSizeReporterExtension (Entry point, registers subscribers)
├── TestSizeCollector (Aggregates test size data, shared state)
├── Subscriber/
│   ├── TestPassedSubscriber   ─┐
│   ├── TestFailedSubscriber   ─┼─ Count tests by size via Groups::size() API
│   ├── TestErroredSubscriber  ─┘
│   └── ExecutionFinishedSubscriber (Triggers report on test run completion)
└── Reporter/
    └── ConsoleReporter (Formats text output to stdout)
```

## Releasing (for Maintainers)

This project uses automated release workflow. When a version tag is pushed, GitHub Actions validates the CHANGELOG, runs tests, and creates a GitHub Release automatically.

### Release Process

1. **Update CHANGELOG.md**
   - Change `[Unreleased]` to `[X.Y.Z] - YYYY-MM-DD`
   - Add a new `[Unreleased]` section at the top for future changes

2. **Commit and push**
   ```bash
   git commit -am "chore(release): prepare vX.Y.Z"
   git push
   ```

3. **Create and push tag**
   ```bash
   git tag vX.Y.Z
   git push origin vX.Y.Z
   ```

4. **Automated steps** (handled by CI)
   - Validates CHANGELOG.md has entry for the version
   - Runs tests on all PHP/PHPUnit combinations
   - Runs PHPStan and code style checks
   - Creates GitHub Release with release notes from CHANGELOG
   - Packagist automatically detects the new tag

### If Release Fails

If the release workflow fails (e.g., CHANGELOG not updated):

```bash
git tag -d vX.Y.Z              # Delete local tag
git push origin :vX.Y.Z        # Delete remote tag
# Fix the issue, commit, push, then re-tag
```

## Questions?

If you have questions, feel free to open an issue for discussion.
