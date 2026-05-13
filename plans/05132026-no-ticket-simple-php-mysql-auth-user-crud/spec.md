# Simple PHP MySQL Auth User CRUD

## Why
Create a small PHP starter app that demonstrates login, signup, and user management against a MySQL database. This gives the project a working baseline instead of an empty repository.

## What
A simple PHP app using HTML, CSS, JavaScript, Bootstrap, and MySQL where users can sign up, log in, view a dashboard, add users, list users, edit users, and delete users.

## Context

**Relevant files:**
- None yet. This is a greenfield app.

**Patterns to follow:**
- Use plain PHP with shared includes for database connection, session/auth helpers, layout header, and layout footer.
- Use PDO prepared statements for all database access.
- Use Bootstrap through CDN for simple UI styling.
- Use one shared CSRF helper for all mutating forms.

**Key decisions already made:**
- Database engine is MySQL.
- Primary schema artifact is `migrations/mysql.sql`.
- Passwords must be stored with `password_hash()` and verified with `password_verify()`.
- Auth state is managed with PHP sessions.
- User CRUD is available only after login.
- Local database defaults are `localhost`, database `phpapp_crud`, username `root`, and empty password. Credentials are edited directly in `config/database.php` for this simple no-dependency app.

## Constraints

**Must:**
- Keep the app simple and readable.
- Use prepared statements for every query.
- Validate required fields server-side.
- Redirect unauthenticated visitors away from dashboard and user management pages.
- Escape rendered user-controlled data with `htmlspecialchars()`.
- Regenerate session IDs after successful login/signup.
- Require CSRF tokens for create, update, and delete requests.
- Require password when creating a user from the dashboard.
- Keep password optional when editing a user; blank means keep the existing password.
- Prevent the logged-in user from deleting their own account.

**Must not:**
- Store plaintext passwords.
- Trust client-side validation as security.
- Add a framework or dependency manager unless explicitly requested.
- Add environment parsing or extra config dependencies.
- Mix unrelated refactors or unrelated features into this work.

**Out of scope:**
- Roles and permissions.
- Password reset.
- Email verification.
- Pagination/search.
- Production deployment hardening beyond basic safe defaults.

## Risk

**Level:** 2

**Risks identified:**
- Auth implemented directly in every page can become duplicated and inconsistent -> **Mitigation:** centralize session and auth checks in shared include files.
- CRUD forms can become SQL injection points -> **Mitigation:** use PDO prepared statements only.
- A simple admin-style dashboard could allow any logged-in user to manage all users -> **Mitigation:** acceptable for this starter scope, but call out that roles are out of scope.
- Mutating forms without CSRF protection can be triggered from another site -> **Mitigation:** add shared CSRF token generation and validation for create, edit, and delete.
- Deleting the current logged-in account can leave the session pointing to a missing user -> **Mitigation:** block self-delete in the delete handler.

**Pushback:**
- Managing all users as any logged-in user is not a production-ready permission model. Future-us will hate this if it becomes real business logic. If this app grows, add roles before adding sensitive user fields or real customer data.

## Tasks

### T1: Database Schema
**Do:** Create MySQL-compatible schema for a `users` table with identity, name, unique email, hashed password, and timestamps. Fill the MSSQL, PostgreSQL, and Knex migration artifacts with equivalent schema and rollback behavior so the migration files do not remain placeholders.
**Files:** `plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/mysql.sql`, `plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/mssql.sql`, `plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/pgsql.sql`, `plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/knexmigration.js`
**Verify:** Manual: `mysql.sql` can be applied to MySQL; all migration artifacts include required columns, constraints, and rollback intent.

### T2: PHP App Foundation
**Do:** Add shared PHP configuration, PDO connection, auth/session helpers, CSRF helpers, and simple Bootstrap layout includes. `config/database.php` uses local defaults and can be edited directly for credentials.
**Files:** `config/database.php`, `includes/auth.php`, `includes/header.php`, `includes/footer.php`, `assets/css/styles.css`, `assets/js/app.js`
**Verify:** `php -l` on all PHP files.

### T3: Auth Flow
**Do:** Add signup, login, logout, and protected dashboard routing. Regenerate the session ID after successful signup/login and fully clear session data on logout.
**Files:** `index.php`, `signup.php`, `login.php`, `logout.php`, `dashboard.php`
**Verify:** Manual: signup creates a user, login starts a session, logout ends it, protected pages redirect when unauthenticated.

### T4: User CRUD
**Do:** Add create, list, edit, and delete behavior for users from the dashboard using server-side validation, CSRF validation, and prepared statements. Create requires a password. Edit only changes password when a new value is provided. Delete blocks deleting the logged-in user.
**Files:** `users/create.php`, `users/edit.php`, `users/delete.php`, `dashboard.php`
**Verify:** Manual: add, list, update, and delete user records successfully.

## Done
- [ ] `php -l` passes for all PHP files.
- [ ] Manual: database schema can be applied to MySQL.
- [ ] Manual: signup and login work with hashed passwords.
- [ ] Manual: dashboard is protected by login.
- [ ] Manual: user create, list, update, and delete work.
- [ ] Manual: create/edit/delete reject missing or invalid CSRF tokens.
- [ ] Manual: editing a user with blank password keeps the existing password.
- [ ] Manual: logged-in user cannot delete their own account.
- [ ] No plaintext password storage.

## Revision Log

### Rev 1 - May 13, 2026
**Change:** Added the primary MySQL schema file, explicit local database defaults, CSRF requirements, session hardening, dashboard password behavior, self-delete protection, and non-placeholder migration artifact requirements.
**Reason:** The first spec left execution-critical behavior ambiguous and omitted the MySQL schema artifact even though MySQL is the target database.
**Updated Done criteria:** Added CSRF rejection, optional edit password behavior, and self-delete protection checks.
