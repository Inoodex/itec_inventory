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
    TrialBalanceController, FinancialStatementController, ContraEntryController,
    ReconciliationController, FiscalYearController
};

use Illuminate\Support\Facades\{Auth, Route};

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// 1. DASHBOARD + EMPLOYEE-ONLY ROUTES → accessible by Super Admin AND Employee
Route::middleware(['auth', 'role:Super Admin|Employee'])->group(function () {

    // Dashboard - now accessible by both roles
    Route::get('/', [FrontendController::class, 'index'])->name('index');

    // Employee TA/DA section (they need this too)
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('tada', [EmployeeTaDaController::class, 'index'])->name('tada.index');
        Route::get('tada/create', [EmployeeTaDaController::class, 'create'])->name('tada.create');
        Route::post('tada/store', [EmployeeTaDaController::class, 'store'])->name('tada.store');
        Route::get('tada/{id}/edit', [EmployeeTaDaController::class, 'edit'])->name('tada.edit');
        Route::put('tada/{id}', [EmployeeTaDaController::class, 'update'])->name('tada.update');
    });
});

// 2. EVERYTHING ELSE → ONLY Super Admin
Route::middleware(['auth', 'role:Super Admin'])->group(function () {

    // === Administration ===
    Route::resource('users', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('/user/pin', [UserController::class, 'pin'])->name('users.pin');
    Route::post('/user/pin', [UserController::class, 'pinStore'])->name('users.pin_store');

    // === Products, Inventory, Purchases, Sales, Services, etc. ===
    Route::resource('brands', BrandController::class);
    Route::get('/products/barcode-lookup', [ProductController::class, 'barcodeLookup'])->name('products.barcode_lookup');
    Route::resource('products', ProductController::class);
    Route::get('/inventory/pdf', [InventoryController::class, 'downloadPdf'])->name('inventory.pdf');
    Route::resource('inventory', InventoryController::class);
    Route::get('inventory/{productId}/serials', [\App\Http\Controllers\ProductSerialController::class, 'getSerialsByProduct'])->name('inventory.serials');
    Route::get('/customers/pdf', [CustomerController::class, 'downloadPdf'])->name('customers.pdf');
    Route::resource('customers', CustomerController::class);
    Route::get('/vendors/pdf', [VendorController::class, 'downloadPdf'])
    ->name('vendors.pdf');
    Route::resource('vendors', VendorController::class);

    // === Warranties ===
    Route::get('warranties/lookup', [WarrantyController::class, 'lookup'])->name('warranties.lookup');
    Route::get('warranties/{id}/print', [WarrantyController::class, 'printReceipt'])->name('warranties.print');
    Route::resource('warranties', WarrantyController::class);
   

    Route::resource('purchase', PurchaseController::class);
    Route::get('purchase/latest-price/{id}', [PurchaseController::class, 'getLatestPrice'])->name('purchase.latest_price');

    Route::resource('sales', SalesController::class);
    Route::get('sales/invoice/{id}', [SalesController::class, 'makeInvoice'])->name('sales.invoice');
    Route::get('sales/invoice/{id}/pdf', [SalesController::class, 'downloadInvoicePdf'])->name('sales.invoice.pdf');
    Route::get('/sales/payments/{saleId?}', [SalesController::class, 'payments'])->name('sales.payments');
    Route::get('sales/{id}/details', [SalesController::class, 'getSaleDetails'])->name('sales.details');

    Route::get('service/pdf', [ServiceController::class, 'downloadPdf'])->name('service.pdf');
    Route::resource('service', ServiceController::class);
    Route::get('service/invoice/{id}', [ServiceController::class, 'makeInvoice'])->name('service.invoice');
    Route::get('complated/service', [ServiceController::class, 'complatedService'])->name('service.complated');
    Route::post('service/makecomplate/{id}', [ServiceController::class, 'makeComplate'])->name('service.makecomplate');
    Route::get('service-payments', [ServiceController::class, 'payments'])->name('service.payments');
    Route::post('service-payment/add', [PaymentController::class, 'addPayment'])->name('add.payment');
    Route::post('/submit-rating', [ServiceController::class, 'storeRating'])->name('submit.rating');

    // === Projects, Bills, Challans, Quotations, etc. ===
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/payments/{project}', [ProjectController::class, 'payments'])->name('projects.payments');
    Route::resource('clients', ClientController::class);
    Route::resource('cost-categories', CostCategoryController::class);
    Route::resource('project-costs', ProjectCostController::class);
    Route::resource('project-items', ProjectItemController::class);

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

    Route::get('/challans/pdf', [ChallanController::class, 'reportPdf'])->name('challans.pdf');
    Route::resource('challans', ChallanController::class);
    Route::get('/challans/{challan}/preview', [ChallanController::class, 'preview'])->name('challans.preview');
    Route::get('/challans/{challan}/download', [ChallanController::class, 'download'])->name('challans.download');
    Route::get('/get-sales', [ChallanController::class, 'getSales'])->name('challans.get-sales');
    Route::get('/get-projects', [ChallanController::class, 'getProjects'])->name('challans.get-projects');

    Route::get('/quotations/pdf', [QuotationController::class, 'reportPdf'])->name('quotations.pdf-report');
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    Route::get('/quotations/{quotation}/preview', [QuotationController::class, 'preview'])->name('quotations.preview');
    Route::get('/quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'sendQuotation'])->name('quotations.send');

    Route::get('/projects/{project}/bills/create', [ProjectBillController::class, 'createBill'])->name('projects.bills.create');
    Route::post('/projects/{project}/bills/', [ProjectBillController::class, 'storeBill'])->name('projects.bills.store');

    // === HR (Super Admin only) ===
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{id}', [EmployeeController::class, 'show'])->name('employees.view');
    Route::resource('ta-da', TaDaController::class);
    Route::resource('salary', SalaryController::class);
    Route::resource('daily-expenses', ExpenseController::class)->names('dailyExpenses');
    Route::resource('expense-categories', ExpenseCategoryController::class);

    Route::post('/salary/get-tada-data-ajax', [SalaryController::class, 'getTaDaDataAjax'])->name('salary.get-tada-data-ajax');
    Route::get('/employee/{id}/advance-sum-by-month', [EmployeeController::class, 'getAdvanceSumByMonth']);
    Route::get('/employee/{id}/advance-sum', [ExpenseController::class, 'getAdvanceSum']);

    // === Reports & Payments ===
    Route::get('purchase-report', [PurchaseController::class, 'reportIndex'])->name('purchase.report');
    Route::get('purchase/report', [PurchaseController::class, 'report'])->name('purchase.report.get');
    Route::get('purchase/report/pdf', [PurchaseController::class, 'reportPdf'])->name('purchase.report.pdf');
    Route::get('sales-report', [SalesController::class, 'report'])->name('sales.report');
    Route::get('sales-report/pdf', [SalesController::class, 'reportPdf'])->name('sales.report.pdf');
    Route::get('/revenues/pdf', [RevenueController::class, 'downloadPdf'])->name('revenues.pdf');
    Route::get('/revenues', [RevenueController::class, 'index'])->name('revenues.index');
    Route::post('/revenues/generate', [RevenueController::class, 'generate'])->name('revenues.generate');
    Route::get('/revenues/export/{id}', [RevenueController::class, 'export'])->name('revenues.export');
    Route::get('/due-payments', [SalesController::class, 'duePayments'])->name('due-payments.index');
    Route::get('/due-payments/pdf', [SalesController::class, 'duePaymentsPdf'])->name('due-payments.pdf');

    Route::post('/sales/process-payment', [SalesController::class, 'processPayment'])->name('sales.process-payment');
    Route::post('/projects/process-payment', [ProjectController::class, 'processPayment'])->name('projects.process-payment');
    Route::get('/sales/search-orders', [SalesController::class, 'searchOrders'])->name('sales.search-orders');

    Route::resource('bank-details', BankDetailController::class);
    Route::post('bank-details/{bankDetail}/set-default', [BankDetailController::class, 'setDefault'])->name('bank-details.set-default');
    
    Route::resource('company-details', CompanyDetailController::class);
    Route::post('company-details/{companyDetail}/set-default', [CompanyDetailController::class, 'setDefault'])->name('company-details.set-default');

        Route::get('product-returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('product-returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('product-returns', [ReturnController::class, 'store'])->name('returns.store');
        Route::get('product-returns/sale-items/{saleId}', [ReturnController::class, 'getSaleItems'])->name('returns.sale.items');
        Route::get('product-returns/{id}', [ReturnController::class, 'show'])->name('returns.show');
        Route::delete('product-returns/{id}', [ReturnController::class, 'destroy'])->name('returns.destroy');
        Route::patch('product-returns/{id}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
        Route::patch('product-returns/{id}/complete', [ReturnController::class, 'complete'])->name('returns.complete');
        Route::patch('product-returns/{id}/reject', [ReturnController::class, 'reject'])->name('returns.reject');

        // =========================================================================
        // DOUBLE-ENTRY ACCOUNTS & BOOKKEEPING (SUPER ADMIN)
        // =========================================================================
        Route::prefix('accounts')->group(function () {
            // Redirect legacy accounts dashboard to Main Unified Dashboard
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

            // Contra Entries (Transfers)
            Route::resource('contra-entries', ContraEntryController::class)->only(['index', 'create', 'store']);

            // Bank Reconciliation
            Route::get('reconciliation', [ReconciliationController::class, 'index'])->name('reconciliation.index');
            Route::post('reconciliation', [ReconciliationController::class, 'store'])->name('reconciliation.store');

            // Fiscal Years & Year-End Close
            Route::get('fiscal-years', [FiscalYearController::class, 'index'])->name('fiscal-years.index');
            Route::post('fiscal-years', [FiscalYearController::class, 'store'])->name('fiscal-years.store');
            Route::post('fiscal-years/{fiscalYear}/set-active', [FiscalYearController::class, 'setActive'])->name('fiscal-years.set-active');
            Route::post('fiscal-years/{fiscalYear}/close', [FiscalYearController::class, 'closeYear'])->name('fiscal-years.close');
        });
});

