# Docker

Each project runs two containers, plus the shared MySQL:

| Service | Image | Networks |
| --- | --- | --- |
| `app` | `Docker/Dockerfile` (PHP 8.5 FPM, Composer, Xdebug) | project network + `localnetwork` |
| `webserver` | `nginx:alpine`, config in `Docker/nginx/conf.d/nginx.conf` | project network only |
| `mysqlserver` | shared, not part of the project | `localnetwork` |

## Why two networks

Every project names its services `app` and `webserver`. On a single shared network, nginx resolving `app` could reach **another project's** PHP and answer "File not found" at random. nginx and PHP therefore talk over a network private to the project; only `app` joins `localnetwork`, to reach MySQL.

The nginx service also gets a unique alias, `<project>-web`, used by the site health tests from inside `app`.

## Everyday commands

```bash
docker compose up -d                          # start
docker compose down                           # stop
docker compose exec app php artisan <cmd>     # artisan
docker compose exec app composer <cmd>        # Composer
docker compose exec app php artisan test      # tests
```

## nginx notes

- The document root is `public/`.
- `try_files` has no `$uri/` for the site, so the empty `index.php` files WordPress ships in `content/plugins/` and friends answer 404 instead of a blank page.
- `/cms/wp-admin` uses a **plain** prefix location. `location ^~ /cms/wp-admin` (as some guides show) stops the PHP location from matching and serves the source of wp-admin as text.
- Dotfiles (`.env`, `.git`) are denied.

## Xdebug

Set `XDEBUG_MODE: 'debug'` in `docker-compose.yml`, rebuild, and copy `Docker/launch.json.example` to `.vscode/launch.json`.
