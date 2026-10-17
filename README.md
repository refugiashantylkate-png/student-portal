# Student Portal

Full-stack web application (PHP + MySQL) built for the Web Application Development final project.
Admins manage student grades (full CRUD). Students register, view their own grades and average, and edit their profile.

## Live Site
https://shantylkate.infinityfree.me

## Test Credentials
**Admin**
- Email: `admin@portal.com`
- Password: `Admin@123`

**Student:** create one from the Sign Up page on the live site.

## Tech Stack
HTML5, CSS3, vanilla JavaScript, PHP (PDO), MySQL. Hosted on InfinityFree.

## Features
- User registration and login with role-based access (admin and student)
- Password hashing (`password_hash`) and secure sessions (`session_regenerate_id`)
- Full CRUD on grades (admin), with search and per-student averages
- Student view limited to own grades; profile page to update name and password
- Client-side and server-side validation
- Protection against SQL injection (prepared statements), XSS (output escaping), and CSRF (tokens)
- Responsive layout

## Setup
1. Create a MySQL database and import `database/schema.sql`.
2. Edit `config.php` with your database host, name, user, password, and port.
3. Upload all files to your web root (`htdocs`).
4. Open `/create_admin.php` once to create the admin account, then delete that file.

## Project Structure
- `index.php` login
- `register.php` student registration
- `dashboard.php` grades list (admin CRUD / student view)
- `grade_form.php` add and edit grade (admin)
- `profile.php` update profile
- `db.php` database connection, session, and helpers
- `database/schema.sql` database schema
