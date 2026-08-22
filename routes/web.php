<?php

use App\Http\Controllers\{
    BillController, BrandController, CategoryController, ChallanController, ClientController,
    CostCategoryController, CustomerController, EmployeeController,
    EmployeeTaDaController, ExpenseCategoryController, ExpenseController,
    FrontendController, InventoryController, ProductController,
    ProjectBillController, ProjectController, ProjectCostController,
    ProjectItemController, PurchaseController, QuotationController,
    RevenueController, RoleController, PermissionController,
    SalaryController, SalesController, ServiceController,
    TaDaController, UserController, VendorController, BankDetailController,
    CompanyDetailController, PaymentController, ReturnController, WarrantyController,
    ChartOfAccountController, JournalEntryController, LedgerController,
    TrialBalanceController, FinancialStatementController,
    FiscalYearController, VendorDueController
};

use Illuminate\Support\Facades\{Auth, Route};

// Authentication routes
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// Legacy / alias redirects
Route::redirect('/admin/dashboard', '/');
Route::redirect('/home', '/');

// =========================================================================
// 1. DASHBOARD & EMPLOYEE PORTAL (Accessible by all authenticated users)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Main Dashboard
    Route::get('/', [FrontendController::class, 'index'])->name('index');

    // Employee TA/DA self-service portal
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('tada', [EmployeeTaDaController::class, 'index'])->name('tada.index');
        Route::get('tada/create', [EmployeeTaDaController::class, 'create'])->name('tada.create');
        Route::post('tada/store', [EmployeeTaDaController::class, 'store'])->name('tada.store');
        Route::get('tada/{id}/edit', [EmployeeTaDaController::class, 'edit'])->name('tada.edit');
        Route::put('tada/{id}', [EmployeeTaDaController::class, 'update'])->name('tada.update');
    });
});

// =========================================================================
// 2. SYSTEM ADMINISTRATION & SECURITY
// =========================================================================
Route::middleware(['auth', 'permission:Administration'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::get('/user/pin', [UserController::class, 'pin'])->name('users.pin');
    Route::post('/user/pin', [UserController::class, 'pinStore'])->name('users.pin_store');
});

// =========================================================================
// 3. PRODUCT CATALOG
// =========================================================================
Route::middleware(['auth', 'permission:Product Management'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::get('/products/barcode-lookup', [ProductController::class, 'barcodeLookup'])->name('products.barcode_lookup');
});

// =========================================================================
// 4. INVENTORY & STOCK
// =========================================================================
Route::middleware(['auth', 'permission:Inventory Management'])->group(function () {
    Route::get('/inventory/pdf', [InventoryController::class, 'downloadPdf'])->name('inventory.pdf');
    Route::resource('inventory', InventoryController::class);
    Route::get('inventory/{productId}/serials', [\App\Http\Controllers\ProductSerialController::class, 'getSerialsByProduct'])->name('inventory.serials');
});

// =========================================================================
// 5. CUSTOMER MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Customer Management'])->group(function () {
    Route::get('/customers/pdf', [CustomerController::class, 'downloadPdf'])->name('customers.pdf');
    Route::resource('customers', CustomerController::class);
});

// =========================================================================
// 6. VENDOR MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Vendor Management'])->group(function () {
    Route::get('/vendors/pdf', [VendorController::class, 'downloadPdf'])->name('vendors.pdf');
    Route::resource('vendors', VendorController::class);
});

// =========================================================================
// 7. WARRANTY MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Warranty Management'])->group(function () {
    Route::get('warranties/lookup', [WarrantyController::class, 'lookup'])->name('warranties.lookup');
    Route::get('warranties/{id}/print', [WarrantyController::class, 'printReceipt'])->name('warranties.print');
    Route::resource('warranties', WarrantyController::class);
});

// =========================================================================
// 8. PURCHASE & PROCUREMENT
// =========================================================================
Route::middleware(['auth', 'permission:Purchase Management'])->group(function () {
    Route::post('purchase/store-batch', [PurchaseController::class, 'storeBatch'])->name('purchase.store.batch');
    Route::get('purchase/latest-price/{id}', [PurchaseController::class, 'getLatestPrice'])->name('purchase.latest_price');
    Route::resource('purchase', PurchaseController::class);
    Route::get('purchase-report', [PurchaseController::class, 'reportIndex'])->name('purchase.report');
    Route::get('purchase/report', [PurchaseController::class, 'report'])->name('purchase.report.get');
    Route::get('purchase/report/pdf', [PurchaseController::class, 'reportPdf'])->name('purchase.report.pdf');
});

// =========================================================================
// 9. SALES & RETURNS MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Sales Management'])->group(function () {
    Route::resource('sales', SalesController::class);
    Route::get('sales/invoice/{id}', [SalesController::class, 'makeInvoice'])->name('sales.invoice');
    Route::get('sales/invoice/{id}/pdf', [SalesController::class, 'downloadInvoicePdf'])->name('sales.invoice.pdf');
    Route::get('/sales/payments/{saleId?}', [SalesController::class, 'payments'])->name('sales.payments');
    Route::get('sales/{id}/details', [SalesController::class, 'getSaleDetails'])->name('sales.details');
    Route::get('/sales/search-orders', [SalesController::class, 'searchOrders'])->name('sales.search-orders');
    Route::post('/sales/process-payment', [SalesController::class, 'processPayment'])->name('sales.process-payment');

    // Product Returns
    Route::get('product-returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('product-returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('product-returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('product-returns/sale-items/{saleId}', [ReturnController::class, 'getSaleItems'])->name('returns.sale.items');
    Route::get('product-returns/{id}', [ReturnController::class, 'show'])->name('returns.show');
    Route::delete('product-returns/{id}', [ReturnController::class, 'destroy'])->name('returns.destroy');
    Route::patch('product-returns/{id}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
    Route::patch('product-returns/{id}/complete', [ReturnController::class, 'complete'])->name('returns.complete');
    Route::patch('product-returns/{id}/reject', [ReturnController::class, 'reject'])->name('returns.reject');
});

// =========================================================================
// 10. SERVICE MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Service Management'])->group(function () {
    Route::get('service/pdf', [ServiceController::class, 'downloadPdf'])->name('service.pdf');
    Route::resource('service', ServiceController::class);
    Route::get('service/invoice/{id}', [ServiceController::class, 'makeInvoice'])->name('service.invoice');
    Route::get('complated/service', [ServiceController::class, 'complatedService'])->name('service.complated');
    Route::post('service/makecomplate/{id}', [ServiceController::class, 'makeComplate'])->name('service.makecomplate');
    Route::get('service-payments', [ServiceController::class, 'payments'])->name('service.payments');
    Route::post('service-payment/add', [PaymentController::class, 'addPayment'])->name('add.payment');
    Route::post('/submit-rating', [ServiceController::class, 'storeRating'])->name('submit.rating');
});

// =========================================================================
// 11. PROJECT & CLIENT MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Project Management'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/payments/{project}', [ProjectController::class, 'payments'])->name('projects.payments');
    Route::post('/projects/process-payment', [ProjectController::class, 'processPayment'])->name('projects.process-payment');
    Route::resource('clients', ClientController::class);
    Route::resource('cost-categories', CostCategoryController::class);
    Route::resource('project-costs', ProjectCostController::class);
    Route::resource('project-items', ProjectItemController::class);
    Route::get('/projects/{project}/bills/create', [ProjectBillController::class, 'createBill'])->name('projects.bills.create');
    Route::post('/projects/{project}/bills/', [ProjectBillController::class, 'storeBill'])->name('projects.bills.store');
});

// =========================================================================
// 12. DUES, BILLING & PAYMENT MANAGEMENT
// =========================================================================
Route::middleware(['auth', 'permission:Payment Management|Sales Management|Accounts Management'])->group(function () {
    // Customer Due
    Route::get('/due-payments', [SalesController::class, 'duePayments'])->name('due-payments.index');
    Route::get('/due-payments/pdf', [SalesController::class, 'duePaymentsPdf'])->name('due-payments.pdf');

    // Vendor Due
    Route::get('/vendor-due', [VendorDueController::class, 'index'])->name('vendor-due.index');
    Route::get('/vendor-due/pdf', [VendorDueController::class, 'downloadPdf'])->name('vendor-due.pdf');
    Route::post('/vendor-due/pay', [VendorDueController::class, 'processPayment'])->name('vendor-due.process-payment');

    // Bills
    Route::prefix('bills')->group(function () {
        Route::get('/', [BillController::class, 'index'])->name('bills.index');
        Route::get('/pdf', [BillController::class, 'reportPdf'])->name('bills.pdf');
        Route::get('/create', [BillController::class, 'create'])->name('bills.create');
        Route::get('/get-sales', [BillController::class, 'getSales'])->name('api.sales');
        Route::get('/get-projects', [BillController::class, 'getProjects'])->name('api.projects');
        Route::post('/', [BillController::class, 'store'])->name('bills.store');
        Route::get('/{bill}', [BillController::class, 'show'])->name('bills.show');
        Route::get('/{bill}/preview', [BillController::class, 'preview'])->name('bills.preview');
        Route::get('/{bill}/download', [BillController::class, 'download'])->name('bills.download');
        Route::post('/{bill}/status', [BillController::class, 'updateStatus'])->name('bills.status.update');
        Route::delete('/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
    });

    // Challans
    Route::get('/challans/pdf', [ChallanController::class, 'reportPdf'])->name('challans.pdf');
    Route::resource('challans', ChallanController::class);
    Route::get('/challans/{challan}/preview', [ChallanController::class, 'preview'])->name('challans.preview');
    Route::get('/challans/{challan}/download', [ChallanController::class, 'download'])->name('challans.download');
    Route::get('/get-sales', [ChallanController::class, 'getSales'])->name('challans.get-sales');
    Route::get('/get-projects', [ChallanController::class, 'getProjects'])->name('challans.get-projects');

    // Quotations
    Route::get('/quotations/pdf', [QuotationController::class, 'reportPdf'])->name('quotations.pdf-report');
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    Route::get('/quotations/{quotation}/preview', [QuotationController::class, 'preview'])->name('quotations.preview');
    Route::get('/quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'sendQuotation'])->name('quotations.send');
});

// =========================================================================
// 13. HR, EMPLOYEES, SALARIES & EXPENSES
// =========================================================================
Route::middleware(['auth', 'permission:Employee Management|Accounts Management'])->group(function () {
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{id}', [EmployeeController::class, 'show'])->name('employees.view');
    Route::resource('ta-da', TaDaController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('daily-expenses', ExpenseController::class)->names('dailyExpenses');
    Route::resource('expense-categories', ExpenseCategoryController::class);

    Route::post('/salary/get-tada-data-ajax', [SalaryController::class, 'getTaDaDataAjax'])->name('salary.get-tada-data-ajax');
    Route::get('/employee/{id}/advance-sum-by-month', [EmployeeController::class, 'getAdvanceSumByMonth']);
    Route::get('/employee/{id}/advance-sum', [ExpenseController::class, 'getAdvanceSum']);
});

// =========================================================================
// 14. COMPANY DETAILS & BANK DETAILS
// =========================================================================
Route::middleware(['auth', 'permission:Company Management|Accounts Management'])->group(function () {
    Route::resource('bank-details', BankDetailController::class);
    Route::post('bank-details/{bankDetail}/set-default', [BankDetailController::class, 'setDefault'])->name('bank-details.set-default');
    Route::resource('company-details', CompanyDetailController::class);
    Route::post('company-details/{companyDetail}/set-default', [CompanyDetailController::class, 'setDefault'])->name('company-details.set-default');
});

// =========================================================================
// 15. REPORTS & REVENUE ANALYTICS
// =========================================================================
Route::middleware(['auth', 'permission:Report Management|Sales Management|Accounts Management'])->group(function () {
    Route::get('sales-report', [SalesController::class, 'report'])->name('sales.report');
    Route::get('sales-report/pdf', [SalesController::class, 'reportPdf'])->name('sales.report.pdf');
    Route::get('/revenues/pdf', [RevenueController::class, 'downloadPdf'])->name('revenues.pdf');
    Route::get('/revenues', [RevenueController::class, 'index'])->name('revenues.index');
    Route::post('/revenues/generate', [RevenueController::class, 'generate'])->name('revenues.generate');
    Route::get('/revenues/export/{id}', [RevenueController::class, 'export'])->name('revenues.export');
});

// =========================================================================
// 16. DOUBLE-ENTRY ACCOUNTS & BOOKKEEPING
// =========================================================================
Route::middleware(['auth', 'permission:Accounts Management'])->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::get('dashboard', function () {
            return redirect('/');
        })->name('accounts.dashboard');

        // Chart of Accounts
        Route::resource('chart-of-accounts', ChartOfAccountController::class);

        // Journal Entries & Vouchers
        Route::get('journal-entries/{journalEntry}/pdf', [JournalEntryController::class, 'downloadPdf'])->name('journal-entries.pdf');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
        Route::resource('journal-entries', JournalEntryController::class)->except(['edit', 'update', 'destroy']);

        // General Ledger
        Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
        Route::get('ledger/pdf', [LedgerController::class, 'downloadPdf'])->name('ledger.pdf');

        // Trial Balance
        Route::get('trial-balance', [TrialBalanceController::class, 'index'])->name('trial-balance.index');
        Route::get('trial-balance/pdf', [TrialBalanceController::class, 'downloadPdf'])->name('trial-balance.pdf');

        // Financial Statements & Reports
        Route::get('reports/profit-loss', [FinancialStatementController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('reports/profit-loss/pdf', [FinancialStatementController::class, 'profitLossPdf'])->name('reports.profit-loss.pdf');
        Route::get('reports/balance-sheet', [FinancialStatementController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('reports/balance-sheet/pdf', [FinancialStatementController::class, 'balanceSheetPdf'])->name('reports.balance-sheet.pdf');
        Route::get('reports/cash-flow', [FinancialStatementController::class, 'cashFlow'])->name('reports.cash-flow');

        // Fiscal Years & Year-End Close
        Route::get('fiscal-years', [FiscalYearController::class, 'index'])->name('fiscal-years.index');
        Route::post('fiscal-years', [FiscalYearController::class, 'store'])->name('fiscal-years.store');
        Route::post('fiscal-years/{fiscalYear}/set-active', [FiscalYearController::class, 'setActive'])->name('fiscal-years.set-active');
        Route::post('fiscal-years/{fiscalYear}/close', [FiscalYearController::class, 'closeYear'])->name('fiscal-years.close');
    });
});
