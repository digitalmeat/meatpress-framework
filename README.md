# Meatpress Framework

Meatpress Framework integrates WordPress with Laravel 13. Developers can use Laravel routing, Blade, and Eloquent while editors work in the WordPress admin.

- Route requests through Laravel and render WordPress templates with Blade.
- Register WordPress hooks, post types, taxonomies, and REST routes through PHP attributes.
- Query WordPress data with Colt's Eloquent models.
- Share Vite configuration across themes, plugins, and modules with `@meatpress/vite-config`.

## Getting started

Start a new site with [Meatpress Base](https://github.com/digitalmeat/meatpress-base). The starter configures the WordPress installation, theme, and required Composer patch.

To integrate the package in an existing Laravel 13 project, configure Composer access to both private repositories and add the framework:

```bash
composer config repositories.meatpress-framework vcs https://github.com/digitalmeat/meatpress-framework
composer config repositories.meatpress-helpers vcs https://github.com/digitalmeat/meatpress-helpers
composer require digitalmeat/meatpress-framework:^13.0
```

Use the starter's [WordPress core patch](https://github.com/digitalmeat/meatpress-base/blob/main/patches/wordpress-core.patch) and [Composer configuration](https://github.com/digitalmeat/meatpress-base/blob/main/composer.json) as the integration reference. The patch renames WordPress's `__()` to `__wp()` so [Meatpress Helpers](https://github.com/digitalmeat/meatpress-helpers) can provide a shared translation function.

## Learning Meatpress

Read the [documentation](docs), starting with the [project setup](docs/guide/getting-started.md), [routing and PHP attributes](docs/guide/code.md), and [framework conventions](docs/guide/conventions.md). To preview the documentation locally:

```bash
cd docs
pnpm install
pnpm dev
```

## Contributing

With Composer access to the private Helpers repository, run:

```bash
composer install
composer test
composer test:lint
```

Meatpress follows Laravel's major versions. See the [release guide](docs/guide/releasing.md) for tagging and publishing changes.

## Security vulnerabilities

Report suspected vulnerabilities privately to the MEAT Code maintainers.

## License

Meatpress Framework is licensed under the [MIT License](LICENSE).
