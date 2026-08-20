<?php

namespace Database\Seeders;

use App\Models\BankDetail;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

            // Clean up old unused intermediate header accounts
            $keepCodes = ['1000', '1110', '1120', '1130', '1140', '1210', '2000', '2110', '2120', '3000', '3100', '3200', '4000', '4110', '4120', '4140', '5000', '5110', '5210', '5230'];
            
            // Delete old unused accounts that have no journal items and no bank detail
            ChartOfAccount::whereNotIn('account_code', $keepCodes)
                ->whereNull('bank_detail_id')
                ->whereDoesntHave('journalItems')
                ->delete();

            // Inserter / updater
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

            // 3. Auto-sync existing BankDetail records under Bank & Mobile Accounts (1120)
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
        });
    }
}
