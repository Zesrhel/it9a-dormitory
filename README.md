# Dormitory Management System

A Laravel-based dormitory management application for handling student housing requests, room management, and allocation workflows.

## Overview

This system allows:

- Students to register and apply for dormitory accommodation
- Admins to manage users, rooms, and system activity
- Dorm managers to review applications and assign rooms
- System operators to track allocations and application status changes

## Features

- Role-based authentication and authorization
- Student dormitory application submission and review
- Room management with availability tracking
- Allocation creation and status updates
- User profile management and profile pictures
- Activity logging for administrative actions
- Dashboard with summary statistics

## Tech Stack

- PHP 8.2
- Laravel 12
- SQLite (default for local development)
- Blade templates
- Vite for frontend assets

## Project Structure

- `app/` - application logic, controllers, models, and middleware
- `database/migrations/` - database schema definitions
- `database/seeders/` - seed data for the system
- `resources/views/` - Blade templates for the UI
- `routes/web.php` - application routes
- `public/` - public assets and generated build files

## Database Setup

This project is configured to use SQLite by default.

1. Ensure `database/database.sqlite` exists.
2. If needed, create it:

```bash
touch database/database.sqlite
```

3. Configure the environment:

```bash
cp .env.example .env
php artisan key:generate
```

4. Confirm the database config in `.env` uses SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite
```

## Installation

```bash
composer install
npm install
```

## Run the Application

```bash
php artisan migrate
npm run dev
php artisan serve
```

Then open:

```text
http://localhost:8000
```

## Development Notes

- The app uses a database-backed session and cache configuration by default.
- For local development, the default SQLite database file is stored in `database/database.sqlite`.
- The project includes a `composer setup` script in `composer.json` for a quick bootstrap flow.

## License

This project is licensed under the MIT license.
