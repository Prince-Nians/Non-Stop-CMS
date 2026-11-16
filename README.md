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

## Deploy on Render (free demo)

1. Deploy `nonstop_backend` first: it creates the shared Postgres database and owns the schema.
2. **New → Blueprint** → this repo. Render asks for six values:
   - `DB_URL`: nonstop-db → Connections → **Internal Database URL**
   - `PUBLIC_DISK_URL`: `https://<your-backend>.onrender.com/media` (uploads are kept in the database; the backend serves them)
   - `ADMIN_EMAIL`, `ADMIN_PASSWORD`: the first admin login
   - `FRONTEND_REVALIDATE_URL`: `https://<your-site>.vercel.app/api/revalidate`
   - `REVALIDATE_SECRET`: any long random string, the same one set in Vercel
3. On start the container creates the admin if it is missing (never resets an existing password). The app key and `APP_URL` are filled in automatically; migrations stay off (the schema belongs to the backend).
4. Open `https://<cms>.onrender.com/admin` and log in. Edits show on the website within moments; uploaded images appear because both apps share the database.

The free service sleeps when idle: the first request after a break takes 30–60 s. Uploads up to 128 MB are allowed by the image; the Livewire upload limit (12 MB by default) applies to the admin form.
