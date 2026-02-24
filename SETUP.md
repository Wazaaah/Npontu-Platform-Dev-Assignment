# Npontu Activity Tracker — Setup Guide

## Requirements
- PHP >= 8.2
- Composer
- Node.js >= 18 & npm
- SQLite (default) or MySQL/PostgreSQL

## Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/npontu-activity-tracker.git
cd npontu-activity-tracker
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies & build assets
```bash
npm install
npm run build
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database setup (SQLite — no extra configuration needed)
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 6. Run the application
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Demo Credentials

| Role  | Email               | Password  | Shift   |
|-------|---------------------|-----------|---------|
| Admin | admin@npontu.com    | password  | Morning |
| Staff | kwame@npontu.com    | password  | Morning |
| Staff | ama@npontu.com      | password  | Night   |

---

## Using MySQL instead of SQLite

Update your `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=npontu_tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run:
```bash
php artisan migrate
php artisan db:seed
```

---

## Features Overview

| Feature | Who |
|---|---|
| Dashboard with today's activity + handover alerts | All users |
| Update activity status (done/pending) + add remarks | All users |
| Report and manage incident reports per shift | All users |
| Shift Handover view (pending activities + escalated incidents) | All users |
| Date-range Activity & Incident Reports | All users |
| Manage activity templates (recurring checklist) | Admin only |
| Manage users and shift assignments | Admin only |

---

## Shifts
- **Morning Shift**: 6:00 AM – 6:00 PM
- **Night Shift**: 6:00 PM – 6:00 AM

Daily activities are auto-generated from templates at first login of the shift.
