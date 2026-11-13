# What is Meatpress

Meatpress is the framework every MEAT Code WordPress site is built on. WordPress runs **inside a Laravel 13 application**: the code side gets Laravel routing, Blade, Eloquent, migrations and PHP attributes; the content side keeps the WordPress admin and plugins editors already know.

It replaces the Themosis base (`digitalmeat/themosis3.0`). MEAT Code owns and maintains every piece.

## The pieces

Three repositories, all private, in the `digitalmeat` organization. Day to day you work in the first two.

| Repository | What it is |
| --- | --- |
| [`meatpress`](https://github.com/digitalmeat/meatpress) | The **skeleton**: Docker, plugins, `meat-theme`, tooling. A new site is a copy of it. |
| [`meatpress-framework`](https://github.com/digitalmeat/meatpress-framework) | The **framework** (Composer package `digitalmeat/meatpress-framework`): WordPress bootstrap, routing, template hierarchy, themes, modules, assets, attribute discovery, and the MEAT [conventions](./conventions). Sites depend on it, so a fix reaches all of them with `composer update`. |
| [`meatpress-helpers`](https://github.com/digitalmeat/meatpress-helpers) | One `__()` for Laravel and WordPress. Separate because it must load before Laravel's helpers; it rarely changes. |

The framework repository also holds `@meatpress/vite-config` (`resources/vite`, the Vite configuration of themes, plugins and modules) and this documentation (`docs/`).

## Principles

- **Everything that can be installed with Composer is installed with Composer**: WordPress core, plugins, translations. `DISALLOW_FILE_MODS` blocks installs and updates from the admin.
- **Tracking lives in plugins, not in `.env`**: Clarity on every site, plus GTM *or* GA4. See [Tracking](./tracking).
- **`.env` holds secrets and environment values only**: database, mail, keys and salts.
- **A behavior every site needs belongs in the framework**, not copied into each project.

## Stack

| | |
| --- | --- |
| PHP | 8.3+ (Docker image: 8.5) |
| Framework | Laravel 13 + Meatpress 13 |
| WordPress | 7.1, in `public/cms` |
| Database | MySQL, the shared `mysqlserver` container |
| Front end | Vite 8, SCSS (sass-mq), Vue 3, jQuery, slick, SweetAlert2 |
| Quality | Pint, ESLint, Stylelint, Prettier, Husky + lint-staged, PHPUnit |
