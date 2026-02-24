# Npontu Operations Tracker

A shift-based activity tracking and incident reporting system built for the Applications Support team at Npontu Technologies. Staff log daily tasks, escalate incidents across shifts, and hand over cleanly at shift boundaries — all from a single web interface.

---

## Tech Stack

| Layer       | Technology                               |
|-------------|-------------------------------------------|
| Framework   | Laravel 12 (PHP 8.3)                      |
| Database    | MySQL 8 (via WampServer)                  |
| Frontend    | Blade templates, Tailwind CSS v4, Vite    |
| JS          | Alpine.js (CDN) — notification dropdown   |
| Auth        | Laravel session auth (custom login flow)  |

---

## Features

### For Staff
- **Daily Activities** — View today's task list for your shift; update status to Done/Pending with an optional remark; full update history preserved per activity.
- **Incident Reports** — File incidents with severity (Low → Critical), steps taken, and escalation notes; edit or resolve any incident across shifts.
- **Cross-shift Visibility** — See incidents and activities from both shifts; designed for seamless handovers.
- **Shift Handover** — Dedicated view listing all pending activities and unresolved incidents from the outgoing shift, with full update history.
- **In-app Notifications** — Bell icon alerts for high and critical incidents reported by other users.
- **Profile Management** — Update display name, phone, department, and password.

### For Admins
- **Management Dashboard** — Side-by-side morning/night shift completion stats and all today's incidents at a glance.
- **Activity Templates** — Create, edit, deactivate, and restore the recurring task templates that auto-generate daily activities.
- **User Management** — Create and deactivate staff accounts; assign shifts and roles.
- **Reports** — Date-range reports with shift and category filters; export to CSV or browser-print PDF.
- **Audit Log** — Full, filterable log of all system events: logins, incident CRUD, activity updates, profile changes.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php       # Login, logout, audit logging
│   │   ├── DashboardController.php        # Role-branched dashboard + activity generation
│   │   ├── DailyActivityController.php    # Activity list + status updates
│   │   ├── IncidentReportController.php   # Incident CRUD + notifications
│   │   ├── HandoverController.php         # Shift handover summary
│   │   ├── ReportController.php           # Reports with CSV/PDF export
│   │   ├── ActivityTemplateController.php # Admin: template management
│   │   ├── UserController.php             # Admin: user management
│   │   ├── ProfileController.php          # Self-service profile editing
│   │   ├── AuditLogController.php         # Admin: audit log viewer
│   │   └── NotificationController.php     # Mark notifications read
│   └── Middleware/
│       └── AdminMiddleware.php            # Restricts admin-only routes
├── Models/
│   ├── User.php
│   ├── ActivityTemplate.php
│   ├── DailyActivity.php
│   ├── ActivityUpdate.php
│   ├── IncidentReport.php
│   └── AuditLog.php
├── Notifications/
│   └── IncidentReported.php              # Database notification for high/critical incidents
└── Policies/
    └── IncidentReportPolicy.php          # Any staff member can edit any incident

database/
└── migrations/
    ├── ..._create_users_table.php
    ├── ..._create_activity_templates_table.php
    ├── ..._create_daily_activities_table.php
    ├── ..._create_activity_updates_table.php
    ├── ..._create_incident_reports_table.php
    ├── ..._add_performance_indexes.php
    ├── ..._create_audit_logs_table.php
    └── ..._create_notifications_table.php

resources/views/
├── layouts/app.blade.php                 # Sidebar layout with nav, clock, bell icon
├── auth/login.blade.php
├── dashboard.blade.php                   # Staff dashboard
├── dashboard-admin.blade.php             # Admin dashboard
├── daily-activities/
├── incidents/
├── handover/
├── reports/
├── profile/
├── audit-log/
├── users/
└── activity-templates/
```

---

## Database Schema

```
users               — id, name, email, password, role, shift, phone, department, is_active
activity_templates  — id, name, description, category, applicable_shift, is_active, created_by
daily_activities    — id, activity_template_id, activity_date, shift, status
activity_updates    — id, daily_activity_id, updated_by, status, remark, updated_at_time
incident_reports    — id, reported_by, incident_date, shift, title, description,
                       steps_taken, resolution_status, escalation_note, severity
audit_logs          — id, user_id, action, entity_type, entity_id, description, ip_address
notifications       — id (uuid), type, notifiable_type, notifiable_id, data, read_at
```

---

## Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+ with npm
- MySQL 8+ (WampServer on Windows or equivalent)

### Installation

```bash
# 1. Clone the repository
git clone <repository-url>
cd Npontu-Platform-Dev-Assignment

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Set database credentials in .env
#    DB_DATABASE=npontu_tracker
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Run migrations and seed demo data
php artisan migrate --seed

# 7. Serve locally
php artisan serve
```

### Demo Accounts

| Role  | Email              | Password | Shift   |
|-------|--------------------|----------|---------|
| Admin | admin@npontu.com   | password | —       |
| Staff | kwame@npontu.com   | password | Morning |
| Staff | ama@npontu.com     | password | Night   |

---

## Key Design Decisions

### Idempotent Activity Generation
Daily activities are generated lazily on first dashboard load each day using `firstOrCreate`, keyed on `(template_id, date, shift)`. A unique index on this triplet prevents duplicates even under concurrent requests.

### Cross-shift Incident Visibility
Incident reports are visible to all authenticated users regardless of shift, so night-shift incidents are immediately actionable by morning-shift staff. Any staff member can update or resolve any incident.

### Append-only Update History
Activity status changes are never overwritten. Every update creates a new `ActivityUpdate` row, giving the handover view a full, timestamped audit trail of who touched each task and when.

### In-app Notifications (database channel)
High and critical incidents dispatch an `IncidentReported` notification to all other active users via Laravel's database notification channel. No external mail or push service required.

### Audit Trail
Every significant action (login, logout, incident CRUD, activity updates, profile changes) is recorded in the `audit_logs` table with user ID, IP address, entity reference, and a human-readable description.

---

## Running Tests

```bash
php artisan test
```

---

## License

Built as a developer assessment submission for Npontu Technologies.
