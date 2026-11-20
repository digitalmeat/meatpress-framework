# Meatpress Framework

The MEAT Code framework that runs **WordPress inside a Laravel 13 application**: Laravel routing, Blade, Eloquent and PHP attributes on the code side; the WordPress admin and plugins on the content side.

MEAT Code sites start from an internal skeleton that requires this package. Documentation: [`docs/`](docs) (a VitePress site: `cd docs && pnpm install && pnpm dev`).

## What is inside

| Directory | |
| --- | --- |
| `src/` | The framework: WordPress bootstrap, routing (`Route::wp()`), template hierarchy, themes, plugins, modules, assets, Blade directives, console commands (`meatpress:*`) |
| `src/Conventions/` | MEAT Code conventions: noindex outside production, searchable custom fields, clean `<head>`, ACF local JSON, `plugins:activate`, `salts:generate` — configured in `config/meatpress.php` |
| `packages/` | The components the framework is built from, each with its own namespace: `Hook`, `Query`, `Attributes`, `Discovery`, `Entity`, `Media`, `Meta`, `Option`, `Role`, `Schedule`, `WpCli`, `WpRest`, `BlockBinding`, `Colt` (Eloquent models for WordPress), `Asset`, `Ajax`, `Abilities`, `WordPressArgs` |
| `config/` | Framework configuration published to projects |
| `resources/` | Views, translations, stubs |
| `resources/vite/` | `@meatpress/vite-config`, the Vite configuration of themes, plugins and modules (npm package installed from this repository) |
| `docs/` | The Meatpress documentation |

The translation helper that lets Laravel and WordPress share `__()` lives in a separate package, [`digitalmeat/meatpress-helpers`](https://github.com/digitalmeat/meatpress-helpers): it must be autoloaded before Laravel's helpers, so it cannot depend on Laravel.

## Installation

The packages are installed from GitHub, so the project declares both repositories:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/digitalmeat/meatpress-framework" },
    { "type": "vcs", "url": "https://github.com/digitalmeat/meatpress-helpers" }
]
```

```bash
composer require digitalmeat/meatpress-framework
```

The project must also apply `patches/wordpress-core.patch` to `johnpbloch/wordpress-core` (the skeleton does): it renames WordPress's `__()` to `__wp()`.

## Development

```bash
composer install
composer test        # Pest
composer lint        # Pint
```

## Versioning

Meatpress follows Laravel's major version: Meatpress 13.x runs on Laravel 13.x. Releases are Git tags (`v13.0.0`…).

## License

MIT — see [LICENSE](LICENSE).
