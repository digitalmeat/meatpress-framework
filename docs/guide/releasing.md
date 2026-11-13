# Updating and releasing

## Versions

Meatpress follows Laravel's major version: **Meatpress 13.x runs on Laravel 13.x**. The framework is released with Git tags (`v13.0.0`, `v13.1.0`…); sites require `^13.0`.

## Updating a site

```bash
docker compose exec app composer update digitalmeat/meatpress-framework   # framework and conventions
docker compose exec app composer update "wp-plugin/*"                     # plugins
```

WordPress core follows `johnpbloch/wordpress` in `composer.json`. After a WordPress update, check that `patches/wordpress-core.patch` still applies: `php artisan test` fails if it does not.

## Releasing the framework

1. Change `meatpress-framework` on a branch, with a test; `composer test` must pass.
2. Merge to `main`, then tag and publish:

   ```bash
   git tag -a v13.1.0 -m "Meatpress Framework 13.1.0"
   git push origin v13.1.0
   gh release create v13.1.0 --notes "…"
   ```

3. Sites get it with `composer update digitalmeat/meatpress-framework`.

Use a patch version for fixes, a minor version for new behaviors (behind a switch, on by default only when every site wants it), and keep breaking changes for the next Laravel major.

`meatpress-helpers` is released the same way (the framework requires `^13.0`). `@meatpress/vite-config` ships with the framework: themes require `github:digitalmeat/meatpress-framework#semver:^13.1&path:/resources/vite`, so a framework release is also a Vite config release.

## Maintaining the framework

MEAT Code maintains Meatpress: new Laravel and WordPress versions, dependency updates and security fixes are ours to make. Dependabot alerts are on for every repository.

## Changing the skeleton

The skeleton only affects **new** projects. A change every existing site needs belongs in the framework.
