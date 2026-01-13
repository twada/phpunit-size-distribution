# ADR 0005: Namespace Naming Convention

## Status

Accepted

## Context

Before publishing `phpunit-size-distribution` to Packagist, we needed to finalize the namespace naming convention. Once published, changing the namespace would be a breaking change requiring a major version bump.

The initial namespace was `Twada\PhpunitSizeDistribution`, which was mechanically derived from the composer package name using simple UpperCamelCase conversion. However, "PHPUnit" is a well-known proper noun in the PHP ecosystem, and we questioned whether this mechanical conversion was appropriate.

### Three Candidate Namespaces

| Option | Pattern | Example |
|--------|---------|---------|
| 1. `PhpunitSizeDistribution` | Mechanical UpperCamelCase | `Twada\PhpunitSizeDistribution\` |
| 2. `PhpUnitSizeDistribution` | Strict PascalCase | `Twada\PhpUnitSizeDistribution\` |
| 3. `PHPUnitSizeDistribution` | Respecting proper noun | `Twada\PHPUnitSizeDistribution\` |

### Investigation of PHP Ecosystem Conventions

We investigated how PHPUnit itself and related packages handle this naming:

**PHPUnit Official:**
- Namespace: `PHPUnit\Framework`
- Uses uppercase `PHP` and uppercase `U`

**PHPUnit Extension Packages:**

| Package | Namespace | Pattern |
|---------|-----------|---------|
| phpunit/phpunit | `PHPUnit\` | **PHPUnit** |
| johnkary/phpunit-speedtrap | `JohnKary\PHPUnit\Extension\` | **PHPUnit** |
| yoast/phpunit-polyfills | `Yoast\PHPUnitPolyfills\` | **PHPUnit** |
| spatie/phpunit-watcher | `Spatie\PhpUnitWatcher\` | **PhpUnit** |
| php-cs-fixer (class names) | `PhpUnitSizeClassFixer` | **PhpUnit** |

Two patterns emerged:
- **Pattern A (`PHPUnit`)**: Used by PHPUnit itself and packages closely tied to PHPUnit (johnkary, yoast)
- **Pattern B (`PhpUnit`)**: Used by some utility packages (spatie, php-cs-fixer internal classes)

### Comparison with npm Conventions

In the npm ecosystem, package names are typically lowercase with hyphens (e.g., `eslint-plugin-foo`), and there's no strict convention for namespace/class naming derived from package names. However, proper nouns are generally respected (e.g., `ESLint` not `Eslint`).

## Decision

We chose **`PHPUnitSizeDistribution`** (Option 3) as the namespace.

### Rationale

1. **PHPUnit is a proper noun**
   - "PHPUnit" is a brand name, not a generic English word
   - It's a compound of "PHP" and "Unit", both of which have established capitalizations
   - Similar precedents: `PHPMailer`, `PHPDoc`, `PHPStan` all use uppercase `PHP`

2. **Consistency with PHPUnit official namespace**
   - PHPUnit uses `PHPUnit\Framework` namespace
   - As a PHPUnit extension, visual alignment with the host framework is valuable
   - Users immediately recognize `PHPUnitSizeDistribution` as PHPUnit-related

3. **Following established PHPUnit extension patterns**
   - Major PHPUnit extensions (johnkary/phpunit-speedtrap, yoast/phpunit-polyfills) use `PHPUnit` in their namespaces
   - This establishes a de facto convention for PHPUnit extension naming

4. **PSR-4 does not dictate acronym handling**
   - PSR-4 specifies class-to-file mapping, not capitalization rules for acronyms
   - The choice between `PHPUnit` and `PhpUnit` is a style decision, not a compliance issue

### Why not Option 1 (`PhpunitSizeDistribution`)?

- Word boundaries are unclear (`Php` + `unit`? `Phpunit`?)
- No precedent in the ecosystem for this style
- Doesn't respect the established "PHPUnit" branding

### Why not Option 2 (`PhpUnitSizeDistribution`)?

- While valid PascalCase, it differs from PHPUnit's official style
- Creates visual inconsistency: `use PHPUnit\Framework\TestCase` alongside `use Twada\PhpUnitSizeDistribution\...`
- Less common among PHPUnit-specific extensions

## Consequences

### Positive

- Namespace aligns with PHPUnit's official naming convention
- Immediate visual recognition as a PHPUnit extension
- Consistent with other major PHPUnit extension packages
- Clear word boundaries (`PHPUnit` + `Size` + `Distribution`)

### Negative

- Not strictly PascalCase (though this is acceptable for proper nouns/acronyms)
- Requires careful typing (must remember the capitalization)

### Migration

The namespace change was performed before initial Packagist publication, affecting 32 files:
- 1 configuration file (`composer.json`)
- 7 source files (`src/`)
- 22 test files (`tests/`)
- 1 fixture configuration (`tests/Fixtures/phpunit.xml`)
- 1 documentation file (`README.md`)

All tests and static analysis (PHPStan level 8) pass after the migration.

## References

- [PHPUnit GitHub Repository](https://github.com/sebastianbergmann/phpunit) - Uses `PHPUnit\` namespace
- [johnkary/phpunit-speedtrap](https://github.com/johnkary/phpunit-speedtrap) - Uses `JohnKary\PHPUnit\Extension\`
- [yoast/phpunit-polyfills](https://github.com/yoast/phpunit-polyfills) - Uses `Yoast\PHPUnitPolyfills\`
- [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/) - Namespace-to-path mapping specification
