<?php

namespace Database\Seeders;

use App\Models\BankDetail;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the simplified 2-tier Chart of Accounts (14 core accounts)
     * and cleanly consolidates/removes legacy bloated accounts from live databases.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Seed or ensure current active Fiscal Year
            $currentYear = date('Y');
            $fiscalYearName = "{$currentYear}-" . ($currentYear + 1);

            $fiscalYear = FiscalYear::firstOrCreate(
                ['year_name' => $fiscalYearName],
                [
                    'start_date' => "{$currentYear}-01-01",
                    'end_date' => "{$currentYear}-12-31",
                    'is_active' => true,
                    'is_closed' => false,
                ]
            );

            FiscalYear::where('id', '!=', $fiscalYear->id)->update(['is_active' => false]);

            // 2. Define Clean Simplified 2-Tier Chart of Accounts
            $accounts = [
                // ==================== ASSETS (1000) ====================
                [
                    'code' => '1000',
                    'name' => 'Assets',
                    'type' => 'asset',
                    'level' => 1,
                    'is_system' => true,
                    'children' => [
                        [
                            'code' => '1110',
                            'name' => 'Cash in Hand',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '1120',
                            'name' => 'Bank & Mobile Accounts',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '1130',
                            'name' => 'Accounts Receivable (Customer Dues)',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '1140',
                            'name' => 'Inventory Asset / Stock',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '1210',
                            'name' => 'Office Equipment & Fixed Assets',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => false,
                        ],
                    ],
                ],

                // ==================== LIABILITIES (2000) ====================
                [
                    'code' => '2000',
                    'name' => 'Liabilities',
                    'type' => 'liability',
                    'level' => 1,
                    'is_system' => true,
                    'children' => [
                        [
                            'code' => '2110',
                            'name' => 'Accounts Payable (Supplier / Vendor Dues)',
                            'type' => 'liability',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '2120',
                            'name' => 'VAT / Tax Payable',
                            'type' => 'liability',
                            'level' => 2,
                            'is_system' => true,
                        ],
                    ],
                ],

                // ==================== EQUITY (3000) ====================
                [
                    'code' => '3000',
                    'name' => 'Equity',
                    'type' => 'equity',
                    'level' => 1,
                    'is_system' => true,
                    'children' => [
                        [
                            'code' => '3100',
                            'name' => 'Owner Capital',
                            'type' => 'equity',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '3200',
                            'name' => 'Retained Earnings',
                            'type' => 'equity',
                            'level' => 2,
                            'is_system' => true,
                        ],
                    ],
                ],

                // ==================== REVENUE (4000) ====================
                [
                    'code' => '4000',
                    'name' => 'Revenue / Income',
                    'type' => 'revenue',
                    'level' => 1,
                    'is_system' => true,
                    'children' => [
                        [
                            'code' => '4110',
                            'name' => 'Sales Revenue',
                            'type' => 'revenue',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '4120',
                            'name' => 'Service & Project Revenue',
                            'type' => 'revenue',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '4140',
                            'name' => 'Delivery Charge Income',
                            'type' => 'revenue',
                            'level' => 2,
                            'is_system' => true,
                        ],
                    ],
                ],

                // ==================== EXPENSES (5000) ====================
                [
                    'code' => '5000',
                    'name' => 'Expenses',
                    'type' => 'expense',
                    'level' => 1,
                    'is_system' => true,
                    'children' => [
                        [
                            'code' => '5110',
                            'name' => 'Cost of Goods Sold (Purchase Expense)',
                            'type' => 'expense',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '5210',
                            'name' => 'Salaries & Staff Expenses',
                            'type' => 'expense',
                            'level' => 2,
                            'is_system' => true,
                        ],
                        [
                            'code' => '5230',
                            'name' => 'Daily Office Expenses',
                            'type' => 'expense',
                            'level' => 2,
                            'is_system' => true,
                        ],
                    ],
                ],
            ];

            // 3. Upsert Root & Child Accounts
            $insertNode = function ($node, $parentId = null) use (&$insertNode) {
                $account = ChartOfAccount::updateOrCreate(
                    ['account_code' => $node['code']],
                    [
                        'account_name' => $node['name'],
                        'account_type' => $node['type'],
                        'parent_id' => $parentId,
                        'level' => $node['level'],
                        'is_active' => true,
                        'is_system' => $node['is_system'] ?? false,
                    ]
                );

                if (!empty($node['children'])) {
                    foreach ($node['children'] as $child) {
                        $insertNode($child, $account->id);
                    }
                }

                return $account;
            };

            foreach ($accounts as $rootAccount) {
                $insertNode($rootAccount);
            }

            // 4. Auto-sync existing BankDetail records under Bank & Mobile Accounts (1120)
            $bankParent = ChartOfAccount::where('account_code', '1120')->first();
            if ($bankParent) {
                $bankDetails = BankDetail::all();
                $seq = 1;
                foreach ($bankDetails as $bank) {
                    $subCode = '1120-' . str_pad($seq++, 2, '0', STR_PAD_LEFT);
                    ChartOfAccount::updateOrCreate(
                        ['bank_detail_id' => $bank->id],
                        [
                            'account_code' => $subCode,
                            'account_name' => "{$bank->bank_name} ({$bank->account_number})",
                            'account_type' => 'asset',
                            'parent_id' => $bankParent->id,
                            'level' => 3,
                            'is_active' => $bank->is_active,
                            'is_system' => false,
                        ]
                    );
                }
            }

            // 5. Re-map legacy journal entry items to simplified accounts (Protects all transaction data!)
            $legacyMapping = [
                '1100' => '1000',
                '1111' => '1110', // Petty Cash -> Cash in Hand
                '1112' => '1110', // Cash Register -> Cash in Hand
                '1121' => '1120',
                '1122' => '1120',
                '1123' => '1120',
                '1131' => '1130', // Trade Debtors -> Accounts Receivable
                '1132' => '1130',
                '1141' => '1140', // Merchandise -> Stock
                '1142' => '1140',
                '1200' => '1000',
                '1211' => '1210', // Computers -> Office Equipment
                '1212' => '1210', // Furniture -> Office Equipment
                '2100' => '2000',
                '2111' => '2110', // Creditors -> Accounts Payable
                '2112' => '2110',
                '2121' => '2120', // VAT -> VAT Payable
                '2122' => '2120',
                '2200' => '2000',
                '3110' => '3100', // Capital -> Owner Capital
                '3120' => '3100',
                '4100' => '4000',
                '4111' => '4110', // Retail -> Sales Revenue
                '4112' => '4110', // Wholesale -> Sales Revenue
                '4121' => '4120', // Project Income -> Service & Project
                '4122' => '4120',
                '4130' => '4140',
                '4131' => '4140',
                '5100' => '5000',
                '5111' => '5110', // Purchases -> COGS
                '5112' => '5110',
                '5200' => '5000',
                '5211' => '5210', // Basic Salary -> Salaries
                '5212' => '5210', // TA/DA -> Salaries
                '5220' => '5230', // Rent -> Office Expenses
                '5221' => '5230',
                '5231' => '5230', // Supplies -> Office Expenses
                '5232' => '5230', // Utilities -> Office Expenses
                '5233' => '5230', // Misc -> Office Expenses
            ];

            foreach ($legacyMapping as $oldCode => $newCode) {
                $oldAcc = ChartOfAccount::where('account_code', $oldCode)->first();
                $newAcc = ChartOfAccount::where('account_code', $newCode)->first();

                if ($oldAcc && $newAcc && $oldAcc->id !== $newAcc->id) {
                    JournalEntryItem::where('account_id', $oldAcc->id)
                        ->update(['account_id' => $newAcc->id]);
                }
            }

            // 6. Permanently delete all old redundant accounts from live database
            $keepCodes = [
                '1000', '1110', '1120', '1130', '1140', '1210',
                '2000', '2110', '2120',
                '3000', '3100', '3200',
                '4000', '4110', '4120', '4140',
                '5000', '5110', '5210', '5230'
            ];

            // Safely delete accounts not in keep list and not a linked bank account
            ChartOfAccount::whereNotIn('account_code', $keepCodes)
                ->whereNull('bank_detail_id')
                ->where('level', '!=', 3) // Protect custom level 3 bank/MFS accounts
                ->delete();
        });
    }
}
