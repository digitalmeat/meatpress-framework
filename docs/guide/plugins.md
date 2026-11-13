# Plugins

Plugins come from [WP Packages](https://wp-packages.org) (`wp-plugin/<slug>`, the slug of wordpress.org/plugins/&lt;slug&gt;), locked in `composer.lock`.

| Plugin | Package | Active by default |
| --- | --- | --- |
| ACF Pro | versioned in `public/content/plugins` | yes |
| Classic Editor | `wp-plugin/classic-editor` | yes |
| Microsoft Clarity | `wp-plugin/microsoft-clarity` | yes |
| WP Activity Log | `wp-plugin/wp-security-audit-log` | yes |
| Safe SVG | `wp-plugin/safe-svg` | yes |
| GTM4WP | `wp-plugin/duracelltomi-google-tag-manager` | no — see [Tracking](./tracking) |
| GA Google Analytics | `wp-plugin/ga-google-analytics` | no — see [Tracking](./tracking) |

## Add a plugin

```bash
docker compose exec app composer require wp-plugin/<slug>
docker compose exec app php artisan plugins:activate <slug>
```

Its `es_CL` translation, if wordpress.org has one, is downloaded by Composer.

## Update plugins

```bash
docker compose exec app composer update "wp-plugin/*"
```

Updates from the admin are disabled on purpose: every environment runs the versions in `composer.lock`.

## ACF Pro

ACF Pro is paid and has no public Composer repository, so it is the one plugin committed to the repository (`public/content/plugins/.gitignore`). To update it, replace `public/content/plugins/advanced-custom-fields-pro` with the new release from the ACF account.

Field groups are saved as JSON in `themes/meat-theme/acf-json` (a framework [convention](./conventions)): commit them, and *Sync* them in the admin of the other environments.
