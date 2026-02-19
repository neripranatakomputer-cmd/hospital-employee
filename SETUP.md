# Hospital Employee Management System - Setup Guide

## Requirements
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM

## Installation Steps

```bash
# 1. Create Laravel project
composer create-project laravel/laravel hospital-employee
cd hospital-employee

# 2. Install dependencies
composer require maatwebsite/excel
composer require yajra/laravel-datatables-oracle
npm install bootstrap @popperjs/core
npm install --save-dev sass

# 3. Copy all files from this package to the project

# 4. Configure .env
cp .env.example .env
php artisan key:generate

# 5. Run migrations and seeders
php artisan migrate --seed

# 6. Create storage link
php artisan storage:link

# 7. Compile assets
npm run build

# 8. Run server
php artisan serve
```

## Default Login
- Super Admin: superadmin@hospital.com / password
- Admin: admin@hospital.com / password
