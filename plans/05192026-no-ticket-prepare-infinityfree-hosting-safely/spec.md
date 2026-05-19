# Prepare InfinityFree Hosting Safely

## Why
The app is intended to run on InfinityFree as a plain PHP + MySQL site, but it currently stores real database credentials directly in `config/database.php`. That is unsafe for source control and risky for shared hosting, especially because InfinityFree serves the app from `htdocs`.

## What
Prepare the app for safe InfinityFree deployment by moving database credentials out of committed PHP, adding ignored local environment configuration, blocking direct access to sensitive project files, and documenting the exact upload/database setup path for InfinityFree.

## Context

**Relevant files:**
- `config/database.php` - creates the PDO connection and currently contains hardcoded local credentials.
- `plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/mysql.sql` - contains the MySQL `users` table schema needed for InfinityFree phpMyAdmin import.
- `index.php`, `login.php`, `signup.php`, `dashboard.php`, `users/*.php` - public PHP entry points that depend on the database connection.
- `assets/`, `includes/`, `config/` - runtime app directories expected to be uploaded with the site.
- `plans/`, `.git/`, `AGENTS.md`, `.agents/`, `.codex/` - local/project-control artifacts that should not be deployed as public site content.

**Patterns to follow:**
- Keep this a dependency-free plain PHP app.
- Keep `config/database.php` as the single source for PDO creation.
- Keep database connection failures generic to avoid leaking hostnames, usernames, passwords, or raw PDO errors.
- Use simple project-root config files rather than adding Composer or framework conventions.

**Key decisions already made:**
- Target host is InfinityFree free PHP hosting.
- Runtime deployment root is InfinityFree `htdocs`.
- Database setup is through InfinityFree MySQL Databases and phpMyAdmin.
- Remote MySQL access from a local machine is not available on InfinityFree free hosting; only hosted PHP scripts and InfinityFree phpMyAdmin should access the database.
- `plans/05192026-no-ticket-prepare-infinityfree-hosting-safely/spec.md` is the active source of truth for this work. The older `plans/05132026-no-ticket-add-env-gitignore-infinityfree-hosting-safety/spec.md` is superseded and must not drive execution.
- A real `.env` file must not be committed. Implementation should commit `.env.example` only; the user creates `.env` locally and on InfinityFree from the documented template.

## Constraints

**Must:**
- Remove real database credentials from committed PHP files.
- Add `.env.example` with safe placeholder keys for `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, and `DB_CHARSET`.
- Add `.gitignore` entries for `.env`, local/editor files, dependency output, logs, temporary files, and non-deployable artifacts.
- Refactor `config/database.php` to load database settings from `.env` without adding external dependencies.
- Preserve existing PDO options and strict connection behavior.
- Add `.htaccess` rules to deny direct browser access to secrets and non-public project files when uploaded under `htdocs`.
- Add concise deployment notes that explain InfinityFree `htdocs`, phpMyAdmin schema import, and database credential setup.
- Document the exact files/directories to upload to InfinityFree: `index.php`, `login.php`, `logout.php`, `signup.php`, `dashboard.php`, `assets/`, `config/`, `includes/`, `users/`, `.htaccess`, and the user's untracked `.env`.
- Document the exact files/directories not to upload: `.git/`, `plans/`, `AGENTS.md`, `.agents/`, and `.codex/`.

**Must not:**
- Add Composer, `vlucas/phpdotenv`, or any new dependency.
- Echo raw database exception messages to users.
- Change authentication, CRUD behavior, routing, or page layout.
- Refactor unrelated PHP pages.
- Upload or document uploading `.git/`, `plans/`, `.agents/`, `.codex/`, or `AGENTS.md` as required production files.
- Commit a real `.env` file or any real database password.

**Out of scope:**
- Domain/DNS setup.
- SSL setup.
- Automated FTP deployment.
- Production-grade secret management outside InfinityFree’s shared-hosting constraints.
- Database schema changes beyond using the existing `users` table.

## Risk

**Level:** 3

**Risks identified:**
- Real credentials are currently hardcoded in `config/database.php` -> **Mitigation:** move all secrets into ignored `.env` configuration and keep only safe placeholders committed.
- `.env` may need to live under `htdocs` on InfinityFree, which is weaker than keeping secrets outside the document root -> **Mitigation:** add `.htaccess` denial rules and document that only necessary runtime files should be uploaded.
- Free InfinityFree hosting blocks external MySQL connections -> **Mitigation:** document that schema/data work must be done through InfinityFree phpMyAdmin or PHP scripts hosted on the account.
- If the existing hardcoded password was ever pushed to a remote repository, removing it from the current file is not enough -> **Mitigation:** recommend rotating the local/database password after implementation if the repo has ever left the machine.
- Creating `.env` before `.gitignore` exists can accidentally stage secrets -> **Mitigation:** add `.gitignore` before any local `.env` handling, commit only `.env.example`, and leave real `.env` creation to the user/deployment notes.
- Multiple similar plan folders can confuse execution and finalization -> **Mitigation:** mark this May 19 plan as active and the May 13 hosting-safety plan as superseded.

**Pushback:**
- So, hear me out: putting config secrets anywhere under a web root is not ideal. Future-us will hate relying on `.htaccess` as the only barrier. InfinityFree’s free-hosting model makes a cleaner outside-document-root secret path awkward, so the pragmatic path is `.env` plus denial rules, but this should not be treated as enterprise-grade secret management.

## Tasks

### T1: Ignore Rules And Environment Template
**Do:** Add `.gitignore` before any real secret handling, and add `.env.example` with placeholder-only database configuration. Do not create or commit a real `.env`.
**Files:** `.gitignore`, `.env.example`
**Verify:** `git check-ignore .env`; manual: `.env.example` contains placeholders only and no real credentials.

### T2: Environment-Based Database Config
**Do:** Refactor `config/database.php` so database credentials are loaded from project-root `.env` without external dependencies. Keep safe defaults only for non-secret values, preserve PDO options, and keep the connection failure message generic.
**Files:** `config/database.php`
**Verify:** `php -l config/database.php`; manual: app creates `$pdo` when `.env` contains valid database values.

### T3: Web Access Protection
**Do:** Add `.htaccess` rules that deny direct browser access to `.env`, Git metadata, control-agent files, plans, config internals, and other non-public project paths when deployed under InfinityFree `htdocs`.
**Files:** `.htaccess`
**Verify:** Manual: direct browser requests to `/.env`, `/config/database.php`, `/plans/`, `/.git/`, `/AGENTS.md`, `/.agents/`, and `/.codex/` are denied when Apache honors `.htaccess`.

### T4: InfinityFree Deployment Notes
**Do:** Add deployment instructions explaining the exact upload list, exact non-upload list, MySQL database creation, phpMyAdmin schema import, `.env` creation from `.env.example`, InfinityFree credential placement, and the lack of remote MySQL access on free hosting.
**Files:** `README.md`
**Verify:** Manual: README includes `htdocs`, phpMyAdmin import, `.env`, InfinityFree DB credentials, upload list, non-upload list, and remote MySQL limitation.

## Done
- [ ] `php -l config/database.php` passes.
- [ ] `find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l` passes.
- [ ] App still creates the PDO connection from `.env` values.
- [ ] `.env.example` contains placeholders only.
- [ ] `.env` is ignored by Git.
- [ ] No real `.env` file is committed.
- [ ] Real DB credentials are removed from committed PHP files.
- [ ] `.htaccess` blocks direct access to `.env` and non-public project/control paths.
- [ ] README documents InfinityFree `htdocs`, phpMyAdmin import, MySQL credential setup, upload list, non-upload list, and remote MySQL limitation.
- [ ] No new dependencies are added.

## Revision Log

### Rev 1 - May 19, 2026
**Change:** Reordered implementation tasks so `.gitignore` and `.env.example` come before database config changes, clarified that real `.env` must not be committed, marked this May 19 spec as the active source of truth, added exact InfinityFree upload/non-upload lists, separated `.htaccess` into its own task, and broadened PHP syntax verification to all PHP files.
**Reason:** Review found ambiguity around secret handling, duplicate hosting-prep specs, and too-narrow verification.
**Updated Done criteria:** Added full PHP lint verification, no-committed-`.env` confirmation, and README coverage for upload lists plus remote MySQL limitation.
