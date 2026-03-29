# Laravel Inventory App

A scalable multi-business inventory management system built with Laravel 10.  
Designed with a clean architecture, stock audit trail, and production-ready structure.

---

## Features

- Multi-business (multi-tenant support)
- Product management (category, unit, supplier, customer)
- Stock management (in, out, adjustment)
- Stock ledger (audit trail)
- Reporting system (stock, movements, low stock alert)
- Role-based access (extendable)

---

## Tech Stack

- Backend: Laravel 10  
- Database: MySQL  
- Frontend: Blade + Tailwind CSS  
- Build Tool: Vite  

---

## Screenshots

### Dashboard
<img src="https://github.com/user-attachments/assets/31e40446-f198-4f22-9395-8273b1ebabb3" width="800"/>

---

### Product Management
<img src="https://github.com/user-attachments/assets/14c46e1e-66b6-4386-ba10-559ad8850552" width="800"/>

---

### Category
<img src="https://github.com/user-attachments/assets/3e0065be-5345-46dd-a9d8-16a03a2afb9b" width="800"/>

---

### Units
<img src="https://github.com/user-attachments/assets/f8ca0122-a592-42a2-8a8b-b93553f169aa" width="800"/>

---

### Supplier
<img src="https://github.com/user-attachments/assets/8439e132-e46b-4a8b-88f6-e24e2baf746f" width="800"/>

---

### Customer
<img src="https://github.com/user-attachments/assets/e7c979ab-1725-4c85-8664-9c8270d4273c" width="800"/>

---

### Stock Transactions
<img src="https://github.com/user-attachments/assets/452f86c7-5cd6-42c8-b52d-ff90689272be" width="800"/>

---

### Reports

#### Stock Overview
<img src="https://github.com/user-attachments/assets/1ef850f0-1a24-4285-b3f9-6852f8fd2406" width="800"/>

#### Stock Movements
<img src="https://github.com/user-attachments/assets/6619e673-d0a7-433c-ae83-e928239776d9" width="800"/>

#### Low Stock Alert
<img src="https://github.com/user-attachments/assets/cc821047-9750-49c2-ab9d-d55a39c822b7" width="800"/>

#### Detailed Report
<img src="https://github.com/user-attachments/assets/0d48c762-90b9-47e1-ae0c-f5e75b9a5b8c" width="800"/>

---

## Architecture

This application follows a clean and scalable architecture:

- Service Layer (business logic separation)
- Multi-tenant Business Context
- Stock Ledger System (audit-friendly)
- Transaction-based stock processing
- Modular and extensible structure

---

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
