# Simple PHP + MySQL Setup

## Files

- `config.php`: database connection settings
- `schema.sql`: create database and tables
- `login.php`: login page
- `signup.php`: create user
- `dashboard.php`: summary page
- `student.php`, `vendor.php`, `publication.php`, `subscription.php`, `book.php`, `purchase.php`, `allotment.php`, `allotment-history.php`: basic tasks

## MySQL setup

1. Create a MySQL database by running `schema.sql`.
2. Update `config.php` with your MySQL username and password.
3. Put the `simple-html` folder inside your PHP server root, or run it with PHP locally.

## Default login

- Email: `admin@example.com`
- Password: `admin123`

## Example local run

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000/simple-html/login.php
```
