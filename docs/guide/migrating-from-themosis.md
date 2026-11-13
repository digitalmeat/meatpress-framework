# Migrating from Themosis

| Themosis | Meatpress |
| --- | --- |
| `htdocs/` | `public/` |
| `htdocs/content/themes/meat-theme/` | `themes/meat-theme/` |
| `resources/views`, `resources/scss`, `resources/js` | `themes/meat-theme/resources/{views,assets}` |
| `Route::get('/', …)` with Themosis conditions | Template hierarchy, or `Route::wp('front', …)` |
| `Themosis\Hook\Hookable` classes listed in `config/app.php` | Classes with `#[Action]` / `#[Filter]` in `app/Cms` |
| `PostType::make(…)`, `Taxonomy::make(…)` | `#[PostType]` / `#[Taxonomy]` classes |
| `App\Wordpress` / `App\Post` query builder | `Meatpress\Support\Facades\PostQuery` |
| `themosis_assets()` / `themosis_theme_assets()` | `Asset::url('images/…')` |
| `GTM_CODE`, `GOOGLE_ANALYTICS`, `FACEBOOK_PIXEL` in `.env` | GTM4WP (Pixel inside GTM), or GA Google Analytics for GA4 only |
| `HOTJAR_KEY` | Microsoft Clarity |
| `ENABLE_GUTEMBER` | Classic Editor plugin |
| SVG upload filter | Safe SVG plugin |
| `DATABASE_NAME`, `DATABASE_USER`, … | `DB_DATABASE`, `DB_USERNAME`, … (`DATABASE_PREFIX` stays) |
| `php artisan salts:generate` | Same command, from the framework |
| wp-cli forbidden (it breaks Themosis) | Supported (`wp-cli.yml` points to `public/cms`) |

## Moving an existing site

1. Start a new project from the skeleton ([New project](./getting-started)).
2. Import the database, keeping the table prefix in `DATABASE_PREFIX`, and the uploads into `public/content/uploads`.
3. Replace the domain in the database (serialized data included) if it changes.
4. Move the SCSS, JS and Blade views into `themes/meat-theme/resources`; replace `themosis_*()` helpers with `Asset::url()`.
5. Turn each Hookable class into an attribute class in `app/Cms`.
6. Activate the tracking plugin the client uses and move the IDs from `.env` to its settings.
