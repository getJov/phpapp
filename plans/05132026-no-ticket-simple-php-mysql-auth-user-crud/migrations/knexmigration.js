// Knex migration scaffold for Simple PHP MySQL Auth User CRUD.
// Included because structured plan rules require MSSQL, PostgreSQL, and Knex migration artifacts for DB changes.

/**
 * Tables:
 * users
 *   id: integer primary key
 *   name: required string, max 100
 *   email: required unique string, max 150
 *   password: required string containing password_hash() output
 *   created_at: timestamp default current time
 *   updated_at: timestamp default current time
 */

exports.up = async function up(knex) {
  // TODO: fill during execution if Knex support is required.
};

exports.down = async function down(knex) {
  // TODO: fill during execution if Knex support is required.
};
