# Laravel Blog

[![CI](https://github.com/diyor-ai/laravel-blog/actions/workflows/ci.yml/badge.svg)](https://github.com/diyor-ai/laravel-blog/actions/workflows/ci.yml)

A small multi-user blog built with **Laravel 11**. It was built while following a Laravel 11 course, then reviewed and hardened: the authorization, validation, upload and queue code was audited, the bugs found were fixed, and a feature-test suite was added.

> **This is a learning project, not production software.** See [Limitations](#limitations).

## Features

- Registration, login and logout (session auth, CSRF-protected forms).
- Posts: create, read, edit and soft-delete, with category, tags (many-to-many) and an optional cover photo.
- Authorization through `PostPolicy`: only the author can edit or delete a post.
- Comments on posts (authenticated users only).
- Paginated post list (6 per page) and a "recent posts" sidebar.
- On post creation a `PostCreated` event fires; a queued listener sends a queued mail (`mails` queue) to the author.
- Demo seeders, model factories, Laravel Pint, GitHub-Actions-ready test suite.

The UI text is mostly Uzbek (it comes from the course) on top of a free HTML template.

## Stack

PHP 8.2+ (8.3 in Docker), Laravel 11, Blade, Bootstrap 4 template, SQLite by default (MySQL in Docker), database queue, PHPUnit 11, Laravel Pint.

## Running it

### Local (SQLite)

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed       # seeds demo@example.com / password
php artisan storage:link         # so uploaded photos are served
php artisan serve                # http://localhost:8000
php artisan queue:work --queue=default,mails   # in a second terminal, to send the queued mail
```

Mails use the `log` mailer by default, so the "post created" mail is written to `storage/logs/laravel.log`.

### Docker (Laravel Sail: app, queue worker, MySQL, Redis, Mailpit)

```bash
composer install                 # Sail's Dockerfile lives in vendor/
cp .env.example .env             # then switch to the Docker values listed at the top of the DB section
docker compose up -d --build
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate --seed
docker compose exec laravel.test php artisan storage:link
```

The app is on http://localhost, Mailpit (caught mail) on http://localhost:8025. A `queue` service runs the worker for the `default` and `mails` queues.

## Tests

```bash
php artisan test      # 44 tests, 147 assertions
vendor/bin/pint --test
```

The suite uses PHPUnit with `RefreshDatabase` on in-memory SQLite, so it needs no setup and does not touch your data. It covers auth flows, post CRUD, the owner / other user / guest authorization matrix, comments, validation errors, uploads (`Storage::fake`), soft deletes, pagination, and the `PostCreated` flow with `Event::fake`, `Queue::fake` and `Mail::fake`.

## Review & fixes

After the course, the code was reviewed and these problems were fixed (each with a test):

| Problem found | Fix |
|---|---|
| **No authorization on `PostController@update`**: any logged-in user could edit anyone's post. | `Gate::authorize('update', $post)`; other user gets 403, guest is redirected to login. `destroy` now authorizes `delete`. |
| **`PostPolicy::restore` / `forceDelete` returned `true` for everyone.** | `restore` is owner-only, `forceDelete` is always denied. |
| **Comments without auth or validation** (a guest request crashed on `auth()->user()->id`; any `post_id` accepted). | `auth` middleware, validation (`body` required/max 1000, `post_id` must exist), route limited to `->only(['store'])`. |
| **Upload collisions**: files were stored under `getClientOriginalName()`, so equal names overwrote each other. They also went to the private disk while views read `storage/…`. | `store('post-photos', 'public')` generates hashed names; old photo is deleted on replacement. |
| **Weak validation**: `category_id` and `tags` were unchecked, photo had no type restriction. | `category_id` must exist, `tags` is an array of existing ids, photo must be a jpg/png/webp image up to 2 MB. |
| **`update()` ignored category and tags.** | Category is updated and tags are `sync()`ed; the edit form pre-selects both. |
| **Soft deletes silently disabled**: the migration had `softDeletes()` but the model did not use the trait, so `delete()` was permanent. | `SoftDeletes` on `Post`; deleted posts return 404 and stay restorable. |
| **Demo `ChangePost` job overwrote every new post's photo path** with `bigFile/bigFile.zip`. | Removed, together with the unused `UploadBigFile` job. |
| **Listeners did nothing but log, and were not actually queued** (`ShouldQueue` imported but not implemented). | One queued listener (`SendPostCreatedMail`) sends a queued `PostCreatedMail`; the log-only listeners were removed. |
| **Registration**: `email:rfc,dns` needs network at validation time; the confirmation rule was hand-rolled. | `email:rfc` and Laravel's `confirmed` rule. |
| Seeder contained a personal email and password. | Replaced by a demo user; the personal values were also scrubbed from the git history. |

Cleanup: the `$paht` typo, the `projeckt` route (now `projects`), a duplicated line, the empty `EnsureTokenIsValid` middleware, the unused broadcast method and imports, empty controllers, `xxxx.blade.php`, and the dead `blog()` action. `show()` now queries only the 5 most recent posts instead of loading all of them, relations are eager-loaded to avoid N+1 queries, and the controller uses `HasMiddleware` instead of extending `Illuminate\Routing\Controller`.

## Limitations

- Learning project: no email verification, rate limiting on login, password reset, roles/admin, or search.
- Soft-deleted posts can only be restored through code/console; there is no UI for it.
- Photos are validated by type and size but are not resized or scanned.
- Laravel Horizon is installed, but it only works with the Redis queue; the default queue is `database`.
- Most of the UI is an unmodified free HTML template ("Klean" by [HTML Codex](https://htmlcodex.com), credit kept in the footer) with Uzbek text; its images and libraries keep their own licenses. The page content (about, services, ...) is placeholder text.
- Developed against PHP 8.3 (CI target). Docker setup is provided but was only validated with `docker compose config`, not exercised end to end.

## License

The application code is released under the [MIT License](LICENSE) © 2026 Mukhammaddiyor Abdulazimov. Third-party template assets under `public/` keep their original licenses.
