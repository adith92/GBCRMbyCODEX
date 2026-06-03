# Railway Deployment Guide for GBCRMbyCODEX

## Prerequisite

- GitHub repository: `adith92/GBCRMbyCODEX`
- Railway account with access to create a project
- MySQL service added inside Railway
- Production environment variables prepared from `.env.railway.example`

## Railway Dashboard Steps

1. Create a new Railway project.
2. Choose deploy from GitHub repository.
3. Connect `adith92/GBCRMbyCODEX`.
4. Add a MySQL database service.
5. Open the app service settings and add required environment variables.

## Required Environment Variables

Minimum variables:

- `APP_NAME=GBCRMbyCODEX`
- `APP_ENV=production`
- `APP_KEY=`
- `APP_DEBUG=false`
- `APP_URL=`
- `LOG_CHANNEL=stderr`
- `DB_CONNECTION=mysql`
- `DB_HOST=`
- `DB_PORT=3306`
- `DB_DATABASE=`
- `DB_USERNAME=`
- `DB_PASSWORD=`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`
- `ENABLE_DEMO_SEED=false`
- `DEMO_SEED_MODE=demo`
- `DEMO_CUSTOMER_COUNT=1200`

## Build Command

Use your normal install and asset build pipeline, for example:

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
npm run build
```

## Startup Command

Recommended Railway start command:

```bash
sh railway/init-app.sh
```

In this repo, `nixpacks.toml` chains that script before `php artisan serve`, so every fresh boot or redeploy will safely run the startup preparation first.

This script will:

- clear config, route, and view cache
- run migrations with `--force`
- refresh RBAC baseline safely on each deploy
- only seed demo data when `ENABLE_DEMO_SEED=true` and the `clients` table is still empty
- support `DEMO_SEED_MODE=demo` for curated walkthrough data
- support `DEMO_SEED_MODE=stress` for larger performance-oriented demo data
- rebuild production caches

## Seeder Modes

Recommended values:

- stakeholder demo: `ENABLE_DEMO_SEED=true`, `DEMO_SEED_MODE=demo`, `DEMO_CUSTOMER_COUNT=1200`
- performance smoke test: `ENABLE_DEMO_SEED=true`, `DEMO_SEED_MODE=stress`
- production without demo data: `ENABLE_DEMO_SEED=false`

When `DEMO_SEED_MODE=stress` and `DEMO_CUSTOMER_COUNT` is empty, the seeder defaults to 1200 clients.

## First Demo Reset Command

If a demo database was left half-migrated or half-seeded from a failed deploy, do a one-time manual reset:

```bash
railway run --service web -- php artisan migrate:fresh --seed --force
```

Use this only for a disposable demo environment.
Do not keep `migrate:fresh` in permanent Railway startup or pre-deploy scripts.

## Normal Deploy Behavior

Normal deploys should keep using:

```bash
php artisan migrate --force
```

That keeps Railway startup deterministic and avoids dropping production or long-lived demo data on every deploy.

## First Deploy

1. Generate app key locally:

```bash
php artisan key:generate --show
```

2. Paste the generated key into Railway as `APP_KEY`.
3. Start deployment.
4. Confirm migration completes successfully.
5. Confirm the application root redirects guests to login.

## Generate Domain

1. In Railway service settings, generate a public domain.
2. Copy that domain.
3. Set `APP_URL` to the generated Railway domain.
4. Redeploy if required.

Current verified live domain:

- `https://gbdemo01.up.railway.app`

## Smoke Test

After deployment verify:

- login page loads
- demo user login works if demo seed is enabled
- dashboard opens
- bookings page opens
- finance page opens
- maintenance page opens
- no obvious 500 error in logs
- logout works

## Rollback

If a deploy fails:

1. Roll back to the previous healthy Railway deployment.
2. Review deployment logs.
3. Fix environment variables or migration issue.
4. Redeploy from a known good commit.

## Common Errors

### APP_KEY missing

Symptoms:
- application cannot boot correctly
- encryption/session related errors

Fix:
- generate a key with `php artisan key:generate --show`
- set the result into Railway `APP_KEY`

### DB connection failed

Symptoms:
- migration or runtime connection errors

Fix:
- verify `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- confirm Railway MySQL service is healthy

### Storage permission issues

Symptoms:
- cache/session/write failures

Fix:
- ensure Railway image/container allows Laravel runtime write paths
- keep file writes within expected Laravel storage directories

### Migration failed

Symptoms:
- deploy stops during `php artisan migrate --force`

Fix:
- inspect migration logs
- verify DB credentials
- retry only after issue is understood

### Route cache issue

Symptoms:
- route resolution problems after deploy

Fix:
- clear and rebuild caches through `railway/init-app.sh`
- ensure no closure route is incompatible with cache strategy if that issue appears

### APP_DEBUG true in production

Symptoms:
- sensitive debug details exposed publicly

Fix:
- set `APP_DEBUG=false`
- redeploy after config refresh
