# Theme

`themes/meat-theme` is the MEAT theme: **SCSS + Vue 3**, built by Vite through `@meatpress/vite-config`, with its Tailwind and Gutenberg block options turned off (`themes/meat-theme/vite.config.js`).

## Assets

| | |
| --- | --- |
| Entry | `resources/assets/app.js` (imports the SCSS and the scripts) |
| SCSS | `resources/assets/scss/` — `abstracts/`, `base/`, `components/` |
| JS | `resources/assets/js/app/` (jQuery plugins), `js/vue/` (Vue components, mounted on `#app`) |
| Images and fonts | `resources/assets/images/`, `fonts/` — copied to the build |
| Build output | `public/build/theme/meat-theme/` |

```bash
pnpm dev      # Vite dev server with hot reload
pnpm build    # production build
```

In Blade, reference built files with `Asset::url()`:

```blade
<img src="{{ Asset::url('images/logo.svg') }}" alt="">
```

A file the build does not know about has no public URL: the theme directory itself is not served.

## Views

Blade views in `resources/views` follow the WordPress template hierarchy: `home`, `page`, `single`, `index`, `404`… A request finds its view without a route. Use [Sage directives](https://log1x.github.io/sage-directives-docs/) for WordPress data:

```blade
@posts
  <h2>@title</h2>
  @content
@endposts
```

The layout (`layouts/app.blade.php`) calls `@wphead`, `wp_body_open()` and `@wpfooter`: plugins (GTM, Clarity, robots) print their tags there. Do not remove them.

## Configuration

`themes/meat-theme/config/` registers menus (`primary`, `footer`), sidebars, image sizes, theme supports and page templates.

## Text

User-facing text is in Spanish and goes through `__('…', 'meat-theme')`.
