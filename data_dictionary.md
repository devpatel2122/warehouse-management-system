# DATA DICTIONARY: WAREHOUSE PRO SYSTEM

This document details the database schema and data structures used in the Warehouse Pro Inventory Management System.

---

## 1. User Management

### Table: `users`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Unique identifier for each user |
| `username` | VARCHAR (50) | Unique | Login handles for staff and admins |
| `password` | VARCHAR (255) | - | Hashed password string (bcrypt) |
| `email` | VARCHAR (100) | - | Contact email for the user |
| `role` | ENUM | - | one of: 'admin', 'product_dept', 'purchase_dept', 'sell_dept', 'inventory_dept' |
| `xp` | INT (11) | - | Gamification points earned by user activity |
| `created_at` | TIMESTAMP | - | Registration timestamp |

---

## 2. Inventory & Products

### Table: `products`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Unique product identifier |
| `name` | VARCHAR (255) | - | Full name of the product |
| `description` | TEXT | - | Detailed product specifications |
| `category_id` | INT (11) | FK | Link to categories table |
| `barcode` | VARCHAR (100) | Unique | Scannable barcode string |
| `price` | DECIMAL (10,2) | - | Selling price per unit |
| `stock_quantity` | DECIMAL (15,3) | - | Current available stock in warehouse |
| `reorder_level` | INT (11) | - | Threshold for low-stock alerts (default: 5) |
| `rack_location` | VARCHAR (50) | - | Physical location (e.g., A1, B4) |

### Table: `categories`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Category identifier |
| `name` | VARCHAR (100) | - | Category title (e.g., Oil, Ghee, Sugar) |

---

## 3. Sales & Revenue

### Table: `sales`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Sale transaction ID |
| `customer_id` | INT (11) | FK | Link to customers table (nullable) |
| `total_amount` | DECIMAL (15,2) | - | Final bill amount including taxes |
| `sale_date` | DATE | - | Transaction date |
| `payment_status`| ENUM | - | 'Unpaid', 'Paid', 'Partial' |
| `payment_method`| ENUM | - | 'Cash', 'UPI', 'Card', 'Credit' |
| `invoice_no` | VARCHAR (50) | - | Human-readable receipt number (e.g., INV-00001) |

### Table: `sale_items`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Line item ID |
| `sale_id` | INT (11) | FK | Header sale link |
| `product_id` | INT (11) | FK | Sold product link |
| `quantity` | DECIMAL (15,3) | - | Number of units sold |
| `unit_price` | DECIMAL (10,2) | - | Price at time of sale |

---

## 4. Procurement & Vendors

### Table: `purchases`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Purchase order ID |
| `vendor_id` | INT (11) | FK | Supplier link |
| `total_amount` | DECIMAL (15,2) | - | Total cost of inbound items |

### Table: `vendors`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Vendor identifier |
| `name` | VARCHAR (255) | - | Supplier company name |
| `gstin` | VARCHAR (15) | - | Tax registration number |

---

## 5. System Logs & Tasks

### Table: `audit_logs`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Log entry ID |
| `user_id` | INT (11) | FK | User who performed the action |
| `action` | VARCHAR (255) | - | Description of the action (e.g., 'New Product Added') |
| `details` | TEXT | - | Additional context or raw data changed |

### Table: `settings`
| Field | Data Type | Key | Description |
|---|---|---|---|
| `id` | INT (11) | PK | Setting ID |
| `setting_key` | VARCHAR (50) | Unique | Configuration key (e.g., 'company_name') |
| `setting_value`| TEXT | - | Stored value for the key |
