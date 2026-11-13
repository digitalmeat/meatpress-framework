# Conventions

The framework ships the conventions every MEAT site shares, in `src/Conventions` of [`meatpress-framework`](https://github.com/digitalmeat/meatpress-framework). Laravel package discovery registers `Meatpress\Conventions\ConventionsServiceProvider`.

## Behaviors

| Behavior | Key in `config/meatpress.php` | Default |
| --- | --- | --- |
| `noindex, nofollow` on every environment but `APP_ENV=production`, whatever the WordPress setting says | `noindex_outside_production` | on |
| Copies text custom fields into `post_content` on save, so the native search finds pages built with ACF | `searchable_custom_fields` | on |
| Removes block library, global styles and emoji assets from the front end | `clean_head` | on |
| Saves ACF field groups as JSON in the active theme | `acf_local_json` | on (`acf-json/`) |
| Tracking plugins that `plugins:activate` leaves inactive unless named | `optional_plugins` | GTM4WP, GA Google Analytics |

### Turning a behavior off for one project

```bash
docker compose exec app php artisan vendor:publish --tag=meatpress-config
```

Then edit `config/meatpress.php`. For example, a site that renders Gutenberg blocks keeps the block styles:

```php
'clean_head' => [
    'block_styles' => false,
    'global_styles' => true,
    'emojis' => true,
],
```

### Searchable custom fields

Meta keys matching a SQL `LIKE` pattern of `searchable_custom_fields.meta_key_patterns` (`%text%`, `%title%`, `%descript%`) are concatenated into `post_content`. ACF's `_field` reference keys are skipped, and a post without matching values keeps its own content. Name ACF fields accordingly (`hero_title`, `intro_text`, `card_description`…) to have them indexed.

## Commands

| Command | |
| --- | --- |
| `php artisan plugins:activate [slug...] [--except=slug]` | Activates the plugins Composer installed. Without arguments, the optional (tracking) plugins stay inactive. |
| `php artisan salts:generate [--force] [--show]` | Writes the WordPress keys and salts to `.env`. Existing salts are kept unless `--force`: replacing them logs every user out. |

## Adding a convention

A behavior every site needs belongs in the framework:

1. Add the class under `src/Conventions/Hooks` (or `src/Conventions/Console`), with its switch in `config/meatpress.php`.
2. Register it in `ConventionsServiceProvider` through the framework's `Action` / `Filter` services. Attributes (`#[Action]`) are not discovered in `vendor/`.
3. Add a test in `tests/Unit/Conventions` (Orchestra Testbench, no WordPress needed).
4. Release a version: see [Updating and releasing](./releasing).
