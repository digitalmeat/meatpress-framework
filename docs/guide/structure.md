# Project structure

```
app/Cms/                       # Project hooks, post types, taxonomies (auto-discovered)
config/
├── wordpress.php              # WordPress constants, route conditions, mail handling
└── meatpress.php              # Only if published: per-project convention switches
Docker/                        # Dockerfile, nginx and PHP configuration
public/                        # Document root
├── cms/                       # WordPress core (Composer, not versioned)
└── content/
    ├── plugins/               # Composer, not versioned — except ACF Pro
    ├── languages/             # es_CL translations (Composer, not versioned)
    └── uploads/
routes/web.php                 # Laravel routes and Route::wp() routes
themes/meat-theme/
├── acf-json/                  # ACF field groups (local JSON)
├── app/                       # Theme\MeatTheme\ classes (auto-discovered)
├── config/                    # menus, sidebars, image sizes, supports, templates
└── resources/
    ├── assets/                # app.js entry, scss/, js/, images/, fonts/
    └── views/                 # Blade templates (WordPress template hierarchy)
patches/                       # wordpress-core.patch (renames WordPress's __() to __wp())
tests/                         # PHPUnit: installation and site health
```

## What is not versioned

`vendor/`, `node_modules/`, `public/cms/`, the Composer-installed plugins, `public/content/languages/`, `public/build/` and `.env`. Everything there is rebuilt with `composer install` and `pnpm build`.
