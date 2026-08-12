# Accounts Module — How It Works

A complete double-entry bookkeeping system built into the itech inventory Laravel app.

---

## Architecture Overview

`
Chart of Accounts
    └── Journal Entry Items (account_id)
            └── Journal Entries (journal_entry_id)
                    ├── Fiscal Years (fiscal_year_id)
                    └── Sales / Purchase / Expense / etc. (reference_type + reference_id)

Contra Entries ──────────────────────► Journal Entries
Bank Reconciliation ─────────────────► Chart of Accounts
Ledger / Trial Balance / Reports ────► reads Journal Entry Items
`

---

## 1. Chart of Accounts (chart_of_accounts)

The **backbone** of the whole system. Every financial account lives here.

| Field | Purpose |
|---|---|
| account_code | Numeric code (e.g. 1110, 2000) |
| account_type | asset, liability, equity, revenue, expense |
| parent_id | Self-referential — hierarchical tree structure |
| level | Depth in the tree (1 = root, 2 = sub-account, etc.) |
| opening_balance | Balance at start of period |
| is_system | Cannot be deleted (system accounts) |
| bank_detail_id | Linked bank profile if it's a bank account |

### Account Type Ranges

| Type | Code Range | Normal Balance |
|---|---|---|
| Asset | 1xxx | Debit |
| Liability | 2xxx | Credit |
| Equity | 3xxx | Credit |
| Revenue | 4xxx | Credit |
| Expense | 5xxx | Debit |

calculateBalance(sOfDate) computes the live running balance from all posted journal entry items up to any given date.

---

## 2. Journal Entries (journal_entries + journal_entry_items)

The **core transaction record**. Every financial event creates a journal entry.

### Journal Entry Header

| Field | Purpose |
|---|---|
| journal_no | Auto-generated: JV-YYYYMMDD-0001 |
| entry_date | Transaction date |
| fiscal_year_id | Must fall within active fiscal year |
| reference_type | Source: manual, sale, purchase, expense, contra, etc. |
| reference_id | ID of the source record (e.g. sale ID) |
| status | draft > posted > approved > reversed |
| total_debit / total_credit | Must always be equal (double-entry law) |

### The Golden Rule

`
Total Debit == Total Credit
`

The postJournalEntry() helper enforces this — throws a DomainException if unbalanced.

### Auto-Created by Other Modules

- A Sale — Debit Accounts Receivable → Credit Revenue
- A Purchase — Debit Inventory/Expense → Credit Accounts Payable
- A Contra Transfer — Debit Destination → Credit Source account
- An Expense — Debit Expense account → Credit Cash/Bank

---

## 3. Contra Entries (contra_entries)

Cash/bank transfers between liquid accounts only (Cash 1110 ↔ Bank 1120).

On save:
1. Calls postJournalEntry() → creates the double-entry voucher
2. Creates a ContraEntry record linked via journal_entry_id
3. Auto-generates voucher number: CE-YYYYMMDD-0001

---

## 4. Fiscal Years (fiscal_years)

Defines the accounting period.

- Only one fiscal year active at a time
- setActive switches the active year
- closeYear marks it closed — blocks new journal posting

---

## 5. General Ledger (LedgerController)

Read-only report — shows running balance of a selected account over a date range.
No data stored — computed live on every request. Exportable to PDF.

---

## 6. Trial Balance (TrialBalanceController)

All active accounts listed with their Debit and Credit balances.

`
Sum of all Debit balances == Sum of all Credit balances → Balanced
`

---

## 7. Financial Statements (FinancialStatementController)

### Profit & Loss
Net Profit = Revenue (4xxx) − Expense (5xxx)

### Balance Sheet
Assets (1xxx) = Liabilities (2xxx) + Equity (3xxx) + Current Earnings

### Cash Flow (Direct Method)
Opening Cash + Inflows − Outflows = Closing Cash
(tracks 1110 Cash and 1120 Bank accounts only)

---

## 8. Bank Reconciliation (ReconciliationController)

Matches bank statement entries against posted journal entries.
Stored in account_reconciliations table.

---

## Data Flow — Sale to Accounting

`
User creates Sale
    ↓
SalesController::store()
    ↓
postJournalEntry([
    reference_type: 'sale',
    items: [
        [Accounts Receivable → DEBIT],
        [Sales Revenue       → CREDIT],
    ]
])
    ↓
Picked up automatically by:
    ✅ Ledger
    ✅ Trial Balance
    ✅ Profit & Loss
    ✅ Balance Sheet
    ✅ Cash Flow
`

---

## Key Helper Functions (app/Helpers/helpers.php)

| Function | Purpose |
|---|---|
| postJournalEntry(array $data) | Core engine — validates balance, creates entry + items in DB transaction |
| reverseJournalEntry(int $id, string $reason) | Flips debit/credit to undo an entry (Storno method) |
| getAccountBalance($id, ?$date) | Get live balance for any account by ID or code |
| getActiveFiscalYear() | Returns the currently active fiscal year |

---

## Route Map (/accounts/*)

All routes require: auth + role:Super Admin

| URL | Controller | Purpose |
|---|---|---|
| /accounts/chart-of-accounts | ChartOfAccountController | Manage accounts tree |
| /accounts/journal-entries | JournalEntryController | View/create/reverse vouchers |
| /accounts/contra-entries | ContraEntryController | Cash/bank transfers |
| /accounts/ledger | LedgerController | Account ledger report |
| /accounts/trial-balance | TrialBalanceController | Trial balance report |
| /accounts/reports/profit-loss | FinancialStatementController | P&L statement |
| /accounts/reports/balance-sheet | FinancialStatementController | Balance sheet |
| /accounts/reports/cash-flow | FinancialStatementController | Cash flow |
| /accounts/reconciliation | ReconciliationController | Bank reconciliation |
| /accounts/fiscal-years | FiscalYearController | Manage fiscal periods |

---

## Key Models

| Model | Table | Purpose |
|---|---|---|
| ChartOfAccount | chart_of_accounts | Account definitions + balance calculation |
| JournalEntry | journal_entries | Voucher headers |
| JournalEntryItem | journal_entry_items | Debit/credit line items |
| ContraEntry | contra_entries | Cash/bank transfer records |
| FiscalYear | fiscal_years | Accounting period management |
| AccountReconciliation | account_reconciliations | Bank reconciliation records |
