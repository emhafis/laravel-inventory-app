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

![Dashboard](public/screenshots/dashboard.png)

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
