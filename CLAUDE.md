# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Laravel **13** app (running on **PHP 8.5**) implementing CRUD for **Schools** and **Students** through two
surfaces: a Blade/Inertia admin area under `/admin` (session auth) and a RESTful JSON API under `/api`
(Passport / `auth:api`). Students carry a per-school `order` column that is auto-assigned and can be
renumbered by an artisan command.

Originally a Laravel 8 project (see git history + `README.md`). `composer update` has been run —
`laravel/framework` 13.30, `passport` 13.8, `sanctum` 4.3, `breeze` 2.4, `inertia-laravel` 3.3,
`phpunit` 12.5, plus `laravel/boost`, `laravel/pint`, `laravel/pail`.

### Upgrade: done vs. outstanding

**Done**
- Laravel 11+ skeleton migration: `bootstrap/app.php` is the `Application::configure()` form,
  `bootstrap/providers.php` lists app providers, and `app/Http/Kernel.php`, `app/Console/Kernel.php`,
  `app/Exceptions/Handler.php` and every `app/Http/Middleware/*` class were deleted (folded into
  `bootstrap/app.php` / framework defaults). `RouteServiceProvider` is kept — still registers
  `routes/{web,api}.php`, owns the `api` rate limiter and the `HOME` = `/dashboard` constant that the
  Breeze auth controllers reference.
- `config/app.php` trimmed to the modern form (no `providers` / `aliases` arrays).
- `config/database.php` mysql `options` made PHP 8.5-safe (`Pdo\Mysql::ATTR_SSL_CA` fallback).
- Passport 13: `Passport::routes()` removed from `AuthServiceProvider`; OAuth keys generated into
  `storage/oauth-{private,public}.key`; personal-access + password-grant clients exist;
  `AuthServiceProvider::boot()` calls `Passport::enablePasswordGrant()` (off by default in Passport 11+)
  because the Postman collection logs in via the password grant.
- `app/Models/User.php` switched from `Laravel\Sanctum\HasApiTokens` to `Laravel\Passport\HasApiTokens`
  (the `api` guard is the passport driver).
- Local DB switched to **SQLite** (`database/database.sqlite`) — no MySQL on this machine.

**Outstanding — do not assume these work**
- **Test suite does not run.** PHPUnit 12 made `TestCase::__construct()` `final`; `SchoolsServiceTest`,
  `StudentsServiceTest`, `StudentApiCrudTest`, `TestRepositoriesMainClassTest` all override it and fatal.
  Move the Faker setup into `setUp()` (or use the `WithFaker` trait) and delete the constructors.
- `inertiajs/inertia-laravel` 0.5 → 3 and Breeze 1 → 2 are major jumps (middleware, root view, events).
- Front end still uses `laravel-mix` (`webpack.mix.js`); Laravel 11+ expects **Vite**. `laravel/pint` is
  installed but there is no CI wiring.
- Re-diff the rest of `config/*` against a fresh Laravel 13 skeleton (`cors.php` is now framework-managed,
  `session.php` / `sanctum.php` gained keys, the old `oauth_*` + `personal_access_tokens` migrations
  predate Passport 12 / Sanctum 4 — the Sanctum table lacks `expires_at`).

## Commands

```bash
# Setup (SQLite locally — .env already has DB_CONNECTION=sqlite)
composer install
npm install
cp .env.example .env && php artisan key:generate     # first time only; edit APP_ENV=local, APP_DEBUG=true
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=AdminSeeder              # admin@gmail.com / 123456
php artisan passport:keys                             # generate storage/oauth-*.key — API 500s without these
php artisan passport:client --personal --no-interaction

# Run
php artisan serve
npm run dev            # laravel-mix asset build (also: npm run watch, npm run prod)
php artisan students:order   # renumber every school's students 1..n, then fire SendEmailEvent

# Tests (PHPUnit 12) — currently fatal, see "Outstanding" above
php artisan test
php artisan test tests/Feature/StudentApiCrudTest.php      # single file
php artisan test --filter test_create_student              # single test
vendor/bin/phpunit --testsuite=Feature                     # or Unit

# Format (Pint is installed)
vendor/bin/pint
```

- `php artisan db:seed` (no `--class`) **fails** — `DatabaseSeeder::run()` calls a `SchoolSeeder` class
  that does not exist in the repo. Create it or edit `DatabaseSeeder`.
- StyleCI (`.styleci.yml`, `laravel` preset, `no_unused_imports` disabled) is the historical style gate;
  `vendor/bin/pint` is the local equivalent now.

## Testing gotchas

- **The suite is currently broken** — see "Outstanding" above (PHPUnit 12 `final` constructor).
- `phpunit.xml` leaves the sqlite `:memory:` lines commented out, so tests run against the **configured DB
  connection** — now the SQLite file from `.env`. There is no `RefreshDatabase`: tests use
  `DatabaseTransactions` and assume pre-seeded data (`User::find(1)`, at least one `schools` row), so run
  `migrate` + `db:seed --class=AdminSeeder` and create a school first.
- API tests authenticate with `Passport::actingAs(User::find(1))` — needs the OAuth keys present.

## Architecture

Request flow is a deliberate layering on top of default Laravel MVC:

**Controller → Service → Repository → Factory → Eloquent Model**

- **Controllers** (`app/Http/Controllers/{Students,Schools}/*`) instantiate their service directly with `new`
  in the constructor (no container binding / DI). API controllers return HTTP 200 for everything with a
  `{ success, message, errors }` JSON body.

- **Framework wiring** lives in `bootstrap/app.php` (routing, middleware, exceptions) and
  `bootstrap/providers.php` (app providers) — there are no HTTP/Console `Kernel` classes or an
  `Exceptions/Handler`. Middleware tweaks (CSRF exceptions, trusted proxies, redirects) go in the
  `->withMiddleware()` closure; the login/dashboard redirects are set there via `redirectTo()`.

- **Services** (`app/Services/**`) build a plain `\stdClass` from the request, delegate to the repository,
  and translate the repository's boolean result + error list into a return value. `StudentApiServices`
  (API) and `StudentsServices` (admin) are near-duplicates that differ only in how they read `student` id
  from the request.

- **Repository** (`app/Repositories/Repository.php`, implements `RepositoryInterface`) is a single generic
  CRUD class usable against any `Model`. `create`/`update` take a `FactoryInterface` + `\stdClass $data`;
  `delete`/`getAll`/`getById` take a `Model` instance. It wraps saves in try/catch and records failures via
  `addError()`. Per-entity subclasses (`StudentsRepository`, `SchoolsRepository`) are currently empty.

- **`*FactoryDB`** classes (`app/Factories/`, implement `FactoryInterface`) do `make(\stdClass $data): Model`
  — hydrate a new model, or `Model::find($data->id)` when `$data->id` is set, from a plain data object.
  These are **not** Laravel database factories; those live in `database/factories/` and are used only by
  tests and seeders.

- **Error propagation**: `App\Errors\Errors` provides `addError($msg, $dropPrevious = false)` / `getErrors()`.
  Both `Services` and `Repository` extend it. Errors bubble outward: repository catches exceptions →
  service copies them with `foreach ($repo->getErrors() ...)` → controller serializes them in the response.

### Student ordering

- `Student::boot()` registers a `saving` hook that sets `order = School::getOrder($school_id)`
  (last student's `order` + 1, or 1/0). This runs on **every** normal save.
- `php artisan students:order` (`app/Console/Commands/FixStudentOrder.php`) iterates non-trashed schools,
  reassigns `order` 1..n, and uses `saveQuietly()` to bypass the `saving` hook. It then fires
  `SendEmailEvent`; the `StudentsOrderCommandEnd` listener (mapped in `EventServiceProvider`) creates 50
  students via factory and emails user id 1 the `StudentsOrdered` mailable (`emails.students_ordered` view).

### Auth

- **API**: every `routes/api.php` resource route is behind `auth:api`. The `api` guard uses the **passport**
  driver (`config/auth.php`); `User` uses `Laravel\Passport\HasApiTokens`. Requests 500 with
  `LogicException: Invalid key supplied` if `storage/oauth-*.key` are missing (`php artisan passport:keys`).
  Mint a bearer token with `$user->createToken('name')->accessToken` (needs the personal-access client).
- **Admin**: `routes/auth.php` puts `schools`/`students` resource routes under an `admin` prefix guarded by
  the `auth` (session) middleware; Breeze provides the login/register scaffolding.
- Sanctum is still a dependency (Breeze pulls it) but is unused on the API path.

### Models

`School` hasMany `Student`; `Student` belongsTo `School`. Both use `SoftDeletes`.
`students` has a name/status/order schema plus a `school_id` FK added in a separate migration.

## Key paths

- Framework wiring: `bootstrap/app.php`, `bootstrap/providers.php`
- API: `app/Http/Controllers/Students/StudentApiController.php`, `routes/api.php`
- Admin: `app/Http/Controllers/{Students,Schools}/*AdminController.php`, `routes/auth.php`
- Form requests: `app/Http/Requests/Students/` (base) and `.../Students/Api/` (API subclasses that override
  `failedValidation()` to return JSON instead of redirecting)
- Postman collection: `schools_students_Api_crud.postman_collection.json` (password-grant login auto-captures
  the token; import and run "Auth → Login" first)
- Original task spec: `TASK.md` · full setup + API reference: `README.md`

## Tooling

- **Laravel Boost** is installed. Its MCP server (`php artisan boost:mcp`) is declared in `.mcp.json` and
  enabled for Claude Code via `.claude/settings.local.json` (`enabledMcpjsonServers`). Boost's own
  guidelines are appended below this section — follow them.
- `boost:install` requires `APP_ENV=local` or `APP_DEBUG=true`, otherwise its Artisan commands don't
  register at all.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
