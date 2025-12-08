# Sales Window - MVP Description

## Overview
The Sales Window is the core POS interface where cashiers process customer transactions in real-time. It features a dual-panel layout with product browsing on the left and cart management on the right.

## Key Features

### 1. Product Selection
- **DataTable Display**: Paginated product list with server-side processing
- **Search Functionality**: Real-time product search by name
- **Quick Add**: Click product row to add to cart
- **Product Info**: Shows product name, unit price, and available quantity

### 2. Cart Management

#### Input Methods
- **Barcode Scanner**: Dedicated auto-focused input field
  - Scan barcode → Auto-add to cart
  - Enter key triggers product lookup
  - Error alerts for invalid/out-of-stock items
- **Product Search**: Autocomplete search (min 2 characters)
  - Type-ahead suggestions
  - Select product to add to cart
- **Manual Selection**: Click product from left panel

#### Cart Operations
- **View Items**: Real-time cart table with:
  - Product name
  - Unit price
  - Quantity (editable inline)
  - Line total
  - Delete button per item
- **Update Quantity**: Direct inline editing with validation
- **Remove Items**: Individual delete or clear entire cart
- **Live Totals**: Automatic calculation of subtotal and quantities

### 3. Payment Processing

#### Payment Types
- **Cash**:
  - Enter amount received
  - Auto-calculate change
  - Display change amount in real-time
- **POS (Card)**:
  - Enter payment reference number
  - No change calculation needed

#### Customer Selection
- **Existing Customer**:
  - Autocomplete search by name
  - Links to customer record
  - Updates customer purchase history
- **New Customer**:
  - Enter customer name
  - No account creation (walk-in)

#### Transaction Details
- **Discount**: Optional discount amount
- **Tax**: Automatic tax calculation (configurable rate)
- **Order Total**: Real-time total with tax and discount

### 4. Order Finalization

#### Workflow
1. Add products to cart
2. Select payment type
3. Enter payment details (cash/reference)
4. Select/enter customer info
5. Apply discount (optional)
6. Click "Place Order"
7. Confirm order in modal
8. View receipt preview
9. Print receipt or discard
10. Cart auto-clears for next sale

#### Receipt Preview Modal
- Full transaction details
- Store information (name, address, contact)
- Itemized product list
- Subtotal, tax, discount, total
- Payment method and change
- Receipt number and timestamp
- Cashier name

#### Print Options
- **Print & Close**: Sends to thermal printer (Xprinter)
- **Discard**: Close without printing

### 5. Stock Management Integration
- Real-time stock validation
- Prevents overselling
- Auto-deducts inventory on sale completion
- Displays "out of stock" errors

## Technical Features

### AJAX-Driven
- No page refreshes
- Real-time cart updates
- Instant feedback on actions
- Smooth user experience

### Session-Based Cart
- Cart stored in PHP session
- Persists during user session
- Clears after successful sale
- Handles concurrent operations

### Database Transactions
- ACID compliance
- Rollback on errors
- Ensures data integrity
- Prevents partial updates

### Auto-Focus Management
- Barcode input always focused
- Refocus after modals
- Keyboard-friendly navigation
- Fast scanning workflow

## User Experience

### Design Principles
- **Speed**: Optimized for high-volume transactions
- **Simplicity**: Minimal clicks to complete sale
- **Clarity**: Clear visual feedback and totals
- **Reliability**: Error handling and validation
- **Flexibility**: Multiple input methods

### Validation
- Required field checks
- Stock availability verification
- Positive quantity enforcement
- Payment amount validation
- Customer selection confirmation

### Error Handling
- Clear error messages
- Inline validation alerts
- Modal confirmations for destructive actions
- Graceful failure recovery

## Data Flow

```
Product Selection → Cart (Session) → Payment Details → Database Transaction
                                                    ↓
                                          Transaction Record
                                          Transaction Items
                                          Stock Deduction
                                          Customer Update
                                                    ↓
                                          Receipt Generation → Print
                                                    ↓
                                          Cart Clear → Reset Window
```

## Security Features
- Admin authentication required
- Session-based access control
- SQL injection prevention (PDO)
- XSS protection (htmlspecialchars)
- Transaction atomicity

## Integration Points
- **Products**: Real-time stock checking
- **Customers**: Purchase history tracking
- **Transactions**: Complete audit trail
- **Thermal Printer**: CUPS integration
- **System Settings**: Currency, tax rate, store info

## Performance
- Server-side DataTables pagination
- Lazy loading of products
- Optimized AJAX requests
- Minimal DOM manipulation
- Fast cart refresh


Note : use the existing sidebar and include it using php include statement and also add sales window in the sidebar if not available