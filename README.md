# WAREHOUSE PRO - Inventory Management System
Modular Inventory System built with PHP 8, MySQLi, and Vanilla JS.

## Project Overview
Warehouse Pro provides a comprehensive solution for managing warehouse operations, including tracking inventory, processing sales, handling procurement from vendors, and generating business analytics.

## Key Modules
- **Dashboard**: Real-time stats, profit tracking, and live activity feed.
- **Inventory**: Product management with barcode generation and image support.
- **Sales (POS)**: Customer management, AJAX-based search, and invoice generation.
- **Procurement**: Vendor tracking and purchase order history.
- **Reports**: PDF and CSV exports for stock, sales, and procurement.
- **Admin**: Role-based access control (RBAC) and department management.

## Tech Stack
- **Frontend**: HTML5, Vanilla CSS (Glassmorphism), JavaScript (ES6+).
- **Backend**: PHP 8 (Modular MVC-lite structure).
- **Database**: MySQL (optimized for relational integrity).
- **Libraries**: Chart.js, FullCalendar, JsBarcode, Flatpickr.

## Setup Instructions
1. Import the `database.sql` file into your MySQL environment (e.g., PHPMyAdmin).
2. Configure database credentials in `includes/db.php`.
3. Run `tools/setup/setup_users.php` to generate test credentials.
4. Default login for testing: `admin` / `admin@123`.

## Version History
- **v1.0**: Initial Release (Core modules).
- **v1.1**: Added RBAC and Reporting.
- **v1.2**: Elite features (Sound alerts, Global search, Barcodes, and Profit tracking).

---
Developed for Internship Portfolio | 2026
