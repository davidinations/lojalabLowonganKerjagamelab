# Database Migration and Seeding Guide

This project includes a comprehensive database migration and seeding system for the LowonganKerja application.

## Migration Commands

### Running Migrations

```bash
php scripts/migrate.php migrate
```

This command runs all pending migrations in the `migrations/` directory.

### Rolling Back Migrations

```bash
php scripts/migrate.php rollback
```

This command rolls back the last batch of migrations.

### Reset All Migrations

```bash
php scripts/migrate.php reset
```

This command rolls back ALL migrations (use with caution!).

### Fresh Migration with Seeders

```bash
php scripts/migrate.php fresh
```

This command drops all tables, re-runs all migrations, and runs all seeders.

### Running Seeders Only

```bash
php scripts/migrate.php seed
```

This command runs all database seeders.

## Database Structure

The system creates the following tables:

1. **tbl_roles** - User roles and permissions
2. **tbl_users** - Job seekers
3. **tbl_admins** - Administrative users
4. **tbl_organizations** - Companies/organizations
5. **tbl_typeLowongans** - Job categories
6. **tbl_lowongans** - Job postings
7. **tbl_applyLowongans** - Job applications

## Migration Files

Migration files are located in the `migrations/` directory and follow the naming convention:
`YYYYMMDD_descriptive_name.php`

Each migration file contains a class with:

- `up($db)` method - Creates/modifies database structure
- `down($db)` method - Reverses the changes

## Seeder Files

Seeder files are located in the `database/seeders/` directory and contain sample data for testing and development.

Current seeders:

- **RolesSeeder.php** - Creates default user roles
- **UsersSeeder.php** - Creates sample users
- **AdminsSeeder.php** - Creates administrative users
- **OrganizationsSeeder.php** - Creates sample companies
- **TypeLowongansSeeder.php** - Creates job categories

## Configuration

Make sure to configure your database connection in `app/config/database.php` and also in `scripts/migrate.php` last line:

```php
$container->set('db', function () {
    return new Medoo([
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'your_database_name',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8mb4',
    ]);
});
```

## Getting Started

1. Configure your database connection
2. Run fresh migration with seeders:
   ```bash
   php scripts/migrate.php fresh
   ```

This will set up your complete database structure with sample data.

## Default Admin Credentials

After running the seeders, you can use these default admin accounts:

- **Super Admin**

  - Username: `superadmin`
  - Password: `admin123`

- **Admin**

  - Username: `admin`
  - Password: `admin123`

<!-- - **HR Manager**

  - Username: `hrmanager`
  - Password: `hr123`

- **Recruiter**
  - Username: `recruiter`
  - Password: `recruiter123` -->

## Creating New Migrations

To create a new migration, follow this template:

```php
<?php

class YourMigrationName
{
    public function up($db)
    {
        // Your migration code here
        $db->query("CREATE TABLE...");
    }

    public function down($db)
    {
        // Reverse migration code here
        $db->query("DROP TABLE...");
    }
}
```

## Creating New Seeders

To create a new seeder, follow this template:

```php
<?php

return function ($db) {
    echo "Seeding your_table...\n";

    $data = [
        // Your seed data here
    ];

    foreach ($data as $item) {
        $db->insert('your_table', $item);
    }

    echo "Your table seeded successfully!\n";
};
```

Remember to add your new seeder to the `$seederFiles` array in both `scripts/migrate.php` and `scripts/seed.php`.
