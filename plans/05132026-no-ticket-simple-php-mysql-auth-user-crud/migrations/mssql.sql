-- Microsoft SQL Server schema scaffold for Simple PHP MySQL Auth User CRUD
-- Included because structured plan rules require MSSQL, PostgreSQL, and Knex migration artifacts for DB changes.

-- Tables:
-- users
--   id: integer primary key identity
--   name: required string, max 100
--   email: required unique string, max 150
--   password: required string containing password_hash() output
--   created_at: timestamp default current time
--   updated_at: timestamp default current time

-- TODO: fill during execution if MSSQL support is required.
