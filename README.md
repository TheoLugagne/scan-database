# Scan Database

A shared catalog of scans (webtoons, manga, and similar series) with per-user reading progress. Anyone can browse the catalog. Signed-in readers track their own chapter and status. Admins keep the catalog complete: genres, missing fields, and chapter counts that have gone stale.

## Features

**Catalog.** Each scan has a unique title, summary, cover, source link, publication status, available chapter count, and one or more genres. The listing supports search (title or source link), filters by publication status and genre, and pagination. Signed-in readers can also filter by their own reading status. Creating a scan starts a progress row for the current user.

**Reading progress.** Each user has their own row per scan: current chapter and reading status (`not started`, `ongoing`, `completed`, `on hold`, `dropped`). Readers see only their own list. The current chapter can be updated from the progress page without a full form submit.

**Publication status.** A scan is `ongoing`, `completed`, `hiatus`, or `cancelled`. That status belongs to the catalog entry, not to an individual reader.

**Dashboard.** Signed-in users see their library broken down by reading status, how many chapters they are behind the published count, reads that are on hiatus or cancelled, recently touched reads, and ongoing / unread / on-hold reads left untouched the longest.

**Admin.** Users with `role = admin` also see catalog totals by publication status, scans missing required information, and chapter counts that were never dated or last changed more than 30 days ago. Admins manage genres and are the only role that can delete a scan. A scan counts as complete when title, summary, cover, source link, status, available chapters, and at least one genre are all filled. Changing the chapter count stamps `available_chapters_updated_at`; clearing the count clears that date.

**Accounts.** Registration, login, password reset, email verification, and profile edit come from Laravel Breeze. The first admin is created by the role migration from `SUPERUSER`, `SUPERUSER_EMAIL`, and `SUPERUSER_PASSWORD`.

## Architecture

Laravel 12 application (PHP 8.2) with server-rendered Blade views. Tailwind CSS and Alpine.js are built by Vite. State lives in the database (SQLite by default; MySQL and MariaDB are configured). Sessions, cache, and queues use Laravel’s defaults.

```
Browser
  │
  ▼
routes/web.php          public catalog
routes/auth.php         Breeze auth, behind guest or auth middleware
  │
  ▼
Controllers             Scan, UserScanProgress, Dashboard, Genre, Profile
  │
  ├── Policies          Scan (delete is admin-only), UserScanProgress (owner only), Genre (admin)
  │
  ▼
Eloquent models         Scan, User, Genre, UserScanProgress
Enums                   ScanStatus, ReadingStatus
  │
  ▼
Database                scans, users, genres, genre_scan, user_scan_progress
```

| Piece | Role |
| --- | --- |
| `ScanController` | Catalog CRUD, search and filters, cover uploads on the `public` disk, chapter-count timestamp |
| `UserScanProgressController` | The signed-in user’s list, filters, and chapter updates |
| `DashboardController` | Reader panels for everyone; catalog health panels when `role` is `admin` |
| `GenreController` | Genre list, create, and delete |
| `Scan::scopeIncomplete()` | Scans missing a required field, a chapter count, or any genre |

Catalog routes (`/scan`) are public. Dashboard, My Scans, genres, profile, and chapter updates sit behind the `auth` middleware. Policies enforce ownership of progress rows and restrict genre changes and scan deletion to admins. Cover files are stored under `storage/app/public` and served through the `public` disk (`php artisan storage:link`).

List pages load the first page as a normal view, then request filtered HTML from `/scans/fetch` and `/userScanProgress/fetch`. Laravel exposes a health check at `/up`.

### Data model

```
users 1───* user_scan_progress *───1 scans *───* genres
                                      (genre_scan)
```

- **users** — name, email, password, `role` (`user` or `admin`).
- **scans** — shared catalog row: title (unique), summary, cover path, source URL, publication status, `available_chapters`, `available_chapters_updated_at`.
- **genres** — name. Linked to scans through `genre_scan`.
- **user_scan_progress** — `user_id`, `scan_id`, `current_chapter`, `reading_status`. Deleting a user or a scan cascades to these rows.

## Local setup

Requirements: PHP 8.2+, Composer, Node.js, and a database. SQLite needs no extra server.

```bash
composer install
cp .env.example .env   # create .env if the example file is absent
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan storage:link
npm install
npm run build
```

Set these in `.env` before migrating if you want a known admin instead of the migration defaults (`admin` / `admin@example.com` / `admin`):

```
SUPERUSER=
SUPERUSER_EMAIL=
SUPERUSER_PASSWORD=
```

`composer run dev` starts the PHP server, queue listener, log tail, and Vite together. Tests run with `php artisan test`.

## Deployment

Pushes to `main` run `.github/workflows/deploy.yml`. The job SSHs into the server with `appleboy/ssh-action` and runs:

```bash
cd $SSH_REMOTE_PATH
git pull
php artisan migrate
npm install
npm run build
```

GitHub Actions secrets:

| Secret | Use |
| --- | --- |
| `SSH_HOST` | Server hostname |
| `SSH_USERNAME` | SSH user |
| `SSH_PASSWORD` | SSH password |
| `SSH_REMOTE_PATH` | Absolute path of the deployed checkout |

The server is expected to already have PHP, Composer’s `vendor/` directory, Node.js, a populated `.env` (`APP_KEY`, database credentials, `APP_URL`), and a linked `public/storage`. The workflow does not run `composer install` or `php artisan storage:link`. Run those on the server when PHP dependencies change or on a fresh checkout. The web server document root must be `public/`.
