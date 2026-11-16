# Non Stop — CMS (Laravel + Filament)

Admin panel where editors manage the Non Stop website: sections, products, events, blogs, menus, shop links, site settings and the enquiries inbox.

```
nonstop_cms (this repo, /admin) ──► PostgreSQL ◄── nonstop_backend (API) ◄── nonstop_webapp (site)
```

This app **does not own the schema**. Migrations, seeders and the public API live in `nonstop_backend`; run those first. This app connects to the same database.

## Setup

```bash
composer install
cp .env.example .env          # same DB_* as nonstop_backend
php artisan key:generate
php artisan db:seed           # creates the admin login from ADMIN_EMAIL / ADMIN_PASSWORD
php artisan serve --port=8001
php artisan test
```

Open http://localhost:8001/admin.

## Environment

| Variable | Purpose |
|---|---|
| `DB_*` | Same PostgreSQL database as the backend |
| `PUBLIC_DISK_ROOT` / `PUBLIC_DISK_URL` | Folder (or bucket) and public URL where uploaded images are stored. The backend serves them, so both apps must share it |
| `FRONTEND_REVALIDATE_URL` / `REVALIDATE_SECRET` | After any content change, the CMS calls the Next.js site so it refreshes. The secret must match the frontend's `REVALIDATE_SECRET` |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | Used by the seeder to create the first admin |

Sessions and cache use files, so this app needs no tables beyond the shared content tables and `users`.

## Schema changes

When `nonstop_backend` adds a migration: update the matching model and Filament resource here, then copy the new migration into `tests/schema/` so tests can build the schema.

## Deploy on Render

1. Deploy `nonstop_backend` first (it creates the shared Postgres database and owns the schema).
2. **New → Blueprint** → this repo. Paste the database's **Internal Database URL** into `DB_URL`, set `APP_KEY` (a different key from the backend), `APP_URL`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`.
3. `FRONTEND_REVALIDATE_URL` = `https://<your-site>.vercel.app/api/revalidate` and `REVALIDATE_SECRET` = the same value as in Vercel, so edits refresh the website.
4. Use the **same storage variables** as the backend (`PUBLIC_DISK_DRIVER=s3`, `AWS_*`), otherwise uploaded images will not reach the website.
5. On start the container creates the first admin if missing (`RUN_SEED=true`); it never resets an existing password. Migrations stay off (`RUN_MIGRATIONS=false`).

The Docker image allows uploads up to 128 MB (reel videos).
