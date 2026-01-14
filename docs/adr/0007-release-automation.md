# ADR 0007: Release Automation Strategy

## Status

Accepted

## Context

With the project ready for publication on Packagist, we needed to establish a release workflow. Unlike compiled languages that require building artifacts and uploading them to package registries, PHP packages distributed via Composer/Packagist have a simpler release process:

1. **Packagist integration**: Packagist monitors GitHub repositories via webhooks and automatically detects new version tags
2. **No build artifacts**: The source code itself is the distribution—no compilation or bundling required
3. **Git tags as versions**: Packagist uses Git tags to determine available versions

This means the release process fundamentally consists of:
- Creating a Git tag
- (Optionally) Creating a GitHub Release with release notes

### Requirements

We identified the following requirements for the release workflow:

1. **Automated GitHub Release creation** with release notes extracted from CHANGELOG.md
2. **Pre-release validation** to ensure CHANGELOG.md is updated before releasing
3. **CI verification** to confirm tests pass before publishing a release
4. **Minimal manual steps** to reduce human error

### Release Workflow Options Considered

| Option | Description | Pros | Cons |
|--------|-------------|------|------|
| A: Fully manual | Tag locally, manually create GitHub Release | No CI changes needed | Error-prone, no validation |
| B: Tag-triggered automation | Push tag → CI creates Release | Simple, tag-driven | Requires CHANGELOG update before tagging |
| C: GitHub UI-initiated | Create Release via GitHub UI | Visual preview | CHANGELOG/Release notes duplication |
| D: release-please | Conventional Commits → auto PR → auto release | Fully automated | Complex, overkill for small projects |

## Decision

We chose **Option B: Tag-triggered automation** with pre-release validation.

### Rationale

1. **Simplicity**: The workflow is straightforward—update CHANGELOG, tag, push
2. **Validation**: CI verifies CHANGELOG has the correct version entry before creating the release
3. **Single source of truth**: CHANGELOG.md serves as the authoritative source for release notes
4. **No new dependencies**: Uses standard GitHub Actions without external services

### Implementation

#### Workflow Trigger

```yaml
on:
  push:
    tags:
      - 'v*'
```

**Tag format**: `v1.0.0` (with `v` prefix)—the most common convention in the PHP ecosystem and GitHub.

#### Job Structure

```
validate-changelog ─────┬───────────────┐
        │               │               │
        ▼               ▼               │
      test           quality            │
        │               │               │
        └───────┬───────┘               │
                │                       │
                ▼                       │
            release ◄───────────────────┘
```

1. **validate-changelog**: Extract version from tag, verify CHANGELOG.md has matching section, extract release notes
2. **test**: Run tests on representative PHP/PHPUnit combinations (3 of 11)
3. **quality**: Run PHPStan and php-cs-fixer
4. **release**: Create GitHub Release with extracted release notes

#### CHANGELOG Extraction

Using `awk` for robust section extraction that handles both middle sections and the last section in the file:

```bash
VERSION="1.0.0"
awk "/^## \[${VERSION}\]/{found=1} found{if(/^## \[/ && !/^## \[${VERSION}\]/) exit; print}" CHANGELOG.md
```

This extracts everything from `## [1.0.0]` up to (but not including) the next `## [` header, or to EOF if it's the last section.

#### Test Matrix for Releases

To balance thoroughness with release speed, the release workflow runs a subset of the full CI matrix:

| CI Workflow | Release Workflow | Rationale |
|-------------|------------------|-----------|
| 11 combinations | 3 combinations | Representative coverage |
| fail-fast: false | fail-fast: true | Fail early on release |

Selected combinations:
- PHP 8.1 + PHPUnit 10.5 (minimum supported)
- PHP 8.3 + PHPUnit 11.5 (middle ground)
- PHP 8.4 + PHPUnit 12.1 (latest)

#### Validation Behavior

If validation fails (CHANGELOG not updated), the workflow exits with an error and no GitHub Release is created. The tag remains in the repository but is effectively unpublished until the issue is resolved.

### Release Process (Developer Workflow)

```bash
# 1. Update CHANGELOG.md
#    - Change [Unreleased] to [1.0.0] - YYYY-MM-DD
#    - Add new [Unreleased] section for future changes

# 2. Commit and push
git commit -am "chore(release): prepare v1.0.0"
git push

# 3. Create and push tag
git tag v1.0.0
git push origin v1.0.0

# 4. (Automated) CI validates, tests, and creates GitHub Release
# 5. (Automated) Packagist detects new tag and updates package listing
```

## Consequences

### Positive

- GitHub Releases are created automatically with consistent formatting
- CHANGELOG.md is enforced as the source of truth for release notes
- Pre-release validation catches forgotten CHANGELOG updates
- Tests and quality checks run before every release
- Simple, predictable workflow with minimal manual steps

### Negative

- Requires discipline to update CHANGELOG.md before tagging
- If validation fails after tagging, manual cleanup may be needed (delete tag, fix, re-tag)
- Reduced test matrix for releases (3 vs 11 combinations)

### Operational Notes

**First-time setup:**
1. Configure Packagist webhook in GitHub repository settings
2. Uncomment the Packagist version badge in README.md after first release

**Handling validation failures:**
```bash
# If release workflow fails due to CHANGELOG issue:
git tag -d v1.0.0              # Delete local tag
git push origin :v1.0.0        # Delete remote tag
# Fix CHANGELOG.md, commit, push, then re-tag
```

## References

- [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
- [Semantic Versioning](https://semver.org/)
- [softprops/action-gh-release](https://github.com/softprops/action-gh-release)
- [Packagist - How to Update Packages](https://packagist.org/about)
