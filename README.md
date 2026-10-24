# Restaurant Table Reservation System

A web-based reservation management system for a restaurant. Users register, browse available time slots by date, book a table, and manage their reservations. Admins manage reservations, users, and time slots from a dashboard with live statistics.

**Repository:** https://github.com/Ahmad-alhwari0/restaurant-reservation-system

**Project page:** https://ahmad-alhwari0.github.io/restaurant-reservation-system/

> The project page shows this README. To run the full PHP application, follow the [Installation](#installation) steps below.

## Features

### User side
- Register, log in, and log out
- View available time slots by date with remaining capacity
- Book a time slot with an optional note (new reservations start as **Pending**)
- **My Reservations** page showing date, time, and status (Pending / Confirmed / Rejected / Cancelled)
- Cancel own reservations before the time slot starts

### Admin dashboard
- Statistics: total reservations, pending, confirmed, and registered users
- Doughnut chart of reservations by status (Chart.js)
- Manage reservations: table, search by name or email, filter by status, confirm / reject / cancel
- Manage users: activate or deactivate accounts
- Manage time slots: add, edit, and delete (full CRUD)

## Business Rules

| # | Rule | Enforced in |
|---|------|-------------|
| 1 | Past time slots cannot be booked | `Reservation::create()` |
| 2 | A time slot cannot exceed its capacity | `Reservation::create()` |
| 3 | A user cannot book the same slot twice | `Reservation::create()` + `UNIQUE(user_id, slot_id)` |
| 4 | Cancelled / rejected reservations do not count toward capacity | `TimeSlot` queries (`status IN ('pending','confirmed')`) |
| 5 | Slots with pending or confirmed reservations cannot be deleted | `TimeSlot::delete()` |
| 6 | Only admins can confirm or reject reservations | `Auth::requireAdmin()` + `Reservation::updateStatus()` |
| 7 | Users can view and cancel only their own reservations | `WHERE user_id = ?` using the session user id |
| 8 | Deactivated accounts cannot log in | `User::login()` |

## Technologies

- **PHP 8** (OOP, PDO, sessions) — no framework
- **MySQL / MariaDB**
- **Bootstrap 5** (responsive UI)
- **Chart.js 4**
- **Git & GitHub**
- **XAMPP** (Apache + MySQL) for local development

## Security

- Passwords hashed with `password_hash()` and checked with `password_verify()`
- Session-based authentication with `session_regenerate_id()` on login
- Role-based access control on the server (`Auth::requireLogin()`, `Auth::requireAdmin()`)
- Prepared statements for every query with user input (SQL injection protection)
- `htmlspecialchars()` on all output (XSS protection)
- Server-side validation with clear error messages
- Database credentials kept out of the repository (`config/db.php` is git-ignored)

## Project Structure

```
restaurant-reservation-system/
├── admin/                 # Admin pages (dashboard, reservations, users, slots)
├── assets/                # CSS, JS, images
├── config/
│   ├── db.example.php     # Database config template (copy to db.php)
│   └── db.php             # Your local config (not in the repo)
├── database/
│   ├── schema.sql         # Tables
│   └── seed.sql           # Demo data
├── includes/
│   ├── init.php           # Session, timezone, DB connection, class loading
│   ├── Database.php       # PDO connection
│   ├── Auth.php           # Login / admin guards
│   ├── User.php           # users table
│   ├── TimeSlot.php       # time_slots table + remaining capacity
│   ├── Reservation.php    # reservations table + booking rules
│   ├── header.php
│   └── footer.php
├── index.php              # Redirects by role
├── login.php / register.php / logout.php
├── slots.php              # Available slots + booking
└── my-reservations.php    # User reservations + cancel
```

## Installation

1. **Install XAMPP** and start **Apache** and **MySQL**.
2. **Clone the repository** into the `htdocs` folder:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/Ahmad-alhwari0/restaurant-reservation-system.git
   ```
3. **Create the database** in phpMyAdmin (`http://localhost/phpmyadmin`):
   - Name: `reservation_db`
   - Collation: `utf8mb4_general_ci`
4. **Import the SQL files** (select `reservation_db` → **SQL** tab), in this order:
   1. `database/schema.sql`
   2. `database/seed.sql`
5. **Create the config file:** copy `config/db.example.php` to `config/db.php` and update the values if your MySQL user or password is different.
6. **Open the app:** `http://localhost/restaurant-reservation-system/`

> Re-running `schema.sql` drops and recreates all tables. Run `seed.sql` again after it to restore the demo data.

## Demo Accounts

| Role | Email | Password | Note |
|------|-------|----------|------|
| Admin | `admin@restaurant.com` | `admin123` | |
| User | `ahmad@test.com` | `user123` | |
| User | `sara@test.com` | `user123` | |
| User | `omar@test.com` | `user123` | **Deactivated** (cannot log in) |

The seed data creates time slots relative to the current date (tomorrow, in 2 days, in 3 days, and one in the past), plus reservations in every status, so all features and rules can be tested right away.

## Testing Checklist

- [x] Registration and login work correctly
- [x] Invalid inputs are rejected with clear messages
- [x] Users cannot access admin pages (HTTP 403)
- [x] Users cannot cancel other users' reservations
- [x] Past time slots cannot be booked
- [x] Duplicate reservations are prevented
- [x] Slot capacity is enforced
- [x] Reservation status changes appear on the user side
- [x] Deactivated users cannot log in
- [x] Time slots with active reservations cannot be deleted