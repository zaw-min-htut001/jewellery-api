# Jewellery API
A RESTful API for managing jewellery products with variants. Built on **Laravel 12** with **Sanctum authentication**, supporting full CRUD operations for products and their variants.

---
## Installation & Setup
```bash
# Clone repository
git clone <your-repo-url>
cd <your-repo-folder>

# Install PHP 8.4 (if not installed)
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"

# Install Laravel installer (optional)
composer global require laravel/installer

# Install dependencies
composer install
npm install
npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Edit .env to set database credentials
# Example:
# DB_DATABASE=jewellery
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Start server
php artisan serve

# Run tests
php artisan test

## Architecture Flow
routes/api.php
     ↓
Controllers → use ApiResponseTrait
     ↓
Services → Business Logic
     ↓
Repositories → Data Access
     ↓
Eloquent Models → Product ↔ Variant (hasMany)

---
## API Documentation (Live)

All endpoints tested and documented in **Postman**.

Open Interactive API Docs

<a href="https://documenter.getpostman.com/view/25006896/2sB3WnwhCu">https://documenter.getpostman.com/view/25006896/2sB3WnwhCu</a>