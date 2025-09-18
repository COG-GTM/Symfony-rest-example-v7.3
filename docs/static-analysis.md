# Static Analysis Tools

This project uses PHPStan and Psalm for static analysis to improve code quality and catch potential bugs early.

## Tools Overview

### PHPStan
- **Level**: 1 (permissive, for gradual adoption)
- **Extensions**: Symfony, Doctrine
- **Focus**: Basic syntax errors and critical issues

### Psalm
- **Error Level**: 8 (most permissive, for gradual adoption)
- **Focus**: Critical errors only, with common issues suppressed

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

## Gradual Adoption Strategy

The tools are initially configured with permissive settings to allow CI to pass while the team addresses existing code quality issues:

### Current Settings
- PHPStan: Level 1 (basic checks only)
- Psalm: Error level 8 (most permissive)

### Recommended Progression
1. **Phase 1**: Fix critical errors found at current levels
2. **Phase 2**: Increase PHPStan to level 3, reduce Psalm to error level 6
3. **Phase 3**: Gradually increase to PHPStan level 5, Psalm error level 4
4. **Phase 4**: Target PHPStan level 6-8, Psalm error level 2-3 for strict type safety

### Monitoring Progress
Use `--show-info=true` with Psalm to see suppressed issues that could be addressed.

## Increasing Strictness

As the codebase improves, consider:
- Increasing PHPStan level (currently 1, max 9)
- Decreasing Psalm error level (currently 8, min 1)
