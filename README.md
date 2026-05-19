# PHPApp

Simple PHP and MySQL auth/user CRUD app.

## InfinityFree Deployment

InfinityFree serves custom PHP sites from the hosting account's `htdocs` directory. Upload the app files directly into `htdocs`, not into an extra nested project folder.

### 1. Create the database

1. Open the InfinityFree control panel for the hosting account.
2. Go to MySQL Databases.
3. Create a database.
4. Copy the database host, database name, username, and password.
5. Open phpMyAdmin from the same control panel.
6. Select the new database and import the schema from:

```text
plans/05132026-no-ticket-simple-php-mysql-auth-user-crud/migrations/mysql.sql
```

InfinityFree free hosting does not allow remote MySQL connections from tools like MySQL Workbench. Use InfinityFree phpMyAdmin or PHP scripts uploaded to the hosting account.

### 2. Create `.env`

Create a `.env` file from `.env.example` and replace the placeholders with the database values from InfinityFree:

```text
DB_HOST=sql000.infinityfree.com
DB_NAME=if0_00000000_phpapp
DB_USER=if0_00000000
DB_PASS=replace-with-your-database-password
DB_CHARSET=utf8mb4
```

Do not commit `.env`.

### 3. Upload to `htdocs`

Upload these files and directories:

```text
index.php
login.php
logout.php
signup.php
dashboard.php
assets/
config/
includes/
users/
.htaccess
.env
```

Do not upload these files or directories:

```text
.git/
plans/
AGENTS.md
.agents/
.codex/
```

### 4. Test the site

Visit the InfinityFree site URL and test:

```text
/
/signup.php
/login.php
/dashboard.php
/users/create.php
```

If the app shows a database connection failure, recheck the `.env` values against the InfinityFree MySQL Databases panel.
