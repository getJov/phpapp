// Knex migration for Simple PHP MySQL Auth User CRUD.

exports.up = async function up(knex) {
  const exists = await knex.schema.hasTable('users');

  if (!exists) {
    await knex.schema.createTable('users', (table) => {
      table.increments('id').primary();
      table.string('name', 100).notNullable();
      table.string('email', 150).notNullable().unique();
      table.string('password', 255).notNullable();
      table.timestamp('created_at').notNullable().defaultTo(knex.fn.now());
      table.timestamp('updated_at').notNullable().defaultTo(knex.fn.now());
    });
  }
};

exports.down = async function down(knex) {
  await knex.schema.dropTableIfExists('users');
};
