# Laravel React Issue Tracker

[![tests](https://github.com/pancakebaker/laravel-react-issue-tracker/actions/workflows/tests.yml/badge.svg)](https://github.com/pancakebaker/laravel-react-issue-tracker/actions/workflows/tests.yml)
![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![React 19](https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react&logoColor=111111)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=flat-square)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-local-003B57?style=flat-square&logo=sqlite&logoColor=white)
![Auth](https://img.shields.io/badge/Auth-enabled-2E7D32?style=flat-square)
![PHPUnit](https://img.shields.io/badge/PHPUnit-11-366488?style=flat-square)

An authenticated issue-management application built with Laravel 12, React 19, TypeScript, Inertia.js, and Tailwind CSS. It includes ticket CRUD, dashboard summaries, server-side validation and filtering, pagination, and a responsive interface.

## Features

- Authenticated ticket management
- Dashboard with ticket status summaries
- Recent tickets on the dashboard
- Dashboard shortcuts into all, open, in-progress, and resolved ticket views
- Create, read, update, and delete tickets
- Status, priority, and category backed enums
- Status and priority filtering
- Paginated ticket list
- Server-side validation surfaced in React
- Success flash messages
- Responsive dashboard, ticket index, and detail views
- Feature tests covering dashboard and ticket workflows

## Tech Stack

- Laravel 12
- PHP 8.2+
- React 19
- TypeScript
- Inertia.js
- Tailwind CSS
- SQLite
- PHPUnit
- Vite

## Local Setup

Install dependencies and prepare the local SQLite database:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
```

Start the development environment:

```bash
composer run dev
```

This starts the Laravel server, queue listener, application logs through Pail, and Vite development server together.

Open:

```text
http://localhost:8000
```

If `composer run dev` is unavailable because Pail's `pcntl` requirement is not supported in the local environment, use the two-terminal fallback.

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

## Demo Login

Public registration is disabled because this project is framed as an internal issue tracker. The database seeder creates one local demo user:

Email: `test@example.com`  
Password: `password`

These credentials are for local/demo use only.

## Scope

This project is intentionally implemented as a focused issue-tracking application rather than a full project-management platform. The emphasis is on clear Laravel/Inertia architecture, validation, maintainability, testing, and a reviewer-friendly local setup.

## Architecture Decisions

### Why Laravel + Inertia + React

Inertia allows the application to use React for the UI while keeping Laravel routing, validation, redirects, and controller-driven application flow. A separate REST API would be appropriate if independent clients, mobile apps, or external consumers became requirements, but it would add unnecessary surface area for this application.

### Validation

Laravel Form Requests are the authoritative validation layer for creating and updating tickets. Inertia carries Laravel validation errors back to the React form, where they are displayed next to the relevant fields.

### PHP Enums

Ticket status, priority, and category are modeled as backed PHP enums. This keeps finite domain values centralized and avoids scattering string literals through the backend. The enum options are passed from Laravel to React, so the frontend does not maintain a second source of truth.

### Architecture Scope

The application intentionally avoids service or repository layers because the current behavior is straightforward CRUD. Introducing additional abstraction at this size would add indirection without separating meaningful business logic. If workflows, integrations, or domain rules became more complex, those responsibilities could be extracted as needed.

### Database

SQLite was chosen for easy reviewer setup, no external database service, and enough capability for this project. The implementation uses Eloquent models and migrations, so moving to MySQL or PostgreSQL would mainly be a configuration and deployment decision.

## Filtering and Pagination

Status and priority filters are handled server-side through query parameters. Valid filter values are resolved through the PHP enums, invalid values are ignored, and Laravel pagination preserves active query parameters across pages.

## Testing and Quality Checks

Run the test suite:

```bash
php artisan test
```

The suite covers the authentication boundary, ticket CRUD, validation, filtering, pagination, summary counts, and model enum/date casting.

Latest verified result:

```text
48 tests passed
347 assertions
```

Additional checks used during development:

```bash
vendor/bin/pint --test
npx tsc --noEmit
npx eslint .
npm run format:check
npm run build
npm audit
composer audit
```

The latest verification pass completed successfully for PHPUnit/Laravel tests, Pint, TypeScript type checking, ESLint, formatting check, and production Vite build.

## AI Usage

ChatGPT/Codex was used during implementation, review, and iteration. I verified generated changes through tests, static analysis, dependency audits, and code review rather than accepting them automatically.

One example of this verification came up during factory development. AI suggested a nullable Faker chain equivalent to:

```php
fake()->optional(...)->dateTimeBetween(...)->format(...)
```

Because `optional()` can return `null`, `format()` could be called on `null`, and `php artisan migrate:fresh --seed` exposed the failure. The factory was corrected to explicitly branch between a generated/formatted date and `null`.

## Security / Configuration

- Ticket routes require authentication.
- Public self-registration is disabled.
- Laravel's standard CSRF protection applies to form submissions.
- Environment-specific configuration is kept in `.env`, which is excluded from version control.

## Future Improvements

- User-backed ticket assignment instead of free-text assignees
- Authorization policies or roles if multiple permission levels are required
- Search and sortable table columns
- Notifications or SLA workflow support
- PostgreSQL or MySQL configuration for a deployed environment
