# Hostinger deployment

This project is deployed from source with compiled Vite assets committed in `public/build`. Do not commit `.env`, SQLite databases, CMS uploads from `storage/app/public`, `vendor`, `node_modules`, or the local `public/storage` link.

## Server requirements

- PHP 8.2 or newer with the extensions required by Laravel and Filament
- MySQL or MariaDB
- Composer 2
- The domain or subdomain document root set to the project's `public` directory

Laravel must be able to write to `storage` and `bootstrap/cache`. Prefer the hosting account's normal ownership and group-writable permissions (typically directories `775` and files `664` where needed); do not use `777`.

## First deployment

1. Clone or upload the project, including `public/build` and `composer.lock`.
2. Create `.env` from `.env.example` and replace all placeholders, especially `APP_URL` and the MySQL credentials. Keep `APP_ENV=production` and `APP_DEBUG=false`.
3. Run:

```sh
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan admin:create
php artisan optimize
```

Generate the production `APP_KEY` before the application receives real traffic or encrypted data. Never copy the local key, commit the production key, or change it later: changing it invalidates encrypted cookies/sessions and can make encrypted application data unreadable.

The `admin:create` command securely prompts for the first administrator. Do not store administrator credentials in Git, migrations, seeders, configuration, or Business Settings.

## Demo content

The production database starts clean except for one structural Business Settings row. After signing in at `/admin`, enter Business Settings and add the selected demo services, categories, products, and gallery items. Do not copy the local SQLite database or local CMS uploads.

## Updates

After pulling a new revision, use:

```sh
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

Recreate the storage link with `php artisan storage:link` only when it is missing. The committed `public/build` means Node.js is not required on Hostinger.

## Without SSH

Run Composer and the frontend build locally, upload the deployable project without `.env`, local data, `vendor`, `node_modules`, CMS uploads, or `public/storage`, then provide a production `.env` through Hostinger. Laravel still requires migrations, the storage link, and administrator creation; use Hostinger's terminal where available or have the hosting operator run those commands. Do not expose a permanent web-based setup route.
