# ADR 0004: Distribution Package Validation Strategy

## Status

Accepted

## Context

When preparing `phpunit-size-distribution` for publication on Packagist, we needed to control which files are included in the distribution package. Users installing via Composer should only receive files necessary for using the library, not development files like tests, CI configurations, or documentation.

### Comparison with npm

In the npm ecosystem, `package.json` provides a `files` field that acts as an **allowlist**—only specified files are included in the published package. This approach is safe by default: new files are excluded unless explicitly added.

```json
{
  "files": ["src", "LICENSE", "README.md"]
}
```

### Composer's Approach

Composer uses a fundamentally different mechanism. It relies on Git's archive functionality, which respects `.gitattributes` `export-ignore` directives. This is a **blocklist** approach—all tracked files are included unless explicitly excluded.

```gitattributes
/tests export-ignore
/docs export-ignore
/.github export-ignore
```

This approach is similar to Bower (a now-deprecated JavaScript package manager), which also used Git repositories directly.

### Risk of Blocklist Approach

The blocklist approach introduces a risk: when adding new files to the repository, developers may forget to add corresponding `export-ignore` directives. This can lead to:

- Unnecessary files in distribution (tests, CI configs, documentation)
- Increased package download size
- Potential exposure of internal development files

## Decision

We adopted a two-part strategy to ensure lean distribution packages:

### 1. Define `.gitattributes` with `export-ignore` Directives

**Decision:** Create a `.gitattributes` file that excludes all development-related files

```gitattributes
# Git/GitHub
/.github export-ignore
/.gitattributes export-ignore
/.gitignore export-ignore

# Documentation
/docs export-ignore

# Tests
/tests export-ignore
/phpunit.xml.dist export-ignore

# Static analysis and code style
/phpstan.neon export-ignore
/.php-cs-fixer.dist.php export-ignore
```

**Resulting distribution contents:**
- `src/` - Source code
- `composer.json` - Package metadata
- `LICENSE` - License file
- `README.md` - User documentation

### 2. Integrate lean-package-validator in CI

**Decision:** Use `stolt/lean-package-validator` to automatically detect when development files slip into the distribution archive

**Rationale:**

We evaluated several approaches to mitigate the blocklist risk:

| Approach | Pros | Cons |
|----------|------|------|
| lean-package-validator | Comprehensive, maintained, has presets | Additional dev dependency |
| Manual shell script | No dependencies, full allowlist control | Maintenance burden |
| Pre-commit hook | Real-time warning | Requires setup per developer |

**lean-package-validator was selected** because:
- It is actively maintained and widely used in the PHP ecosystem
- Provides `--validate-git-archive` option to inspect actual archive contents
- Integrates easily with Composer scripts and CI
- Supports PHP presets for common exclusion patterns

**Implementation:**

```json
{
  "require-dev": {
    "stolt/lean-package-validator": "^5.2"
  },
  "scripts": {
    "validate-dist": "lean-package-validator validate --validate-git-archive --keep-license --keep-readme"
  }
}
```

**CI Integration:**

A dedicated `validate-dist` job runs on every push and pull request:

```yaml
validate-dist:
  runs-on: ubuntu-latest
  steps:
    - uses: actions/checkout@v6
    - uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
    - run: composer install --prefer-dist --no-progress
    - run: composer validate-dist
```

### 3. Options for LICENSE and README

**Decision:** Use `--keep-license` and `--keep-readme` flags

**Rationale:**

By default, lean-package-validator's PHP preset considers LICENSE and README files as "artifacts" that should be excluded. However, these files are essential for distribution:

- `LICENSE` - Required by most open source licenses for redistribution
- `README.md` - Primary user documentation, often displayed on Packagist

The `--keep-license` and `--keep-readme` flags prevent these files from being flagged as unwanted artifacts.

## Consequences

### Positive

- Distribution packages contain only necessary files (~12 entries vs 50+ in full repository)
- CI automatically catches forgotten `export-ignore` entries
- Developers can verify distribution contents locally with `composer validate-dist`
- Reduced download size for package consumers
- Clear documentation of distribution strategy

### Negative

- Additional development dependency (lean-package-validator)
- Must remember to update `.gitattributes` when adding new development files
- CI job adds slight overhead to pipeline execution

### Maintenance Notes

When adding new files to the repository:

1. **Development files** (tests, configs, docs): Add `export-ignore` directive to `.gitattributes`
2. **Distribution files** (source code, essential docs): No action needed
3. Run `composer validate-dist` locally to verify

If CI fails on `validate-dist`:
1. Check which files were flagged as "slipped in"
2. Add appropriate `export-ignore` directives to `.gitattributes`
3. Re-run validation

## References

- [GitAttributes for PHP Composer Projects - PHP.Watch](https://php.watch/articles/composer-gitattributes)
- [stolt/lean-package-validator - Packagist](https://packagist.org/packages/stolt/lean-package-validator)
- [Composer Libraries Documentation](https://getcomposer.org/doc/02-libraries.md)
- [Git archive export-ignore](https://git-scm.com/docs/gitattributes#_creating_an_archive)
