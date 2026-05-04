# MindEase Full Deployment (GitHub -> Render Free)

This project is a full Laravel app (frontend + backend + database), so deploy it to an app host (not GitHub Pages).

## 1) Push repository to GitHub

Make sure this repository is in GitHub with these files committed:

- `render.yaml`
- `Dockerfile`
- `.dockerignore`

## 2) Create services on Render (free)

This repo now includes `render.yaml` for Blueprint deploy.

- In Render dashboard, choose **New +** -> **Blueprint**.
- Connect your GitHub repo and select this project.

If your GitHub repository root is NOT the Laravel project folder that contains `artisan`, set **Root Directory** in the blueprint to the correct subfolder (often `mindease`).
- Render will create:
  - `mindease-web` (free web service)
  - `mindease-db` (free PostgreSQL)

## 3) Add required environment variables

Set these in Render (some are already defined in `render.yaml`):

- `APP_ENV=production` (already set)
- `APP_DEBUG=false` (already set)
- `APP_URL=https://your-service-name.onrender.com` (set after first deploy)
- `APP_KEY=base64:...` (set manually; generate locally using `php artisan key:generate --show`)

Default `.env.example` uses database-backed sessions/caches/jobs queues. Render free web disk is ephemeral, so PostgreSQL is required for persistence.

PostgreSQL settings are wired automatically from Render database in `render.yaml`:

- `DB_CONNECTION=pgsql` (already set)
- `DB_HOST` (from `mindease-db`)
- `DB_PORT` (from `mindease-db`)
- `DB_DATABASE` (from `mindease-db`)
- `DB_USERNAME` (from `mindease-db`)
- `DB_PASSWORD` (from `mindease-db`)

Avoid SQLite on Render free because local filesystem data does not persist across deploys/restarts.

For Google login, also set:

- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI` (must match your deployed callback URL)

## 4) Deploy

During the **Docker build**, `Dockerfile` runs `composer install`.

On container start (via `serversideup/php` Laravel automations controlled by `AUTORUN_ENABLED`), Render will commonly run:

- migrations
- Laravel optimization/caches (config/events/routes/views depending on defaults)
- storage link

Important: do **not** commit a real `.env` to GitHub — set secrets in Render.

## 5) Google OAuth callback

If using Google login, set:

- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI=https://your-service-name.onrender.com/auth/google/callback`

Also add the same callback URL in your Google Cloud OAuth app settings.

## 6) Post-deploy checks

- Open `/` and verify landing page loads.
- Test login and Google OAuth callback URL.
- Test authenticated chat + mood pages.
- Confirm writes persist in your configured database.

## Free tier notes

- Render free web services spin down after inactivity; first request after idle may be slow.
- If your app later uses queued jobs heavily, add a dedicated background worker service.
