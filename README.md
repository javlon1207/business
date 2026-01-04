# Business Calendar (PHP + MySQL + JWT + FullCalendar)

## Features
- Event fields: name, start_time, location, agenda, participants (last_name, first_name, position via users table)
- Admin:
  - Create users (default password: 123)
  - Create events and attach participants
  - See participant statuses
- User:
  - See invited events in calendar
  - Notifications (invite messages)
  - Confirm / Reject invitation (status tracked)
- JWT auth (HS256) using pure PHP

## Requirements
- PHP 7.4+ (works on 8.x)
- MySQL/MariaDB
- Apache with mod_rewrite enabled (OpenServer is OK)

## Setup
1) Create DB and import schema:
   - Open `db/schema.sql` in your MySQL client and run it.

2) Configure DB:
   - Edit `config/config.php`

3) Run in OpenServer:
   - Project folder as domain, e.g. `business`
   - Ensure Apache + PHP enabled
   - Open:
     - Login: `http://business/login.html` (if DocumentRoot=project root) or `http://business/public/login.html` (if DocumentRoot=project root and using /public), or `http://business/` (if DocumentRoot=public)
     - Admin: `http://business/admin.html` or `http://business/public/admin.html`
     - User: `http://business/user.html` or `http://business/public/user.html`

## Default accounts (created by schema.sql)
- Admin:
  - email: admin@local
  - password: 123
- User:
  - email: user1@local
  - password: 123

## API quick test
- POST /api/auth/login  { "email":"admin@local", "password":"123" }
- Use returned token: Authorization: Bearer <token>


## v2 Updates
- events.end_time (optional) added
- Migration file: db/migration_add_end_time.sql
