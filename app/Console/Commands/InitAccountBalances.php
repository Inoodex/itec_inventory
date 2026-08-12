<?php

namespace App\Console\Commands;

use App\Models\ChartOfAccount;
use App\Models\Inventory;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:init-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap and calculate initial opening balances for AR, Inventory, and AP from existing operational tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Opening Balances Calculation...');

        DB::transaction(function () {
            // 1. Calculate live Accounts Receivable (Uncollected Sale Dues)
            $totalAR = (float) Sale::where('status', '!=', 'cancelled')->sum('due_payment');

            // 2. Calculate Inventory Asset Valuation (Stock * Unit Purchase Price)
            $totalInventory = (float) (Inventory::join('purchases', 'purchases.product_id', '=', 'inventories.product_id')
                ->selectRaw('SUM(inventories.current_stock * purchases.unit_price) as valuation')
                ->value('valuation') ?? 0.00);

            // 3. Calculate Accounts Payable (Unpaid Purchase Dues)
            $totalAP = (float) Purchase::sum('due');

            // 4. Net Balancing difference -> Owner Capital (Equity)
            $totalAssets = $totalAR + $totalInventory;
            $netEquity = $totalAssets - $totalAP;

            $this->table(
                ['Account Classification', 'Account Code & Title', 'Calculated Amount ($)'],
                [
                    ['Asset', '[1130] Accounts Receivable (Debtors)', number_format($totalAR, 2)],
                    ['Asset', '[1140] Inventory Asset / Stock Valuation', number_format($totalInventory, 2)],
                    ['Liability', '[2110] Accounts Payable (Creditors)', number_format($totalAP, 2)],
                    ['Equity (Balancing)', '[3100] Owner Capital', number_format($netEquity, 2)],
                ]
            );

            // Fetch Chart of Accounts
            $arAccount = ChartOfAccount::where('account_code', '1130')->first();
            $invAccount = ChartOfAccount::where('account_code', '1140')->first();
            $apAccount = ChartOfAccount::where('account_code', '2110')->first();
            $eqAccount = ChartOfAccount::where('account_code', '3100')->first();

            if (!$arAccount || !$invAccount || !$apAccount || !$eqAccount) {
                $this->error('Standard Chart of Accounts not found. Please run `php artisan db:seed --class=ChartOfAccountSeeder` first.');
                return;
            }

            // Update opening balance attributes on master accounts
            $arAccount->update(['opening_balance' => $totalAR]);
            $invAccount->update(['opening_balance' => $totalInventory]);
            $apAccount->update(['opening_balance' => $totalAP]);
            $eqAccount->update(['opening_balance' => $netEquity]);

            // Create balanced Initial Opening Voucher
            $today = date('Y-m-d');
            $journalNo = JournalEntry::generateJournalNo($today);

            $voucherItems = [];

            if ($totalAR > 0) {
                $voucherItems[] = [
                    'account_id' => $arAccount->id,
                    'debit' => $totalAR,
                    'credit' => 0.00,
                    'description' => 'Opening balance brought forward from uncollected sales receivables',
                ];
            }

            if ($totalInventory > 0) {
                $voucherItems[] = [
                    'account_id' => $invAccount->id,
                    'debit' => $totalInventory,
                    'credit' => 0.00,
                    'description' => 'Opening inventory asset valuation based on current warehouse stock',
                ];
            }

            if ($totalAP > 0) {
                $voucherItems[] = [
                    'account_id' => $apAccount->id,
                    'debit' => 0.00,
                    'credit' => $totalAP,
                    'description' => 'Opening balance brought forward from unpaid vendor payables',
                ];
            }

            if ($netEquity > 0) {
                $voucherItems[] = [
                    'account_id' => $eqAccount->id,
                    'debit' => 0.00,
                    'credit' => $netEquity,
                    'description' => 'Initial Owner Capital balancing net asset position',
                ];
            }

            // Post balanced journal entry
            postJournalEntry([
                'journal_no' => $journalNo,
                'entry_date' => $today,
                'reference_type' => 'opening_balance',
                'description' => 'System Bootstrapped Opening Balances for AR, Stock Valuation, AP, and Owner Equity',
                'status' => 'approved',
                'created_by' => 1,
                'items' => $voucherItems,
            ]);

            $this->info("Opening balance journal voucher [{$journalNo}] created and balanced successfully!");
        });
    }
}
