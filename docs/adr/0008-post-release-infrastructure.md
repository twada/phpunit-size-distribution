# ADR 0008: Post-Release Infrastructure Setup

## Status

Accepted

## Context

After releasing v0.1.0 to Packagist, several infrastructure components needed to be enabled or configured:

1. **README badges** were commented out pending first release
2. **Code coverage reporting** was generated locally but not published
3. **Dependency updates** were manual with no automation

These are common post-release tasks for OSS projects that improve visibility, maintainability, and contributor experience.

## Decision

We implemented three infrastructure improvements:

### 1. Badge Activation

**Decision:** Enable Packagist and Codecov badges in README.md

**Badges enabled:**
- Packagist version badge (via poser.pugx.org)
- Codecov coverage badge

**README refactoring:** Converted all badge URLs to reference-style links for better readability:

```markdown
<!-- Before: Inline style (hard to read) -->
[![CI](https://github.com/.../badge.svg)](https://github.com/...)

<!-- After: Reference style (clean) -->
[![CI][ci-image]][ci-url]

[ci-image]: https://github.com/.../badge.svg
[ci-url]: https://github.com/...
```

**Rationale:** Reference-style links keep the badge section concise and move long URLs to the end of the file, improving markdown readability for contributors.

### 2. Codecov Integration

**Decision:** Integrate Codecov for public code coverage reporting

**Implementation:**

| Component | Purpose |
|-----------|---------|
| `codecov/codecov-action@v5` in CI | Upload coverage reports |
| `CODECOV_TOKEN` secret | Authenticate uploads |
| `codecov.yml` configuration | Coverage thresholds and PR comments |
| Badge with token parameter | Display coverage percentage |

**Configuration choices:**

```yaml
coverage:
  status:
    project:
      default:
        target: auto      # Use base commit coverage as target
        threshold: 1%     # Allow 1% drop without failing
    patch:
      default:
        target: auto
        threshold: 1%

comment:
  require_changes: true   # Only comment when coverage changes
```

**Rationale:**
- `target: auto` prevents arbitrary coverage requirements that may not suit all projects
- `threshold: 1%` provides flexibility for minor fluctuations while catching significant drops
- `require_changes: true` reduces noise in PRs with no coverage impact

**Token requirement:** Codecov now requires authentication tokens for badge URLs on public repositories. The badge URL includes a `?token=` parameter that is safe to expose publicly (it only grants read access to coverage data).

**Distribution consideration:** `codecov.yml` was added to `.gitattributes` with `export-ignore` to exclude it from the Composer distribution package, following the lean package strategy established in ADR 0004.

### 3. Dependabot Configuration

**Decision:** Enable Dependabot for automated dependency updates

**Configuration:**

```yaml
updates:
  - package-ecosystem: "composer"
    schedule:
      interval: "weekly"
    groups:
      dev-dependencies:
        dependency-type: "development"
        update-types: ["minor", "patch"]

  - package-ecosystem: "github-actions"
    schedule:
      interval: "weekly"
```

**Design choices:**

| Choice | Rationale |
|--------|-----------|
| Weekly schedule | Balance between freshness and PR noise |
| Group dev dependencies | Reduce PR volume for low-risk updates |
| Separate GitHub Actions | Keep CI updates visible and reviewable |
| Conventional Commits prefixes | `chore(deps)` and `ci(deps)` for consistency |

**Rationale:** Dependabot reduces maintenance burden by automatically proposing dependency updates. Grouping development dependency updates (minor/patch) minimizes review overhead while keeping production dependencies individually reviewable.

## Consequences

### Positive

- Package visibility improved through Packagist version badge
- Code coverage publicly visible, encouraging quality maintenance
- Dependency updates automated, reducing security risk from outdated packages
- README more maintainable with reference-style links
- Distribution package remains lean (codecov.yml excluded)

### Negative

- `CODECOV_TOKEN` secret must be configured in repository settings
- Dependabot PRs require periodic review and merging
- Badge token in README could theoretically be rotated (low risk, read-only)

### Operational Notes

**Initial setup required:**
1. Add `CODECOV_TOKEN` secret in GitHub repository settings
2. Codecov GitHub App should be installed for the repository

**Ongoing maintenance:**
- Review and merge Dependabot PRs weekly
- Monitor Codecov for coverage trends

## Implementation Summary

| Commit | Type | Description |
|--------|------|-------------|
| `866f5ce` | docs | Enable Packagist version badge |
| `0a59071` | ci | Integrate Codecov in CI workflow |
| `cae836b` | chore | Add codecov.yml configuration |
| `75e1c26` | docs | Enable Codecov badge |
| `3e03e85` | ci | Add Dependabot configuration |
| `e780e9f` | ci | Use CODECOV_TOKEN secret |
| `b6c27a5` | chore | Exclude codecov.yml from distribution |
| `2bf6b55` | docs | Add token to Codecov badge URL |
| `f3dcd79` | docs | Convert badges to reference-style links |

## References

- [Codecov Quick Start](https://docs.codecov.com/docs/quick-start)
- [Dependabot Configuration Options](https://docs.github.com/en/code-security/dependabot/dependabot-version-updates/configuration-options-for-the-dependabot.yml-file)
- [Markdown Reference-Style Links](https://www.markdownguide.org/basic-syntax/#reference-style-links)
