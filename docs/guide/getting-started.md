# New project

## Before the first project

1. The shared MySQL container `mysqlserver` runs on the Docker network `localnetwork` (the same one the Themosis projects use).
2. The Meatpress packages are private. Composer needs a GitHub token: add this to `~/.zshrc` (`docker-compose.yml` passes it to the `app` container). pnpm installs `@meatpress/vite-config` with git, so your GitHub credentials (SSH key or `gh auth setup-git`) are enough.

   ```bash
   export COMPOSER_AUTH="{\"github-oauth\":{\"github.com\":\"$(gh auth token)\"}}"
   ```

## Create the site

```bash
# 1. Copy the skeleton into a new repository
git clone https://github.com/digitalmeat/meatpress.git client-website
cd client-website
rm -rf .git && git init -b main
gh repo create digitalmeat/client-website --private --source . --remote origin
```

In `composer.json`, change `name` to `digitalmeat/client-website` and the `description`.

```bash
# 2. Database
docker exec mysqlserver mysql -uroot -proot \
  -e "CREATE DATABASE client_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Environment: set APP_NAME, DB_DATABASE, APP_PORT and APP_URL
cp .env.example .env

# 4. Containers
docker compose up -d --build

# 5. Dependencies, keys and WordPress
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan salts:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan meatpress:install --locale=es_CL --public=false   # activates meat-theme
docker compose exec app php artisan plugins:activate

# 6. Front end (on the host)
pnpm install && pnpm --dir themes/meat-theme install
pnpm build
```

The site answers on `APP_URL`, the admin on `/cms/wp-admin`.

## Then

- Pick the tracking setup: [GTM or GA4](./tracking).
- Set the favicon as the **Site Icon** (*Appearance → Customize → Site Identity*).
- Start the theme: [Theme](./theme).

::: tip Several projects at once
Give each project its own `APP_PORT` (and the matching `APP_URL`). The Docker setup keeps each project's nginx and PHP on a private network, so projects never answer each other's requests.
:::
