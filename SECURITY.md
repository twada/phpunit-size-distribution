# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 0.x     | :white_check_mark: |

## Reporting a Vulnerability

We take the security of phpunit-size-distribution seriously. If you believe you have found a security vulnerability, please report it to us as described below.

### Please DO NOT

- Open a public GitHub issue for security vulnerabilities
- Disclose the vulnerability publicly before it has been addressed

### Please DO

Report security vulnerabilities through GitHub's Security Advisory feature:

1. Go to the [Security Advisories page](https://github.com/twada/phpunit-size-distribution/security/advisories)
2. Click "New draft security advisory"
3. Fill in the details of the vulnerability

Alternatively, you can report via email to **takuto.wada@gmail.com**.

### What to Include

Please include the following information in your report:

- Type of vulnerability
- Full paths of source file(s) related to the vulnerability
- Steps to reproduce
- Proof-of-concept or exploit code (if possible)
- Impact of the vulnerability

### Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Resolution Target**: Within 30 days (depending on complexity)

### After Reporting

Once you have submitted a vulnerability report:

1. You will receive an acknowledgment within 48 hours
2. We will investigate and keep you informed of our progress
3. Once the vulnerability is confirmed, we will work on a fix
4. We will coordinate with you on the disclosure timeline
5. After the fix is released, we will publicly acknowledge your contribution (unless you prefer to remain anonymous)

## Security Best Practices

When using this extension:

- Keep PHP and PHPUnit updated to their latest stable versions
- Run `composer audit` regularly to check for known vulnerabilities in dependencies
- Review the extension's output in CI/CD pipelines, not in production environments

## Scope

This security policy applies to the phpunit-size-distribution package distributed via Packagist.

Third-party dependencies are managed by their respective maintainers. Please report vulnerabilities in dependencies to those projects directly.
