# Broken Links and Includes Report
## Generated: 2025-12-03

### Missing Include Files

1. **include/reset_password.php**
   - Referenced in: `index.php` (line 90)
   - Purpose: Password reset functionality
   - Status: MISSING

2. **include/val_current_password.php**
   - Referenced in: `change_password.php` (line 110)
   - Purpose: Validate current password via AJAX
   - Status: MISSING

3. **include/pdf_invoice.php**
   - Referenced in: `sales_window.php` (line 146)
   - Path issue: Using `../include/pdf_invoice.php` (incorrect relative path)
   - Purpose: Generate PDF invoices
   - Status: MISSING

### Files to Create

All missing files need to be created to complete the functionality.

### Next Steps

1. Create `include/reset_password.php`
2. Create `include/val_current_password.php`
3. Create `include/pdf_invoice.php`
4. Test all functionality
