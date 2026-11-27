#CodeBarkada Hub

## Environment
- Debian 13, PHP 8.4, Laravel 13, MariaDB. Database `CodeBarkada_Club`, user `ida`.
- Run the app with `php artisan serve` (http://localhost:8000). Tests: `php artisan test`.
- No npm, no Vite, no CDN links. The CSS is plain, in `public/css/school.css`.

## Architecture (the restaurant)
- Routes only in `routes/web.php`.
- Controllers (`app/Http/Controllers`) stay thin: get the models, call the service, return a view. No calculations.
- School rules live only in `app/Services/CodeBarkadaService.php`.
- Models (`app/Models`) hold relationships and small helpers such as `fullName()`. No grading rules.
- Views (`resources/views`) only display data. No queries, no calculations. Every page extends `layouts/app.blade.php`.
- Change the database only with migrations. Never edit a migration that has already run; tell me instead.
- Load relationships with `with()` or `load()` so a page never runs one query per row.

## Users
- There are only 2 users - The club adviser and Club members

## App Objectives
- A platform for coding club to hone their skills and help them prepare for CS competitions.
- Create a skill profiling system from typing speed and logics on coding

## How to work with me
- I am learning Laravel. For every build request, show a short plan first and wait for my OK. Work in small steps.
- After each change, explain each new or changed file in 2 or 3 plain sentences, and tell me one command or URL to check it works.
- Ask before installing anything (`composer require`, npm, Laravel Boost) and before running `php artisan migrate:fresh`.
- Do not edit `.env` or anything in `vendor/`.
