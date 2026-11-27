#CodeBarkada Hub

## Environment
- Debian 13, PHP 8.4, Laravel 13, MariaDB. Database `CodeBarkada_Club`, user `ida`.
- Run the app with `php artisan serve` (http://localhost:8000). Tests: `php artisan test`.
- No npm, no Vite, no CDN links.
- Frontend: Bootstrap 5.3.9, downloaded into `public/vendor/bootstrap/` (see "Frontend (Bootstrap)" below). Our own CSS is in `public/css/school.css`.

## Architecture (the restaurant)
- Routes only in `routes/web.php`.
- Controllers (`app/Http/Controllers`) stay thin: get the models, call the service, return a view. No calculations.
- School rules live only in `app/Services/CodeBarkadaService.php`.
- Models (`app/Models`) hold relationships and small helpers such as `fullName()`. No grading rules.
- Views (`resources/views`) only display data. No queries, no calculations. Every page extends `layouts/app.blade.php`.
- Change the database only with migrations. Never edit a migration that has already run; tell me instead.
- Load relationships with `with()` or `load()` so a page never runs one query per row.

## Design (Balangay theme)
- The color theme follows the Balangay Festival: a royal-blue sea and sky, with the boat's sail colors (yellow, red, blue) as accents and cream text like the outrigger hull.
- Colors are CSS variables at the top of `public/css/school.css` (section 1), and Bootstrap is re-colored from them (section 2). Use the variables, never new hard-coded colors.

| Variable | Color | Use |
|---|---|---|
| `--ink` | `#0b1f5e` deep royal blue | page background, text on yellow buttons |
| `--sea` | `#1d47b5` bright royal blue | glow at the top of the page, stripe |
| `--panel` | `#112b78` | cards |
| `--panel2` | `#17368f` | table headers, pills, plain buttons |
| `--line` | `#2d4fae` | borders |
| `--text` | `#fbf7ea` cream | main text |
| `--muted` | `#b3c2e8` | hints and secondary text |
| `--sun` | `#f9c935` sail yellow | main accent, primary buttons, headings highlight |
| `--sail-red` | `#e0322b` sail red | header stripe, danger card border, Delete button (white text on it passes 4.5:1); too dark to use as text on blue |
| `--coral` | `#ff8a6b` light sail red | readable red text, warnings |
| `--sky` | `#5ec0f2` sky blue | links, focus rings |
| `--good` / `--bad` | `#5fd3a5` / `#ff8f8f` | success / error |

- The header has a yellow–red–blue sail stripe along its bottom edge.
- Keep text readable: at least 4.5:1 contrast against its background (check before adding a new color pair).

## Frontend (Bootstrap)
- Bootstrap 5.3.9 files live in `public/vendor/bootstrap/` (`css/bootstrap.min.css`, `js/bootstrap.bundle.min.js`, `LICENSE`). They were downloaded once from the official npm package and committed, so the site works offline in the lab. Do not edit them; to upgrade, download the new version's files and replace them (ask me first).
- `layouts/app.blade.php` loads `bootstrap.min.css`, then `school.css` (ours wins), and `bootstrap.bundle.min.js` at the end with `defer`. No jQuery, no other libraries.
- The page uses Bootstrap's dark mode (`<html data-bs-theme="dark">`). `school.css` re-colors Bootstrap to the Balangay theme by overriding its CSS variables (`--bs-body-bg`, `--bs-primary`, `.btn-primary`, `.card`, `.table`, …). To change a color, change it there, not in the views.
- Use Bootstrap classes first: grid (`container`, `row`, `col-*`), spacing/flex utilities, `card`, `btn btn-primary` / `btn-outline-light`, `form-control`, `form-label`, `form-text`, `form-check`, `table table-hover` inside `table-responsive`, `alert`, `badge`, `navbar`.
- Add a custom class in `school.css` only for things Bootstrap doesn't have (boat-bot, avatars, track cards, color swatches, status badge tones, later the typing box and quiz). Never re-define Bootstrap class names such as `.row`, `.btn` or `.card` from scratch.
- Form errors: add `is-invalid` to the field with `@class([... 'is-invalid' => $errors->has('field')])`, and show the message with `@error('field') <div class="invalid-feedback">{{ $message }}</div> @enderror`. Use `form-text` for hints.
- Radio choices that look like cards or swatches use Bootstrap's `btn-check` input followed by its label.
- Use Bootstrap's JavaScript only through `data-bs-*` attributes (navbar toggler, collapse, modals). Pages must still work if JavaScript is off.
- Check every page at phone width (about 390px): the navbar folds into the ☰ button and tables scroll sideways.

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
