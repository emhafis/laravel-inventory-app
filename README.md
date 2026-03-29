# Laravel Inventory App

A scalable multi-business inventory management system built with Laravel 10.

## Features

- Multi-business (multi-tenant)
- Product management
- Stock in / out / adjustment
- Stock ledger (audit trail)
- Reporting

## Tech Stack

- Laravel 10
- MySQL
- Tailwind CSS

## Screenshots
<img width="1920" height="1080" alt="Screenshot (26)" src="https://github.com/user-attachments/assets/480de462-7ff1-4ea0-9e02-238f1d40dcdb" />


## Architecture

This application uses:
- Service Layer
- Business Context (multi-tenant)
- Stock Ledger System

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
