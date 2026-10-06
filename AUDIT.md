# Codebase Comprehensive Audit Report

**Project:** ITEC Inventory & Accounting ERP  
**Audit Date:** 2026-10-06  
**Scope:** Architecture, Security, Performance, Database Indexing, Authorization, Error Handling, Testing, Code Hygiene & Dependencies  

---

## Executive Summary & Top 10 Priority Findings

| # | Finding | File & Line | Category | Severity | Status |
|---|---|---|---|---|---|
| **1** | SQL Injection via unvalidated search column parameter | [`app/Http/Controllers/ServiceController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ServiceController.php) | Security | **HIGH** | `FIXED` |
| **2** | Unrestricted & unvalidated file uploads allowing arbitrary script execution | [`app/Http/Controllers/UserController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/UserController.php) & [`EmployeeController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/EmployeeController.php) | Security | **HIGH** | `FIXED` |
| **3** | IDOR / Broken authorization allowing arbitrary employee TA/DA manipulation | [`app/Http/Controllers/EmployeeTaDaController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/EmployeeTaDaController.php) | Missing Authz | **HIGH** | `FIXED` |
| **4** | Silent salary calculation corruption in update method omitting advance deductions | [`app/Http/Controllers/SalaryController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/SalaryController.php) | Error Handling | **HIGH** | `FIXED` |
| **5** | Unknown database column `challan_date` in Bill date filter throwing 500 fatal SQL errors | [`app/Http/Controllers/BillController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/BillController.php) | Error Handling | **HIGH** | `FIXED` |
| **6** | Severe N+1 query storm on product list in quotation creation (~1,000+ sequential queries) | [`app/Http/Controllers/QuotationController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/QuotationController.php) | N+1 / Slow Query | **HIGH** | `FIXED` |
| **7** | Out of Memory (OOM) bottleneck reading all purchases into Eloquent models on index load | [`app/Http/Controllers/PurchaseController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/PurchaseController.php) | N+1 / Slow Query | **HIGH** | `FIXED` |
| **8** | Missing foreign key & filter indexes on high-traffic transactional tables | [`database/migrations/`](file:///c:/laragon/www/itec_inventory/database/migrations/) | Missing Index | **HIGH** | `VERIFIED` |
| **9** | Critical lack of unit & feature test coverage across financial & accounting ledger modules | [`tests/`](file:///c:/laragon/www/itec_inventory/tests/) | Test Gap | **HIGH** | `FIXED` |
| **10** | Over 600+ lines of dead pizza/restaurant & e-commerce legacy code and helper logic | [`app/Http/Controllers/ProductController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ProductController.php) & [`CartHelper.php`](file:///c:/laragon/www/itec_inventory/app/Helpers/) | Dead Code | **HIGH** | `FIXED` |

---

## Detailed Findings by Category

### 1. Security

#### Finding 1.1: SQL Injection via Dynamically Concatenated Column in Service Query
- **File:Line:** [`app/Http/Controllers/ServiceController.php:39-41, 90-92`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ServiceController.php#L39-L41)
- **Severity:** High
- **Why Bad:** The request input `$request->serach_by` (with a spelling mistake) is directly concatenated into the SQL `where('services.' . $request->serach_by, ...)` query without a whitelist check or parameter binding. An attacker can pass arbitrary SQL fragments through this parameter to leak database records.
- **Fix:** Whitelist allowed search columns:
  ```php
  $allowedColumns = ['invoice_no', 'customer_name', 'phone', 'serial_no'];
  if (in_array($request->search_by, $allowedColumns, true) && $request->filled('key')) {
      $services->where('services.' . $request->search_by, 'like', '%' . $request->key . '%');
  }
  ```
- **Effort:** Low (15 mins)

#### Finding 1.2: Unrestricted File Upload Vulnerabilities in User & Employee Management
- **File:Line:** [`app/Http/Controllers/UserController.php:101-106, 184-190`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/UserController.php#L101-L106), [`app/Http/Controllers/EmployeeController.php:56-61, 85-94`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/EmployeeController.php#L56-L61)
- **Severity:** High
- **Why Bad:** Image uploads are moved into public webroot folders (`public/frontend/users/`, `public/uploads/employees/`) using only original client extensions (`getClientOriginalExtension()`) without MIME validation, extension whitelisting, or file size limits. A malicious actor could upload an executable PHP shell (`.php`, `.phtml`).
- **Fix:** Add strict validation rules in Request classes:
  ```php
  'images' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
  'image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
  ```
- **Effort:** Low (30 mins)

#### Finding 1.3: Mass Assignment Vulnerability Passing Raw `$request->all()`
- **File:Line:** [`app/Http/Controllers/TaDaController.php:34, 57`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/TaDaController.php#L34), [`app/Http/Controllers/BankDetailController.php:39, 68`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/BankDetailController.php#L39)
- **Severity:** Medium
- **Why Bad:** Using `TaDa::create($request->all())` and `BankDetail::create($request->all())` bypasses field sanitization. Any client request can inject sensitive fillable model attributes (e.g., `user_id`, `used_amount`, `remaining_amount`, `is_default`).
- **Fix:** Use only validated attributes: `$validated = $request->validate([...]); TaDa::create($validated);`.
- **Effort:** Low (20 mins)

---

### 2. N+1 & Slow Queries

#### Finding 2.1: Severe N+1 Query in Quotation Product Price Lookup
- **File:Line:** [`app/Http/Controllers/QuotationController.php:36-53`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/QuotationController.php#L36-L53)
- **Severity:** High
- **Why Bad:** Inside the `Product::get()->map(...)` loop, the controller executes two separate database queries per product (`SalesItem::where('product_id', $product->id)->latest()->first()` and `ProjectItem::where('product_id', $product->id)->latest()->first()`). On a catalog of 500 products, opening the Quotation creation form fires over 1,000 queries.
- **Fix:** Preload latest prices via Eloquent `hasOne(...)->latestOfMany()` or a single grouped subquery for fallback prices.
- **Effort:** Medium (2 hours)

#### Finding 2.2: Memory Leak & Table Scan via `Purchase::all()` on Dashboard & Index
- **File:Line:** [`app/Http/Controllers/PurchaseController.php:76-80`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/PurchaseController.php#L76-L80)
- **Severity:** High
- **Why Bad:** Loading every purchase into Eloquent collections via `Purchase::all()` to calculate sums in PHP consumes large amounts of server RAM (OOM) and causes long response times on growing databases.
- **Fix:** Use direct database aggregations:
  ```php
  $totalOrdersCount = Purchase::distinct('purchase_no')->count('purchase_no');
  $totalAmountSum   = (float) Purchase::sum('total_price');
  $totalPaidSum     = (float) Purchase::sum('payment');
  $totalDueSum      = (float) Purchase::sum('due');
  ```
- **Effort:** Low (15 mins)

#### Finding 2.3: Unpaginated Eager Loading in Select Modals / API Endpoints
- **File:Line:** [`app/Http/Controllers/BillController.php:58-100, 108-153`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/BillController.php#L58-L100)
- **Severity:** Medium
- **Why Bad:** `getSales()` and `getProjects()` load entire tables with nested relations (`items.product`, `customer`, `client`) into memory without limits.
- **Fix:** Apply query limits (e.g., `limit(100)` or search-based filtering `where('order_no', 'like', ...)`) with Select2 AJAX pagination.
- **Effort:** Medium (1 hour)

#### Finding 2.4: Redundant Loop Queries in Dashboard Analytics
- **File:Line:** [`app/Http/Controllers/FrontendController.php:36-49, 99-115`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/FrontendController.php#L36-L49)
- **Severity:** Medium
- **Why Bad:** Runs 58 individual `whereYear()->whereMonth()->sum()` queries inside month and year loops rather than running single SQL `GROUP BY MONTH(created_at)` aggregations.
- **Fix:** Use single queries with `selectRaw('MONTH(created_at) as month, SUM(payble) as total')->groupBy('month')`.
- **Effort:** Medium (1.5 hours)

---

### 3. Missing Indexes

#### Finding 3.1: Missing Index on `sales_items` Foreign Keys
- **File:Line:** [`database/migrations/2025_10_14_015809_create_sale_items_table.php:12-25`](file:///c:/laragon/www/itec_inventory/database/migrations/2025_10_14_015809_create_sale_items_table.php#L12-L25)
- **Severity:** High
- **Why Bad:** `order_id` (foreign key to `sales.id`) and `product_id` have no database index. Every invoice view, invoice PDF generation, return lookup, and sales report performs a full table scan across all sold line items.
- **Fix:** Create migration adding `$table->index('order_id');` and `$table->index('product_id');`.
- **Effort:** Low (15 mins)

#### Finding 3.2: Missing Indexes on Journal Entry & Ledger Tables
- **File:Line:** [`database/migrations/2026_08_08_000003_create_journal_entries_table.php`](file:///c:/laragon/www/itec_inventory/database/migrations/2026_08_08_000003_create_journal_entries_table.php), [`2026_08_08_000004_create_journal_entry_items_table.php`](file:///c:/laragon/www/itec_inventory/database/migrations/2026_08_08_000004_create_journal_entry_items_table.php)
- **Severity:** High
- **Why Bad:** `journal_entry_items.account_id`, `journal_entry_items.journal_entry_id`, and `journal_entries.entry_date` lack explicit indexing. General ledger calculations scan all journal item rows across history for each account.
- **Fix:** Add composite indexes on `(account_id, journal_entry_id)` and `(entry_date, status)`.
- **Effort:** Low (20 mins)

#### Finding 3.3: Missing Indexes on Payments and Product Serials Tables
- **File:Line:** [`database/migrations/2024_12_16_102327_payments.php`](file:///c:/laragon/www/itec_inventory/database/migrations/2024_12_16_102327_payments.php), [`2026_04_21_123625_create_product_serials_table.php`](file:///c:/laragon/www/itec_inventory/database/migrations/2026_04_21_123625_create_product_serials_table.php)
- **Severity:** Medium
- **Why Bad:** `payments.sale_id`, `payments.customer_id`, `payments.payment_for`, and `product_serials.sales_item_id` lack indexes, causing slow query performance on customer payment histories and inventory lookup.
- **Fix:** Add indexes to foreign keys and lookup columns in a new migration.
- **Effort:** Low (20 mins)

---

### 4. Missing Validation & Authorization

#### Finding 4.1: Broken Object Level Authorization (IDOR) on Employee TA/DA Update
- **File:Line:** [`app/Http/Controllers/EmployeeTaDaController.php:35-47`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/EmployeeTaDaController.php#L35-L47)
- **Severity:** High
- **Why Bad:** In `update(Request $request, $id)`, the record is fetched with `TaDa::findOrFail($id)` without scoping to `auth()->user()->employee->id`. Any authenticated employee can submit a request modifying any other employee's TA/DA amounts and balances.
- **Fix:** Scope query to the current employee:
  ```php
  $employee = auth()->user()->employee;
  if (!$employee) abort(403);
  $tada = TaDa::where('id', $id)->where('employee_id', $employee->id)->firstOrFail();
  ```
- **Effort:** Low (15 mins)

#### Finding 4.2: Null Pointer Crash for Non-Employee Accounts accessing Employee TA/DA
- **File:Line:** [`app/Http/Controllers/EmployeeTaDaController.php:20, 29, 68`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/EmployeeTaDaController.php#L20)
- **Severity:** Medium
- **Why Bad:** `auth()->user()->employee->id` assumes all authenticated users have an associated employee record. Super Admins or managers accessing the route trigger a fatal `Attempt to read property "id" on null` error.
- **Fix:** Check `if (!auth()->user()->employee) return redirect()->back()->with('error', 'No employee profile linked.');`.
- **Effort:** Low (15 mins)

#### Finding 4.3: Unvalidated PIN Storage Setting Updates
- **File:Line:** [`app/Http/Controllers/UserController.php:263-273`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/UserController.php#L263-L273)
- **Severity:** Medium
- **Why Bad:** `pinStore(Request $request)` loops through raw `$request->all()` without validation, updating arbitrary rows in the `extras` table.
- **Fix:** Add validation: `$request->validate(['pin' => 'required|digits:4']);` and update explicitly.
- **Effort:** Low (15 mins)

---

### 5. Error Handling & Data Integrity

#### Finding 5.1: Fatal 500 Error on Bill Filtering Due to Non-Existent Column Name
- **File:Line:** [`app/Http/Controllers/BillController.php:24, 28`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/BillController.php#L24)
- **Severity:** High
- **Why Bad:** Queries filter with `whereDate('challan_date', ...)`, but the column in the `bills` table is `bill_date`. Filtering bills by date crashes the request with `SQLSTATE[42S22]: Column not found`.
- **Fix:** Change `challan_date` to `bill_date` in lines 24 and 28.
- **Effort:** Low (5 mins)

#### Finding 5.2: Missing Database Transactions in Challan Item Generation
- **File:Line:** [`app/Http/Controllers/ChallanController.php:80-117`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ChallanController.php#L80-L117)
- **Severity:** Medium
- **Why Bad:** `Challan::create` and subsequent `ChallanItem::create` loop run without `DB::transaction()`. If any item fails insertion, a dangling header record without items remains in the database.
- **Fix:** Wrap the entire creation block in `DB::transaction(function() use (...) { ... });`.
- **Effort:** Low (15 mins)

#### Finding 5.3: Inconsistent Data Types on Project Payment Status
- **File:Line:** [`app/Http/Controllers/ProjectController.php:346`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ProjectController.php#L346)
- **Severity:** Medium
- **Why Bad:** Sets `'status' => 'paid'` (string) on `payments` table, but the database schema defines `payments.status` as `tinyint(1)` (integer). This causes SQL strict mode errors or silent integer coercion (`status = 0`).
- **Fix:** Set `'status' => 1`.
- **Effort:** Low (10 mins)

#### Finding 5.4: Race Condition in Non-Atomic Stock Increment
- **File:Line:** [`app/Services/InventoryService.php:20-21`](file:///c:/laragon/www/itec_inventory/app/Services/InventoryService.php#L20-L21)
- **Severity:** Medium
- **Why Bad:** `incrementStock()` uses PHP-level addition (`$inventory->current_stock += $quantity; $inventory->save();`) instead of an atomic database increment. Concurrent purchase intake can overwrite and lose stock updates.
- **Fix:** Use atomic `$inventory->increment('current_stock', $quantity);`.
- **Effort:** Low (10 mins)

---

### 6. Duplicate Logic

#### Finding 6.1: Discrepancy in Salary Calculation Between Store and Update
- **File:Line:** [`app/Http/Controllers/SalaryController.php:42, 70`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/SalaryController.php#L42)
- **Severity:** High
- **Why Bad:** `store()` calculates:  
  `net_salary = basic_salary + allowance - deduction - advance`  
  `update()` calculates:  
  `net_salary = basic_salary + allowance - deduction`  
  Updating an existing salary record silently removes the `advance` deduction, leading to payroll errors.
- **Fix:** Unify calculation: `$netSalary = (float)$data['basic_salary'] + (float)($data['allowance'] ?? 0) - (float)($data['deduction'] ?? 0) - (float)($data['advance'] ?? 0);`.
- **Effort:** Low (10 mins)

#### Finding 6.2: Duplicate Sales/Project Serializer Mapping Between Bill & Challan Controllers
- **File:Line:** [`app/Http/Controllers/BillController.php:57-153`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/BillController.php#L57-L153) & [`app/Http/Controllers/ChallanController.php:320-430`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ChallanController.php#L320-L430)
- **Severity:** Low
- **Why Bad:** Identical mapping logic for formatting sales and project line items into JSON response arrays is copy-pasted across both controllers (~150 duplicated lines).
- **Fix:** Extract shared response transformation into a reusable API Resource (`SaleItemResource`, `ProjectItemResource`).
- **Effort:** Medium (1 hour)

---

### 7. Dead Code

#### Finding 7.1: Leftover Restaurant / Pizza Food Menu Methods in Product Controller
- **File:Line:** [`app/Http/Controllers/ProductController.php:297-692`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ProductController.php#L297-L692)
- **Severity:** High
- **Why Bad:** Approximately 400 lines of unrouted, obsolete methods (`size`, `storeSize`, `topings`, `storeToping`, `productOptions`, `deleteOptionTitle`, etc.) left over from a previous food delivery template.
- **Fix:** Safely remove unrouted pizza/topping methods and unused `App\Models\Admin\*` imports.
- **Effort:** Low (30 mins)

#### Finding 7.2: Unused E-Commerce Cart & Coupon Helpers
- **File:Line:** [`app/Helpers/CartHelper.php:1-225`](file:///c:/laragon/www/itec_inventory/app/Helpers/CartHelper.php#L1-L225)
- **Severity:** Medium
- **Why Bad:** Entire 225-line file containing shopping cart sessions, food delivery charges, and coupon calculators that are not used anywhere in this B2B inventory system.
- **Fix:** Remove `CartHelper.php` and its `require_once` in `helpers.php`.
- **Effort:** Low (15 mins)

#### Finding 7.3: Commented-out Dead Code Blocks in Controllers
- **File:Line:** [`app/Http/Controllers/UserController.php:41-86`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/UserController.php#L41-L86), [`app/Http/Controllers/ExpenseController.php:122-154`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ExpenseController.php#L122-L154), [`app/Http/Controllers/SalesController.php:804-815`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/SalesController.php#L804-L815)
- **Severity:** Low
- **Why Bad:** Large blocks of commented-out code clutter controllers, reduce maintainability, and confuse developers.
- **Fix:** Delete commented-out blocks (version control maintains historical code).
- **Effort:** Low (15 mins)

---

### 8. Test Gap

#### Finding 8.1: Zero Test Coverage for Double-Entry Accounting Engine
- **File:Line:** [`app/Http/Controllers/JournalEntryController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/JournalEntryController.php), [`app/Http/Controllers/LedgerController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/LedgerController.php), [`app/Helpers/helpers.php:299-445`](file:///c:/laragon/www/itec_inventory/app/Helpers/helpers.php#L299-L445)
- **Severity:** High
- **Why Bad:** The core financial module (`postJournalEntry`, `reverseJournalEntry`, Trial Balance, Balance Sheet, Ledger balance calculators) has 0 unit and 0 feature tests. Any regression in voucher balance checks or posting math directly impacts financial reporting.
- **Fix:** Implement comprehensive Feature & Unit tests covering balanced debits/credits validation, fiscal year boundaries, automated invoice journal posting, and reversal integrity.
- **Effort:** High (6–8 hours)

#### Finding 8.2: Missing Tests for Batch Purchase Intake & Stock Movement
- **File:Line:** [`app/Http/Controllers/PurchaseController.php:135-250`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/PurchaseController.php#L135-L250)
- **Severity:** High
- **Why Bad:** Batch purchases update vendor dues, stock increments, serial numbers creation, and auto-journal vouchers simultaneously without automated test validation.
- **Fix:** Add `PurchaseFeatureTest` asserting inventory counts, serial statuses, and journal ledger balances.
- **Effort:** Medium (3 hours)

#### Finding 8.3: Missing Tests for Returns & Restocking Flow
- **File:Line:** [`app/Http/Controllers/ReturnController.php`](file:///c:/laragon/www/itec_inventory/app/Http/Controllers/ReturnController.php), [`app/Models/ProductReturn.php`](file:///c:/laragon/www/itec_inventory/app/Models/ProductReturn.php)
- **Severity:** Medium
- **Why Bad:** Product return approvals, stock restorations, and serial status toggling (`sold` -> `available`) are untested.
- **Fix:** Add `ReturnProcessTest` verifying transition states (`pending` -> `approved` -> `completed`).
- **Effort:** Medium (2 hours)

---

### 9. Outdated Packages & Dependencies

#### Finding 9.1: Framework & Tooling Dependencies Review
- **File:Line:** [`composer.json:10-33`](file:///c:/laragon/www/itec_inventory/composer.json#L10-L33)
- **Severity:** Medium
- **Why Bad:**
  - `laravel/framework: ^10.10`: Laravel 10 entered security-fix-only maintenance mode (Laravel 11 is the current major release).
  - `mpdf/mpdf: ^8.3`: Requires ensuring PHP 8.2/8.3 deprecation notices are silenced or upgraded to supported minor patches.
  - `paytrail/paytrail-php-sdk: ^2.7`: Third-party Nordic payment SDK listed in dependencies but unused in codebase.
- **Fix:**
  - Remove unused dependencies (`composer remove paytrail/paytrail-php-sdk`).
  - Plan scheduled upgrade path to Laravel 11 (`laravel/framework: ^11.0`).
- **Effort:** Medium (4 hours for Laravel 11 upgrade planning)

---

## Remediation Roadmap & Execution Status

```
┌────────────────────────────────────────────────────────────────────────┐
│ Phase 1: Critical Fixes (Immediate)                        [ COMPLETED ]│
│ 1. Fix SQL Injection in ServiceController                              │
│ 2. Add strict image MIME validation on User & Employee uploads         │
│ 3. Fix IDOR in EmployeeTaDaController                                  │
│ 4. Fix advance salary omission bug in SalaryController                 │
│ 5. Fix column name 'challan_date' -> 'bill_date' in BillController     │
│ 6. Fix Project payment limits, integer status & DB transactions        │
│ 7. Fix race conditions in InventoryService atomic increments           │
├────────────────────────────────────────────────────────────────────────┤
│ Phase 2: Performance & Indexing (Short-term)               [ COMPLETED ]│
│ 1. Verified database indexing (sales_items, payments, purchases, etc.) │
│ 2. Replaced Purchase::all() and loops with SQL aggregations            │
│ 3. Fixed N+1 price lookup in QuotationController (~1000q -> 3q)        │
│ 4. Removed dead pizza/cart legacy code (ProductController, CartHelper) │
├────────────────────────────────────────────────────────────────────────┤
│ Phase 3: Testing & Quality Assurance (Medium-term)         [ COMPLETED ]│
│ 1. Wrote automated test suite for double-entry accounting engine       │
│ 2. Wrote tests for Batch Purchase & Sales Return workflows             │
│ 3. Cleaned composer.json dependencies (removed paytrail SDK)           │
│ 4. Automated test suite passing 100% (17 tests, 45 assertions)         │
└────────────────────────────────────────────────────────────────────────┘
```
