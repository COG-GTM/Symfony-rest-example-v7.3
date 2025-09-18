# Symfony 7.4 Upgrade Playbook

This document provides a comprehensive guide for upgrading Symfony projects from version 7.3 to 7.4+ (development version).

## Pre-Upgrade Checklist

### Environment Verification
- [ ] Verify PHP version is 8.3 or higher: `php --version`
- [ ] Ensure Composer is up to date: `composer --version`
- [ ] Check current Symfony version: `composer show symfony/framework-bundle`
- [ ] Verify database is accessible and backed up
- [ ] Confirm all team members are aware of the upgrade

### Backup and Safety
- [ ] Create full database backup
- [ ] Commit all pending changes: `git status` and `git add/commit`
- [ ] Create backup branch: `git checkout -b backup-before-7.4-upgrade`
- [ ] Return to main branch: `git checkout master`
- [ ] Ensure CI/CD pipeline is green before starting

### Dependency Analysis
- [ ] Review current composer.json for custom packages that may need updates
- [ ] Check for deprecated features in current codebase
- [ ] Identify any third-party bundles that may need compatibility updates

## Step-by-Step Upgrade Process

### 1. Create Upgrade Branch
```bash
# Generate timestamp for branch naming
TIMESTAMP=$(date +%s)
git checkout -b devin/${TIMESTAMP}-symfony-7.4-upgrade
```

### 2. Update Version Constraints in composer.json

Update all Symfony components from `^7.3` to `^7.4`:
- symfony/asset
- symfony/console
- symfony/dotenv
- symfony/expression-language
- symfony/framework-bundle
- symfony/property-access
- symfony/property-info
- symfony/runtime
- symfony/serializer
- symfony/uid
- symfony/validator
- symfony/yaml

Update require-dev packages:
- symfony/browser-kit
- symfony/css-selector
- symfony/phpunit-bridge

Update extra.symfony.require:
```json
"extra": {
    "symfony": {
        "require": "7.4.*"
    }
}
```

### 3. Allow Development Versions
Since Symfony 7.4 is still in development, update composer.json:
```json
"minimum-stability": "dev",
"prefer-stable": true
```

### 4. Handle PHP 8.4 Compatibility Issues
Symfony 7.4 development version may include features requiring PHP 8.4. If using PHP 8.3, disable native lazy objects:

In `config/packages/doctrine.yaml`:
```yaml
doctrine:
    orm:
        enable_native_lazy_objects: false
```

In `phpunit.xml.dist`:
```xml
<server name="ENABLE_NATIVE_LAZY_OBJECTS" value="0" />
```

### 5. Update Dependencies
```bash
# Remove lock file to allow version updates
rm composer.lock

# Install new dependencies
composer install --optimize-autoloader

# Update Symfony recipes if needed
composer recipes:update
```

### 6. Clear and Warm Caches
```bash
# Clear development cache
php bin/console cache:clear --env=dev

# Clear production cache
php bin/console cache:clear --env=prod

# Warm production cache
php bin/console cache:warmup --env=prod
```

### 7. Database Updates
```bash
# Check for schema changes
php bin/console doctrine:schema:validate

# Apply any necessary migrations
php bin/console doctrine:migrations:migrate -n

# For development, recreate schema if needed
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --purge-with-truncate -n
```

## Testing Procedures

### System Verification
```bash
# Check Symfony requirements
symfony check:requirements

# Verify all Symfony packages are at 7.4.x-dev
composer show | grep symfony
```

### Automated Testing
```bash
# Run full test suite
composer run-script test

# Run specific test groups if available
php bin/phpunit --group=integration
php bin/phpunit --group=unit
```

### Manual Testing Checklist
- [ ] Application starts without errors
- [ ] Database connections work properly
- [ ] API endpoints respond correctly
- [ ] Authentication/authorization functions properly
- [ ] File uploads and downloads work
- [ ] Email functionality operates correctly
- [ ] Background jobs process successfully

### Performance Testing
- [ ] Compare response times before and after upgrade
- [ ] Monitor memory usage during typical operations
- [ ] Verify cache performance improvements

## Risk Mitigation Strategies

### Common Issues and Solutions

**Dependency Conflicts**
- Solution: Use `composer why-not symfony/framework-bundle 7.4.x-dev` to identify blocking packages
- Update or replace incompatible packages

**Deprecated Features**
- Solution: Review Symfony 7.4 changelog for breaking changes
- Update code to use new recommended approaches

**Third-Party Bundle Incompatibility**
- Solution: Check bundle documentation for Symfony 7.4 support
- Consider alternative bundles or temporary workarounds

**Performance Regressions**
- Solution: Profile application before and after upgrade
- Optimize configuration based on new Symfony 7.4 features

### Monitoring and Alerting
- Set up application monitoring during upgrade
- Configure alerts for error rates and response times
- Monitor database performance metrics

## Rollback Plan

### Immediate Rollback (Git-based)
```bash
# If issues discovered immediately after deployment
git checkout master
git branch -D devin/${TIMESTAMP}-symfony-7.4-upgrade

# Restore previous composer.lock
git checkout HEAD~1 -- composer.lock
composer install
```

### Database Rollback
```bash
# Restore database from backup
# (Commands depend on your database system)

# For PostgreSQL example:
pg_restore -d app_database backup_before_upgrade.sql

# Run any necessary down migrations
php bin/console doctrine:migrations:migrate prev -n
```

### Full Environment Rollback
1. Revert code changes via git
2. Restore database from backup
3. Clear all caches
4. Restart application services
5. Verify functionality with smoke tests

## Documentation Update Guidelines

### README.md Updates
- Update Symfony version references from 7.3 to 7.4+
- Update installation instructions if needed
- Verify all documentation links still work

### API Documentation
- Review and update any version-specific API documentation
- Update OpenAPI/Swagger specifications if applicable
- Verify code examples in documentation

### Deployment Documentation
- Update deployment scripts with new requirements
- Review Docker configurations for version updates
- Update CI/CD pipeline documentation

### Team Communication
- Notify team of upgrade completion
- Share any new features or changes in Symfony 7.4
- Update development environment setup instructions

## Post-Upgrade Checklist

### Immediate Actions (First 24 Hours)
- [ ] Monitor application logs for errors
- [ ] Verify all critical business functions
- [ ] Check performance metrics
- [ ] Confirm backup systems are working

### Short-term Actions (First Week)
- [ ] Review and optimize new Symfony 7.4 features
- [ ] Update development team on any workflow changes
- [ ] Plan removal of deprecated code patterns
- [ ] Schedule performance optimization review

### Long-term Actions (First Month)
- [ ] Evaluate new Symfony 7.4 features for adoption
- [ ] Plan next upgrade cycle
- [ ] Update coding standards if needed
- [ ] Review and update this playbook based on experience

## Additional Resources

- [Symfony 7.4 Release Notes](https://symfony.com/releases/7.4)
- [Symfony Upgrade Guide](https://symfony.com/doc/current/setup/upgrade_major.html)
- [Doctrine ORM 3.x Documentation](https://www.doctrine-project.org/projects/orm.html)
- [PHPUnit 12 Documentation](https://phpunit.de/documentation.html)

## Troubleshooting

### Common Error Messages

**"Using native lazy objects requires PHP 8.4 or higher"**
- Disable native lazy objects in doctrine.yaml and phpunit.xml.dist
- See step 4 in the upgrade process above

**"Package X is not compatible with Symfony 7.4"**
- Check for updated versions of the package
- Look for alternative packages
- Consider temporary removal if not critical

**"Tests not executing"**
- Verify PHPUnit configuration
- Check test file locations and namespaces
- Ensure database is properly set up for testing

### Getting Help
- Symfony Community Slack: #help channel
- Stack Overflow: Tag questions with 'symfony' and 'symfony-7.4'
- GitHub Issues: Check Symfony repository for known issues

---

**Document Version**: 1.0  
**Last Updated**: September 2025  
**Symfony Version**: 7.4.x-dev  
**PHP Version**: 8.3+
