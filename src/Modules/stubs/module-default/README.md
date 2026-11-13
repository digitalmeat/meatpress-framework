# %module_name%

%module_description%

A [Laravel module](https://laravelmodules.com) built with [Meatpress](https://github.com/digitalmeat/meatpress-framework/tree/main/docs) from the [module-default](https://github.com/digitalmeat/meatpress-framework) template.

## What's inside

```
%module_name%/
├── app/                      # PHP classes, namespace %module_namespace%
│   └── Cms/Hooks/            # WordPress hooks, declared with attributes
├── resources/
│   ├── assets/               # app.js and app.css, built with Vite and Tailwind CSS v4
│   └── views/blocks/         # Gutenberg blocks, registered by Meatpress
├── composer.json             # PSR-4 autoload, merged into the project's
├── module.json               # No providers: Meatpress discovers the classes in app/
├── package.json
└── vite.config.js            # @meatpress/vite-config, type "module"
```

Classes in `app/` are discovered: hooks, post types, taxonomies, REST routes and scheduled tasks declared with attributes need no service provider.

## Commands

From the project root:

```bash
php artisan meatpress:make:block hero --module=%module_name%   # a block in resources/views/blocks
php artisan meatpress:make:post-type Ticket --module=%module_name%
php artisan module:make-provider %module_name%ServiceProvider %module_name%   # when the module needs one
php artisan module:disable %module_name%
```

From the module's directory:

```bash
npm install
npm run dev      # Vite dev server with hot reload (port 5175, VITE_PORT to change it)
npm run build    # into public/build/module/%module_slug%
```
