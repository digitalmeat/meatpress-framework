# Quality tooling

| Tool | Checks | Run |
| --- | --- | --- |
| Pint | PHP style (Laravel preset, `declare(strict_types=1)`) | `composer lint` |
| ESLint | JS and Vue (`eslint.config.js`) | `pnpm lint` |
| Stylelint | SCSS (`.stylelintrc.json`) | `pnpm lint` |
| Prettier | JS, Vue, SCSS, JSON, Markdown, YAML | `pnpm format` / `pnpm format:check` |
| PHPUnit | Installation and site health | `docker compose exec app php artisan test` |

## Git hooks (Husky)

- **pre-commit** — lint-staged fixes the staged files: ESLint + Prettier, Stylelint + Prettier, Pint. Third-party code (`public/`, `vendor/`) is never touched.
- **pre-push** — runs the unit tests in the `app` container (or with the host PHP).

Hooks are installed by `pnpm install`.

## CI

GitHub Actions (`.github/workflows/ci.yml`) runs Pint, ESLint, Stylelint, Prettier and the theme build on every push and pull request. It does not run `composer install`, so it needs no token for the private packages.
