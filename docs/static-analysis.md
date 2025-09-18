# Static Analysis Tools

This project uses PHPStan and Psalm for static analysis to improve code quality and catch potential bugs early.

## Tools Overview

### PHPStan
- **Level**: 5 (moderately strict)
- **Extensions**: Symfony, Doctrine
- **Focus**: Type checking, dead code detection, unreachable code

### Psalm
- **Error Level**: 4 (moderate strictness)
- **Focus**: Type safety, potential bugs, security issues

## Running Static Analysis

### Individual Tools
```bash
# Run PHPStan
composer run phpstan

# Run Psalm  
composer run psalm
```

### All Static Analysis
```bash
# Run both tools
composer run static-analysis
```

## Configuration Files

- `phpstan.neon` - PHPStan configuration
- `psalm.xml` - Psalm configuration
- `tests/object-manager.php` - Doctrine object manager for PHPStan

## CI Integration

Static analysis runs automatically in GitHub Actions on every push and pull request.

## Handling False Positives

### PHPStan
Add to `phpstan.neon` under `ignoreErrors`:
```neon
ignoreErrors:
    - '#Your specific error pattern#'
```

### Psalm
Use inline suppressions:
```php
/** @psalm-suppress ErrorType */
```

## Increasing Strictness

As the codebase improves, consider:
- Increasing PHPStan level (currently 5, max 9)
- Decreasing Psalm error level (currently 4, min 1)
