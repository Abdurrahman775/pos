# Design Document

## Overview

The BasicPOS System is a web-based point-of-sale application built using PHP, MySQL, and Bootstrap. The system follows a traditional server-side MVC-like architecture where PHP handles business logic and data access, MySQL stores all persistent data, and Bootstrap provides the responsive UI framework.

The existing codebase provides a foundation with:
- Database connection management via PDO
- User authentication system
- Basic admin interface with sidebar navigation
- Template structure for consistent UI
- Product management foundation

This design will build upon the existing structure while implementing all requirements for a complete POS system including sales transactions, inventory management, customer management, reporting, and system administration.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────┐
│         Presentation Layer (Browser)            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │  Login   │  │Dashboard │  │  Sales   │     │
│  │  Page    │  │   Page   │  │  Window  │     │
│  └──────────┘  └──────────┘  └──────────┘     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │ Products │  │ Reports  │  │ Settings │     │
│  │   Page   │  │   Page   │  │   Page   │     │
│  └──────────┘  └──────────┘  └──────────┘     │
└─────────────────────────────────────────────────┘
                      │
                      ↓ HTTP/HTTPS
┌─────────────────────────────────────────────────┐
│        Application Layer (PHP Backend)          │
│  ┌──────────────────────────────────────────┐  │
│  │      Session Management & Auth           │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │  Sales   │ │Inventory │ │ Customer │      │
│  │ Module   │ │  Module  │ │  Module  │      │
│  └──────────┘ └──────────┘ └──────────┘      │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │ Reports  │ │  Admin   │ │  Common  │      │
│  │  Module  │ │  Module  │ │Functions │      │
│  └──────────┘ └──────────┘ └──────────┘      │
└─────────────────────────────────────────────────┘
                      │
                      ↓ PDO
┌─────────────────────────────────────────────────┐
│            Data Layer (MySQL)                   │
│  ┌──────────────┐  ┌──────────────┐           │
│  │   Products   │  │    Sales     │           │
│  │    Table     │  │    Table     │           │
│  └──────────────┘  └──────────────┘           │
│  ┌──────────────┐  ┌──────────────┐           │
│  │   Customers  │  │    Admins    │           │
│  │    Table     │  │    Table     │           │
│  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────┘
```

### Technology Stack

**Frontend:**
- HTML5 for structure
- Bootstrap 5 for responsive UI components
- jQuery for DOM manipulation and AJAX
- DataTables for advanced table features
- Bootbox for modal dialogs
- jQuery Validation for form validation

**Backend:**
- PHP 7.4+ for server-side logic
- PDO for database abstraction
- Session-based authentication
- Bcrypt for password hashing

**Database:**
- MySQL 8.0+ for data persistence
- InnoDB engine for ACID compliance
- Foreign key constraints for referential integrity

**Additional Libraries:**
- FPDF for PDF generation (receipts and reports)
- PHPMailer for email notifications
- jQuery UI for enhanced interactions

## Components and Interfaces

### 1. Authentication Module

**Location:** `include/login.php`, `include/admin_authentication.php`

**Responsibilities:**
- User login validation
- Session management
- Role-based access control
- Password reset functionality
- Activity logging

**Key Functions:**
- `login_admin($dbh, $username, $password)` - Authenticates user credentials
- `check_session()` - Validates active session
- `logout_user()` - Terminates user session
- `log_activity($dbh, $username, $action)` - Records user actions

### 2. Sales Transaction Module

**Location:** `sales_window.php`, `include/sales_functions.php`

**Responsibilities:**
- Product search and selection
- Shopping cart management
- Discount application
- Payment processing
- Receipt generation
- Transaction hold/retrieve
- Void and refund operations

**Key Functions:**
- `add_to_cart($product_id, $quantity)` - Adds product to cart
- `calculate_totals($cart_items, $discount, $tax_rate)` - Computes transaction totals
- `process_payment($transaction_data)` - Completes sale and updates inventory
- `generate_receipt($transaction_id)` - Creates printable receipt
- `hold_transaction($cart_data)` - Saves incomplete transaction
- `retrieve_transaction($hold_id)` - Restores held transaction
- `void_transaction($transaction_id, $auth_user)` - Cancels transaction
- `process_refund($transaction_id, $items, $auth_user)` - Handles returns

### 3. Product & Inventory Module

**Location:** `all_products.php`, `add_product.php`, `edit_product.php`, `include/product_functions.php`

**Responsibilities:**
- Product CRUD operations
- Category management
- Stock level tracking
- Low stock alerts
- Bulk import via CSV
- Stock movement history
- Price history tracking

**Key Functions:**
- `add_product($dbh, $product_data)` - Creates new product
- `update_product($dbh, $product_id, $product_data)` - Modifies product
- `delete_product($dbh, $product_id)` - Soft deletes product
- `update_stock($dbh, $product_id, $quantity, $operation)` - Adjusts inventory
- `get_low_stock_products($dbh)` - Returns products below minimum
- `import_products_csv($dbh, $file_path)` - Bulk product import
- `log_stock_movement($dbh, $product_id, $quantity, $type, $reference)` - Records changes

### 4. Customer Management Module

**Location:** `customers.php`, `include/customer_functions.php`

**Responsibilities:**
- Customer CRUD operations
- Purchase history tracking
- Customer search and lookup
- Spending analytics

**Key Functions:**
- `add_customer($dbh, $customer_data)` - Creates customer record
- `update_customer($dbh, $customer_id, $customer_data)` - Updates customer
- `search_customers($dbh, $search_term)` - Finds customers by name/phone
- `get_customer_history($dbh, $customer_id)` - Retrieves purchase history
- `update_customer_spending($dbh, $customer_id, $amount)` - Updates total spending

### 5. Employee Management Module

**Location:** `employees.php`, `include/employee_functions.php`

**Responsibilities:**
- Employee CRUD operations
- Role assignment
- Performance tracking
- Clock in/out logging

**Key Functions:**
- `add_employee($dbh, $employee_data)` - Creates employee record
- `update_employee($dbh, $employee_id, $employee_data)` - Updates employee
- `get_employee_sales($dbh, $employee_id, $date_range)` - Retrieves sales metrics
- `clock_in_out($dbh, $employee_id, $action)` - Records time

### 6. Reporting Module

**Location:** `reports/`, `include/report_functions.php`

**Responsibilities:**
- Sales report generation
- Inventory reports
- Financial reports
- Export to PDF/Excel/CSV
- Dashboard metrics

**Key Functions:**
- `generate_sales_report($dbh, $report_type, $params)` - Creates sales reports
- `generate_inventory_report($dbh, $report_type)` - Creates inventory reports
- `generate_financial_report($dbh, $report_type, $date_range)` - Creates financial reports
- `export_to_pdf($report_data, $report_title)` - Exports report as PDF
- `export_to_excel($report_data, $report_title)` - Exports report as Excel
- `export_to_csv($report_data, $filename)` - Exports report as CSV
- `get_dashboard_metrics($dbh)` - Retrieves key performance indicators

### 7. System Administration Module

**Location:** `settings.php`, `include/admin_functions.php`

**Responsibilities:**
- System configuration
- User management
- Tax rate configuration
- Store information management
- Receipt template customization
- Database backup
- System logs

**Key Functions:**
- `update_settings($dbh, $settings_data)` - Updates system configuration
- `configure_tax_rate($dbh, $tax_rate)` - Sets tax percentage
- `update_store_info($dbh, $store_data)` - Updates store details
- `backup_database()` - Creates database backup
- `get_system_logs($dbh, $filters)` - Retrieves system logs

## Data Models

### Database Schema

#### Users/Admins Table
```sql
CREATE TABLE admins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(32) UNIQUE NOT NULL,
  password TEXT NOT NULL,
  sname VARCHAR(32) NOT NULL,
  fname VARCHAR(32) NOT NULL,
  mname VARCHAR(32),
  mobile VARCHAR(16),
  email VARCHAR(128) NOT NULL,
  role_id INT NOT NULL,
  acct_attempt INT DEFAULT 0,
  acct_lock TINYINT DEFAULT 0,
  acct_block TINYINT DEFAULT 0,
  acct_activation TINYINT DEFAULT 0,
  updated_by VARCHAR(32),
  last_update TIMESTAMP,
  reg_by VARCHAR(32) NOT NULL,
  reg_date DATETIME NOT NULL,
  is_active TINYINT DEFAULT 1,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

#### Roles Table
```sql
CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  role_name VARCHAR(50) NOT NULL,
  description TEXT,
  permissions JSON,
  is_active TINYINT DEFAULT 1
);
```

#### Products Table
```sql
CREATE TABLE products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  sku VARCHAR(50) UNIQUE NOT NULL,
  barcode VARCHAR(100) UNIQUE,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category_id INT,
  purchase_price DECIMAL(10,2) NOT NULL,
  selling_price DECIMAL(10,2) NOT NULL,
  qty_in_stock INT DEFAULT 0,
  min_stock_level INT DEFAULT 0,
  supplier_id INT,
  image_url VARCHAR(255),
  updated_by VARCHAR(32),
  last_update TIMESTAMP,
  reg_by VARCHAR(32) NOT NULL,
  reg_date DATETIME NOT NULL,
  is_active TINYINT DEFAULT 1,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);
```

#### Categories Table
```sql
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  parent_id INT,
  description TEXT,
  is_active TINYINT DEFAULT 1,
  FOREIGN KEY (parent_id) REFERENCES categories(id)
);
```

#### Sales Table
```sql
CREATE TABLE sales (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id VARCHAR(16) UNIQUE NOT NULL,
  transaction_date DATETIME NOT NULL,
  user_id INT NOT NULL,
  customer_id INT,
  subtotal DECIMAL(10,2) NOT NULL,
  tax_amount DECIMAL(10,2) DEFAULT 0,
  discount_amount DECIMAL(10,2) DEFAULT 0,
  total_amount DECIMAL(10,2) NOT NULL,
  payment_method_id INT NOT NULL,
  amount_paid DECIMAL(10,2) NOT NULL,
  change_amount DECIMAL(10,2) DEFAULT 0,
  status VARCHAR(20) DEFAULT 'completed',
  notes TEXT,
  reg_date DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES admins(id),
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
);
```

#### Sales Items Table
```sql
CREATE TABLE sales_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  sale_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  discount DECIMAL(10,2) DEFAULT 0,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### Customers Table
```sql
CREATE TABLE customers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(16),
  email VARCHAR(128),
  address TEXT,
  total_purchases DECIMAL(10,2) DEFAULT 0,
  reg_by VARCHAR(32) NOT NULL,
  reg_date DATETIME NOT NULL,
  is_active TINYINT DEFAULT 1
);
```

#### Payment Methods Table
```sql
CREATE TABLE payment_methods (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  is_active TINYINT DEFAULT 1
);
```

#### Held Transactions Table
```sql
CREATE TABLE held_transactions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  cart_data JSON NOT NULL,
  customer_id INT,
  notes TEXT,
  hold_date DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES admins(id)
);
```

#### Stock Movement Table
```sql
CREATE TABLE stock_movements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL,
  movement_type VARCHAR(20) NOT NULL,
  quantity INT NOT NULL,
  reference_id INT,
  reference_type VARCHAR(50),
  notes TEXT,
  user_id INT NOT NULL,
  movement_date DATETIME NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES admins(id)
);
```

#### Audit Log Table
```sql
CREATE TABLE auditlog (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(32),
  action VARCHAR(255) NOT NULL,
  table_name VARCHAR(50),
  record_id INT,
  old_values JSON,
  new_values JSON,
  ip_address VARCHAR(45),
  user_agent TEXT,
  action_date DATETIME NOT NULL
);
```

#### System Settings Table
```sql
CREATE TABLE system_settings (
  setting_key VARCHAR(50) PRIMARY KEY,
  setting_value TEXT,
  setting_type VARCHAR(20),
  description TEXT,
  updated_by VARCHAR(32),
  last_update TIMESTAMP
);
```

#### Suppliers Table
```sql
CREATE TABLE suppliers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  supplier_name VARCHAR(150) NOT NULL,
  contact_person VARCHAR(100),
  phone VARCHAR(16),
  email VARCHAR(128),
  address TEXT,
  is_active TINYINT DEFAULT 1
);
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Authentication creates valid session
*For any* valid username and password combination, successful authentication should create a session with user role and permissions
**Validates: Requirements 1.1**

### Property 2: Invalid credentials are rejected
*For any* invalid username or password, authentication should fail and no session should be created
**Validates: Requirements 1.2**

### Property 3: Session timeout enforcement
*For any* session inactive for 30 minutes, the system should automatically terminate the session
**Validates: Requirements 1.3**

### Property 4: Role-based access control
*For any* user action, the system should only allow the action if the user's role has the required permission
**Validates: Requirements 1.4, 1.5, 1.6**

### Property 5: Activity logging completeness
*For any* user action, the system should create an audit log entry with user, timestamp, and action details
**Validates: Requirements 1.7**

### Property 6: Product retrieval by barcode
*For any* valid barcode, scanning should retrieve the correct product with all details
**Validates: Requirements 2.1**

### Property 7: Transaction total calculation accuracy
*For any* set of cart items with discounts and tax, the calculated total should equal (subtotal - discounts) * (1 + tax_rate)
**Validates: Requirements 2.3**

### Property 8: Inventory update on sale
*For any* completed transaction, each product's stock quantity should decrease by the quantity sold
**Validates: Requirements 2.8**

### Property 9: Hold and retrieve transaction consistency
*For any* held transaction, retrieving it should restore the exact cart state including all items and calculations
**Validates: Requirements 2.9, 2.10**

### Property 10: Void restores inventory
*For any* voided transaction, all product stock levels should be restored to pre-transaction values
**Validates: Requirements 3.2**

### Property 11: Low stock alert generation
*For any* product where stock level falls below minimum threshold, a low stock alert should be generated
**Validates: Requirements 4.5**

### Property 12: Stock movement history completeness
*For any* inventory change, a stock movement record should be created with timestamp, user, and reason
**Validates: Requirements 4.7**

### Property 13: Customer purchase history accuracy
*For any* transaction associated with a customer, the transaction should appear in that customer's purchase history
**Validates: Requirements 6.2**

### Property 14: Customer spending update
*For any* completed transaction for a customer, the customer's total spending should increase by the transaction total
**Validates: Requirements 6.3**

### Property 15: Sales report data accuracy
*For any* date range, the sales report total should equal the sum of all transaction totals in that period
**Validates: Requirements 8.2**

### Property 16: Report export completeness
*For any* report exported to PDF/Excel/CSV, the exported data should match the data visible in the report view
**Validates: Requirements 11.2**

### Property 17: Tax calculation consistency
*For any* transaction, the tax amount should equal subtotal * configured_tax_rate
**Validates: Requirements 13.1**

### Property 18: Daily backup execution
*For any* 24-hour period, exactly one database backup should be created
**Validates: Requirements 14.1**

## Error Handling

### Error Categories

**1. Authentication Errors**
- Invalid credentials: Display user-friendly message, log attempt
- Account locked: Display lockout message, require admin unlock
- Session expired: Redirect to login with timeout message

**2. Validation Errors**
- Invalid input: Display field-specific error messages
- Duplicate entries: Prevent submission, show conflict message
- Missing required fields: Highlight fields, prevent submission

**3. Business Logic Errors**
- Insufficient stock: Prevent sale, display available quantity
- Unauthorized action: Display permission denied message, log attempt
- Invalid discount: Reject discount, show valid range

**4. Database Errors**
- Connection failure: Display maintenance message, log error
- Query failure: Rollback transaction, log error, display generic message
- Constraint violation: Display user-friendly message, log details

**5. System Errors**
- File operation failure: Log error, display generic message
- Email sending failure: Log error, continue operation
- Backup failure: Log error, notify administrator

### Error Handling Strategy

**User-Facing Errors:**
- Use Bootbox modals for error messages
- Provide clear, actionable error messages
- Avoid exposing technical details
- Offer suggestions for resolution

**System Errors:**
- Log all errors with full context
- Include timestamp, user, action, and stack trace
- Send critical error notifications to administrators
- Implement graceful degradation where possible

**Transaction Errors:**
- Use database transactions for multi-step operations
- Implement rollback on any failure
- Maintain data consistency
- Log all transaction failures

## Testing Strategy

### Unit Testing

**Authentication Module:**
- Test valid login with correct credentials
- Test invalid login with wrong password
- Test account lockout after failed attempts
- Test session creation and validation
- Test role-based permission checks

**Sales Module:**
- Test cart item addition and removal
- Test discount calculation (percentage and fixed)
- Test tax calculation
- Test total calculation with various scenarios
- Test payment processing with different methods
- Test inventory update after sale

**Inventory Module:**
- Test product creation with all fields
- Test product update and validation
- Test stock level updates
- Test low stock alert generation
- Test CSV import with valid and invalid data

**Customer Module:**
- Test customer creation and search
- Test purchase history tracking
- Test spending total updates

**Reporting Module:**
- Test sales report generation for various date ranges
- Test inventory report accuracy
- Test financial report calculations
- Test export functionality for each format

### Property-Based Testing

We will use PHPUnit with property-based testing extensions to verify correctness properties.

**Configuration:**
- Minimum 100 iterations per property test
- Use random data generators for products, transactions, and users
- Test edge cases: empty carts, zero prices, maximum quantities

**Property Test Examples:**

**Property Test 1: Transaction total calculation**
```php
/**
 * Feature: pos-system, Property 7: Transaction total calculation accuracy
 * For any set of cart items with discounts and tax, 
 * the calculated total should equal (subtotal - discounts) * (1 + tax_rate)
 */
public function testTransactionTotalCalculation() {
    // Generate random cart items
    // Apply random discounts and tax rate
    // Verify calculated total matches formula
}
```

**Property Test 2: Inventory update consistency**
```php
/**
 * Feature: pos-system, Property 8: Inventory update on sale
 * For any completed transaction, each product's stock quantity 
 * should decrease by the quantity sold
 */
public function testInventoryUpdateOnSale() {
    // Generate random products with stock levels
    // Create random transaction
    // Verify stock decreased by exact quantity sold
}
```

**Property Test 3: Hold and retrieve consistency**
```php
/**
 * Feature: pos-system, Property 9: Hold and retrieve transaction consistency
 * For any held transaction, retrieving it should restore 
 * the exact cart state including all items and calculations
 */
public function testHoldRetrieveConsistency() {
    // Generate random cart state
    // Hold transaction
    // Retrieve transaction
    // Verify cart state is identical
}
```

### Integration Testing

**End-to-End Workflows:**
- Complete sales transaction from product scan to receipt
- Product management: add, edit, delete, restore
- Customer registration and transaction association
- Report generation and export
- User management and permission enforcement

**Database Integration:**
- Test all CRUD operations
- Verify foreign key constraints
- Test transaction rollback scenarios
- Verify audit logging

**External Integration:**
- Receipt printer communication
- Barcode scanner input
- Email notification delivery
- PDF generation

### User Acceptance Testing

**Cashier Workflow:**
- Process 10 different transaction types
- Handle returns and voids
- Use hold/retrieve functionality
- Verify receipt accuracy

**Manager Workflow:**
- Generate all report types
- Export reports in all formats
- Manage inventory and products
- Review employee performance

**Administrator Workflow:**
- Configure system settings
- Manage users and permissions
- Review audit logs
- Perform database backup

## Security Considerations

**Authentication Security:**
- Passwords hashed using bcrypt
- Account lockout after 5 failed attempts
- Session tokens with secure random generation
- Session timeout after 30 minutes inactivity

**Authorization Security:**
- Role-based access control on all pages
- Permission checks before sensitive operations
- Audit logging of all user actions

**Data Security:**
- PDO prepared statements to prevent SQL injection
- Input validation and sanitization
- XSS protection through output escaping
- CSRF tokens for state-changing operations

**Database Security:**
- Principle of least privilege for database user
- Encrypted database backups
- Regular security updates

## Performance Considerations

**Database Optimization:**
- Indexes on frequently queried columns (barcode, SKU, order_id)
- Efficient JOIN queries for reports
- Query result caching for dashboard metrics
- Database connection pooling

**Frontend Optimization:**
- Minified CSS and JavaScript
- Lazy loading for large product lists
- AJAX for dynamic updates without page reload
- DataTables pagination for large datasets

**Caching Strategy:**
- Session-based caching for user data
- Application-level caching for system settings
- Browser caching for static assets

## Deployment Considerations

**Environment Setup:**
- PHP 7.4+ with required extensions (PDO, mbstring, gd)
- MySQL 8.0+ with InnoDB engine
- Apache/Nginx web server with mod_rewrite
- SSL certificate for HTTPS

**Configuration:**
- Environment-specific config files
- Database credentials in secure location
- Error reporting disabled in production
- Logging configured for production

**Backup Strategy:**
- Automated daily database backups
- Backup retention for 30 days
- Off-site backup storage
- Regular backup restoration testing

**Monitoring:**
- Error log monitoring
- Performance metrics tracking
- Disk space monitoring
- Database connection monitoring
