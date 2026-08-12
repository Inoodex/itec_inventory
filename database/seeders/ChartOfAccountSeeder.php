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

            // Set any other fiscal year active to false if exists
            FiscalYear::where('id', '!=', $fiscalYear->id)->update(['is_active' => false]);

            // 2. Define Master Hierarchical Chart of Accounts
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
                            'code' => '1100',
                            'name' => 'Current Assets',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '1110',
                                    'name' => 'Cash in Hand',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '1120',
                                    'name' => 'Bank Accounts',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '1130',
                                    'name' => 'Accounts Receivable (Debtors)',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '1140',
                                    'name' => 'Inventory Asset / Merchandise',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '1150',
                                    'name' => 'Employee Advance & Loans',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                            ],
                        ],
                        [
                            'code' => '1200',
                            'name' => 'Fixed Assets',
                            'type' => 'asset',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '1210',
                                    'name' => 'Office Equipment & Machinery',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => false,
                                ],
                                [
                                    'code' => '1220',
                                    'name' => 'Furniture & Fixtures',
                                    'type' => 'asset',
                                    'level' => 3,
                                    'is_system' => false,
                                ],
                            ],
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
                            'code' => '2100',
                            'name' => 'Current Liabilities',
                            'type' => 'liability',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '2110',
                                    'name' => 'Accounts Payable (Creditors / Vendors)',
                                    'type' => 'liability',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '2120',
                                    'name' => 'VAT / Tax Payable',
                                    'type' => 'liability',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '2130',
                                    'name' => 'Salary & Wages Payable',
                                    'type' => 'liability',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                            ],
                        ],
                        [
                            'code' => '2200',
                            'name' => 'Long-Term Liabilities',
                            'type' => 'liability',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '2210',
                                    'name' => 'Bank Loans',
                                    'type' => 'liability',
                                    'level' => 3,
                                    'is_system' => false,
                                ],
                            ],
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
                            'code' => '4100',
                            'name' => 'Operating Revenue',
                            'type' => 'revenue',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '4110',
                                    'name' => 'Sales Revenue',
                                    'type' => 'revenue',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '4120',
                                    'name' => 'Service / Workshop Revenue',
                                    'type' => 'revenue',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '4130',
                                    'name' => 'Project Revenue',
                                    'type' => 'revenue',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '4140',
                                    'name' => 'Delivery Charge Income',
                                    'type' => 'revenue',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                            ],
                        ],
                        [
                            'code' => '4200',
                            'name' => 'Other Income',
                            'type' => 'revenue',
                            'level' => 2,
                            'is_system' => false,
                            'children' => [
                                [
                                    'code' => '4210',
                                    'name' => 'Interest Income',
                                    'type' => 'revenue',
                                    'level' => 3,
                                    'is_system' => false,
                                ],
                            ],
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
                            'code' => '5100',
                            'name' => 'Cost of Goods Sold (COGS) / Purchase Expense',
                            'type' => 'expense',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '5110',
                                    'name' => 'Product Purchase Expense',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '5120',
                                    'name' => 'Sales Returns (Contra Revenue)',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '5130',
                                    'name' => 'Sales Discounts Given',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                            ],
                        ],
                        [
                            'code' => '5200',
                            'name' => 'Operating & Administrative Expenses',
                            'type' => 'expense',
                            'level' => 2,
                            'is_system' => true,
                            'children' => [
                                [
                                    'code' => '5210',
                                    'name' => 'Employee Salary Expense',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '5220',
                                    'name' => 'Travel & Daily Allowance (TA/DA)',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '5230',
                                    'name' => 'Office Daily Expenses',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                                [
                                    'code' => '5240',
                                    'name' => 'Project Direct Costs',
                                    'type' => 'expense',
                                    'level' => 3,
                                    'is_system' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // Recursive inserter
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

            // 3. Auto-sync existing BankDetail records under Bank Accounts (1120)
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
                            'level' => 4,
                            'is_active' => $bank->is_active,
                            'is_system' => false,
                        ]
                    );
                }
            }
        });
    }
}
