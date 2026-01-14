# ADR 0006: OSS Release Preparation Strategy

## Status

Accepted

## Context

Before publishing `phpunit-size-distribution` to Packagist as an open source package, we conducted a comprehensive review to assess release readiness. The review evaluated the project across eight dimensions:

1. Code Quality
2. Documentation
3. Packaging
4. Security
5. CI/CD
6. Legal
7. Community
8. Miscellaneous

### Review Findings

The technical implementation scored well (code quality, CI/CD, legal), but significant gaps existed in community-facing aspects:

| Dimension | Initial Score | Key Issues |
|-----------|---------------|------------|
| Code Quality | 8/10 | PHPDoc completely missing from all source files |
| Documentation | 6/10 | No CONTRIBUTING.md, no limitations documented |
| Packaging | 7/10 | Missing keywords, homepage, support URLs |
| Security | 8/10 | No SECURITY.md |
| CI/CD | 9/10 | Excellent |
| Legal | 9/10 | Good |
| Community | 3/10 | No templates, no Code of Conduct |

The **Community** dimension was critically deficient—a common oversight in developer-led projects where technical excellence overshadows contributor experience.

## Decision

We implemented a comprehensive preparation strategy organized into priority tiers.

### Tier 1: Essential Files for OSS Release

#### 1. CHANGELOG.md (Keep a Changelog Format)

**Decision:** Adopt [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) format with Semantic Versioning

**Rationale:**
- Industry-standard format recognized by developers
- Clear structure (Added, Changed, Deprecated, Removed, Fixed, Security)
- Supports "Unreleased" section for tracking upcoming changes
- Integrates well with release automation tools

**Implementation:** Created with initial "Unreleased" section containing all Phase 1 features.

**Distribution consideration:** CHANGELOG.md is included in the distribution package (not export-ignored) as it provides value to package consumers. Required updating `.lpv` to remove `*.{md,MD}` from the artifact pattern.

#### 2. CONTRIBUTING.md

**Decision:** Create comprehensive contribution guidelines including development commands from CLAUDE.md

**Content structure:**
- Code of Conduct reference
- How to contribute (bugs, features, PRs)
- Development setup and commands
- TDD workflow explanation
- Conventional Commits guidelines
- Code quality requirements checklist
- Architecture overview

**Rationale:** Lowers the barrier for new contributors by documenting the development workflow that was previously only in CLAUDE.md (an AI-assistant configuration file not meant for human contributors).

#### 3. CODE_OF_CONDUCT.md (Contributor Covenant 2.1)

**Decision:** Adopt Contributor Covenant version 2.1

**Alternatives considered:**

| Option | Pros | Cons |
|--------|------|------|
| Contributor Covenant 2.1 | Industry standard, well-maintained, translated | Generic |
| Custom Code of Conduct | Project-specific | Maintenance burden, less recognized |
| No Code of Conduct | Simpler | Unwelcoming, unprofessional |

**Rationale:** Contributor Covenant is the most widely adopted code of conduct in open source, used by projects like Linux, Kubernetes, and Rails. Version 2.1 includes improved enforcement guidelines.

#### 4. SECURITY.md

**Decision:** Use GitHub Security Advisories as the primary vulnerability reporting channel

**Alternatives considered:**

| Option | Pros | Cons |
|--------|------|------|
| GitHub Security Advisories | Private, integrated, CVE support | Requires GitHub |
| Email only | Simple, universal | No tracking, no CVE integration |
| Both | Flexibility | Complexity |

**Rationale:** GitHub Security Advisories provides:
- Private vulnerability discussion
- Coordinated disclosure workflow
- Automatic CVE assignment
- Security advisory publication

Email (takuto.wada@gmail.com) is provided as an alternative for reporters who prefer it.

**Response timeline committed:**
- Initial response: 48 hours
- Status update: 7 days
- Resolution target: 30 days

#### 5. GitHub Templates

**Decision:** Create issue templates (bug report, feature request) and PR template

**Bug report template includes:**
- Environment details (PHP version, PHPUnit version, OS)
- Steps to reproduce
- Expected vs actual behavior
- Error output sections

**Feature request template includes:**
- Problem statement
- Proposed solution
- Use cases
- Willingness to contribute checkbox

**PR template includes:**
- Summary and related issue
- Type of change checkboxes
- Quality checklist (tests, PHPStan, CS, commits)

**Rationale:** Templates ensure consistent, high-quality issue reports and PRs, reducing maintainer burden and improving contributor experience.

#### 6. composer.json Metadata

**Decision:** Add `homepage`, `keywords`, and `support` fields

```json
{
  "homepage": "https://github.com/twada/phpunit-size-distribution",
  "keywords": ["phpunit", "testing", "test-size", "test-pyramid", "extension", "metrics"],
  "support": {
    "issues": "https://github.com/twada/phpunit-size-distribution/issues",
    "source": "https://github.com/twada/phpunit-size-distribution"
  }
}
```

**Rationale:**
- `keywords`: Improves discoverability on Packagist search
- `homepage`: Provides quick access to project repository
- `support`: Directs users to appropriate channels for help

#### 7. README.md Limitations Section

**Decision:** Document known limitations explicitly

**Documented limitations:**
1. **Class-level attributes only**: PHPUnit's size attributes cannot be applied to individual methods
2. **DataProvider inheritance**: Generated test cases inherit class size
3. **No runtime detection**: Extension reads metadata, doesn't measure actual execution

**Rationale:** Prevents user confusion and reduces issue reports for expected behavior. Users appreciate transparent documentation of constraints.

### Tier 2: Code Quality Improvements

#### 8. PHPDoc for All Source Files

**Decision:** Add comprehensive PHPDoc to all 7 source files

**PHPDoc strategy:**

| Element | Approach |
|---------|----------|
| Classes | Description of purpose and responsibility |
| Interface-implemented methods | `{@inheritDoc}` to reference parent documentation |
| Constructor parameters | `@param` with description |
| Public methods | `@param` and `@return` where not obvious from types |
| Properties | Single-line `/** @var */` comments |

**Rationale:**
- Improves IDE support (autocompletion, hover documentation)
- Serves as inline documentation for contributors
- Professional appearance expected of quality packages
- `{@inheritDoc}` avoids documentation drift from PHPUnit interfaces

### Distribution Package Updates

**Decision:** Update `.gitattributes` to exclude new community files from distribution

Added export-ignore for:
- `/CODE_OF_CONDUCT.md`
- `/CONTRIBUTING.md`
- `/SECURITY.md`
- `/CLAUDE.md`

**Not excluded (included in distribution):**
- `CHANGELOG.md` - Useful for package consumers
- `LICENSE` - Required
- `README.md` - Primary documentation

**lean-package-validator configuration:**
- Created `.lpv` file based on default PHP preset
- Removed `*.{md,MD}` pattern to allow CHANGELOG.md in distribution
- Added `/.lpv` to `.gitattributes` export-ignore

## Consequences

### Positive

- Project meets industry standards for OSS release
- Clear contribution pathway for potential contributors
- Professional appearance increases adoption confidence
- Security vulnerability handling process established
- Improved Packagist discoverability
- IDE experience enhanced with PHPDoc
- Known limitations documented, reducing confusion

### Negative

- Additional files to maintain (7 new files)
- Code of Conduct enforcement responsibility
- Security response timeline commitment
- PHPDoc must be kept in sync with code changes

### Community Score Improvement

| Dimension | Before | After |
|-----------|--------|-------|
| Code Quality | 8/10 | 10/10 |
| Documentation | 6/10 | 9/10 |
| Packaging | 7/10 | 9/10 |
| Security | 8/10 | 10/10 |
| Community | 3/10 | 9/10 |
| **Overall** | **7.1/10** | **9.4/10** |

## Implementation Summary

| Commit | Type | Description |
|--------|------|-------------|
| `65b1dd8` | docs | CHANGELOG.md (Keep a Changelog) |
| `dae8e06` | chore | .lpv config, .gitattributes update |
| `06e54c9` | docs | CONTRIBUTING.md |
| `f9a6281` | docs | CODE_OF_CONDUCT.md |
| `4845f7e` | docs | SECURITY.md |
| `4ac0a54` | chore | GitHub issue/PR templates |
| `2d43116` | chore | composer.json metadata |
| `c349140` | docs | README.md limitations |
| `8e72236` | docs | PHPDoc for all source files |

## References

- [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
- [Contributor Covenant](https://www.contributor-covenant.org/)
- [GitHub Security Advisories](https://docs.github.com/en/code-security/security-advisories)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [PHPDoc Reference](https://docs.phpdoc.org/guide/references/phpdoc/index.html)
