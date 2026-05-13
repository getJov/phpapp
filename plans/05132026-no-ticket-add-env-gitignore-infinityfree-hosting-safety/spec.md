# Add Env Gitignore InfinityFree Hosting Safety

## Why
The app currently stores database credentials directly in `config/database.php`, which is unsafe for source control and risky when uploading to shared hosting. Before hosting on InfinityFree, secrets need to move into an ignored local config file and sensitive files need web-access protection.

## What
Add `.env`-based database configuration, a committed `.env.example`, a `.gitignore` that excludes real secrets and local/runtime files, and web-server protection for sensitive dotfiles. Refactor `config/database.php` to load credentials from `.env` without adding Composer or external dependencies.

## Context

**Relevant files:**
- `config/database.php` - currently contains hardcoded DB credentials and creates the PDO connection.
- `AGENTS.md` - local process instructions, should not affect runtime.
- `plans/` - planning artifacts, not needed for production hosting.
- `.git/` - local repository metadata, must never be uploaded to hosting.

**Patterns to follow:**
- Keep the app dependency-free and plain PHP.
- Use a small local `.env` parser because this app has no Composer setup.
- Keep `config/database.php` as the single place that creates `$pdo`.
- Keep error output generic so credentials and connection details are not leaked.

**Key decisions already made:**
- Runtime target is InfinityFree shared PHP hosting.
- InfinityFree website files are uploaded under `htdocs`, not `public_html`.
- InfinityFree does not provide normal PHP environment variable setup for this use case, so the app should read a local `.env` file directly.
- `.env` is ignored by Git. `.env.example` is committed with safe placeholder values.
- `.htaccess` should block direct HTTP access to `.env`, Git metadata, config internals, plans, and other non-public files when uploaded to `htdocs`.

## Constraints

**Must:**
- Remove real credentials from committed PHP files.
- Add `.env.example` with placeholder keys: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET`.
- Add `.gitignore` excluding `.env`, `.git/`, local OS/editor junk, logs, temp files, and dependency/vendor output if later added.
- Refactor `config/database.php` to read `.env` values and fall back only to safe non-secret defaults.
- Keep the PDO options and generic connection failure behavior.
- Add `.htaccess` deny rules for sensitive files/directories.
- Document that InfinityFree credentials must be copied from its MySQL Databases panel into `.env`.

**Must not:**
- Commit real DB passwords.
- Add Composer, `vlucas/phpdotenv`, or any dependency.
- Break existing pages that require `config/database.php`.
- Echo raw PDO exception messages to users.
- Assume `.env` is safe just because it is ignored by Git.

**Out of scope:**
- Full deployment automation.
- Domain/DNS setup.
- Moving the whole app into a separate `public/` web root.
- Production-grade secret management beyond shared-hosting-safe basics.

## Risk

**Level:** 3

**Risks identified:**
- Current credentials are hardcoded in `config/database.php` -> **Mitigation:** move secrets to ignored `.env` and replace committed config with loader logic.
- Uploading `.env` inside InfinityFree `htdocs` can expose secrets if dotfiles are served -> **Mitigation:** add `.htaccess` deny rules and keep only required public PHP/assets in hosting upload when possible.
- InfinityFree does not support setting normal PHP environment variables for this use case -> **Mitigation:** parse `.env` directly in PHP instead of relying on `getenv()` values configured by the host.
- `.gitignore` does not protect already-tracked secrets -> **Mitigation:** after Git is writable, verify Git status/history and rotate any exposed local DB password if this repo was ever pushed.

**Pushback:**
- Putting `.env` in the web root is still not ideal. This belongs outside the document root in a cleaner deployment. InfinityFree’s `htdocs` model makes that harder, so `.htaccess` denial is the pragmatic fallback, not a perfect secret-management story.

## Tasks

### T1: Environment Configuration
**Do:** Add `.env.example`, create/update local `.env` with current local values if needed, and refactor `config/database.php` to load `.env` without dependencies.
**Files:** `.env.example`, `.env`, `config/database.php`
**Verify:** `php -l config/database.php`; manual: app connects using `.env` values.

### T2: Git And Hosting Ignore Rules
**Do:** Add `.gitignore` for secrets/local files and `.htaccess` rules blocking direct access to dotfiles, Git metadata, config internals, plans, and other non-public project files.
**Files:** `.gitignore`, `.htaccess`
**Verify:** Manual: `.env` is ignored by Git; direct request to `/.env` is denied when server honors `.htaccess`.

### T3: Hosting Notes
**Do:** Add concise deployment notes for InfinityFree: upload public app files to `htdocs`, create MySQL DB in InfinityFree panel, copy host/name/user/password into `.env`, and do not upload `.git`.
**Files:** `README.md`
**Verify:** Manual: notes identify `htdocs`, `.env`, and MySQL credential setup.

## Done
- [ ] `php -l config/database.php` passes.
- [ ] App still connects to MySQL using `.env`.
- [ ] `.env.example` contains placeholders only.
- [ ] `.env` is listed in `.gitignore`.
- [ ] Real DB credentials are removed from committed PHP files.
- [ ] `.htaccess` blocks direct access to `.env` and non-public project paths.
- [ ] README includes InfinityFree `htdocs` and MySQL setup notes.
- [ ] No new dependencies.
