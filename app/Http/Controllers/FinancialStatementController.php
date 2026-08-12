<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FinancialStatementController extends Controller
{
    /**
     * Profit and Loss Statement (Income Statement)
     */
    public function profitLoss(Request $request)
    {
        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        // Revenues (Class 4000)
        $revenueAccounts = ChartOfAccount::active()
            ->byType('revenue')
            ->whereNotNull('parent_id')
            ->orderBy('account_code')
            ->get();

        $revenueData = [];
        $totalRevenue = 0.00;

        foreach ($revenueAccounts as $account) {
            $amount = $account->calculateBalance($toDate);
            if (abs($amount) > 0.001) {
                $revenueData[] = [
                    'account' => $account,
                    'amount' => $amount,
                ];
                $totalRevenue += $amount;
            }
        }

        // Expenses (Class 5000)
        $expenseAccounts = ChartOfAccount::active()
            ->byType('expense')
            ->whereNotNull('parent_id')
            ->orderBy('account_code')
            ->get();

        $expenseData = [];
        $totalExpense = 0.00;

        foreach ($expenseAccounts as $account) {
            $amount = $account->calculateBalance($toDate);
            if (abs($amount) > 0.001) {
                $expenseData[] = [
                    'account' => $account,
                    'amount' => $amount,
                ];
                $totalExpense += $amount;
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('frontend.pages.accounts.reports.profit-loss', compact(
            'revenueData',
            'expenseData',
            'totalRevenue',
            'totalExpense',
            'netProfit',
            'fromDate',
            'toDate'
        ));
    }

    public function profitLossPdf(Request $request)
    {
        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        $revenueAccounts = ChartOfAccount::active()->byType('revenue')->whereNotNull('parent_id')->get();
        $revenueData = [];
        $totalRevenue = 0.00;
        foreach ($revenueAccounts as $account) {
            $amount = $account->calculateBalance($toDate);
            if (abs($amount) > 0.001) {
                $revenueData[] = ['account' => $account, 'amount' => $amount];
                $totalRevenue += $amount;
            }
        }

        $expenseAccounts = ChartOfAccount::active()->byType('expense')->whereNotNull('parent_id')->get();
        $expenseData = [];
        $totalExpense = 0.00;
        foreach ($expenseAccounts as $account) {
            $amount = $account->calculateBalance($toDate);
            if (abs($amount) > 0.001) {
                $expenseData[] = ['account' => $account, 'amount' => $amount];
                $totalExpense += $amount;
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';

        $html = view('pdf.accounts.profit-loss', compact(
            'revenueData',
            'expenseData',
            'totalRevenue',
            'totalExpense',
            'netProfit',
            'fromDate',
            'toDate',
            'padBase64'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
            'margin_top' => 45,
            'margin_bottom' => 25,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output("Profit-Loss-{$toDate}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Balance Sheet (Statement of Financial Position)
     */
    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        // Assets (1000)
        $assetAccounts = ChartOfAccount::active()->byType('asset')->whereNotNull('parent_id')->get();
        $assetData = [];
        $totalAssets = 0.00;
        foreach ($assetAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $assetData[] = ['account' => $account, 'amount' => $amount];
                $totalAssets += $amount;
            }
        }

        // Liabilities (2000)
        $liabilityAccounts = ChartOfAccount::active()->byType('liability')->whereNotNull('parent_id')->get();
        $liabilityData = [];
        $totalLiabilities = 0.00;
        foreach ($liabilityAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $liabilityData[] = ['account' => $account, 'amount' => $amount];
                $totalLiabilities += $amount;
            }
        }

        // Equity (3000)
        $equityAccounts = ChartOfAccount::active()->byType('equity')->whereNotNull('parent_id')->get();
        $equityData = [];
        $totalEquity = 0.00;
        foreach ($equityAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $equityData[] = ['account' => $account, 'amount' => $amount];
                $totalEquity += $amount;
            }
        }

        // Net Earnings from Revenue - Expense for period
        $revTotal = 0.00;
        foreach (ChartOfAccount::active()->byType('revenue')->whereNotNull('parent_id')->get() as $a) {
            $revTotal += $a->calculateBalance($asOfDate);
        }
        $expTotal = 0.00;
        foreach (ChartOfAccount::active()->byType('expense')->whereNotNull('parent_id')->get() as $a) {
            $expTotal += $a->calculateBalance($asOfDate);
        }
        $currentEarnings = $revTotal - $expTotal;

        $totalEquityWithEarnings = $totalEquity + $currentEarnings;
        $totalLiabAndEquity = $totalLiabilities + $totalEquityWithEarnings;
        $isBalanced = abs($totalAssets - $totalLiabAndEquity) < 0.01;

        return view('frontend.pages.accounts.reports.balance-sheet', compact(
            'assetData',
            'liabilityData',
            'equityData',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'currentEarnings',
            'totalEquityWithEarnings',
            'totalLiabAndEquity',
            'isBalanced',
            'asOfDate'
        ));
    }

    public function balanceSheetPdf(Request $request)
    {
        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        $assetAccounts = ChartOfAccount::active()->byType('asset')->whereNotNull('parent_id')->get();
        $assetData = [];
        $totalAssets = 0.00;
        foreach ($assetAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $assetData[] = ['account' => $account, 'amount' => $amount];
                $totalAssets += $amount;
            }
        }

        $liabilityAccounts = ChartOfAccount::active()->byType('liability')->whereNotNull('parent_id')->get();
        $liabilityData = [];
        $totalLiabilities = 0.00;
        foreach ($liabilityAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $liabilityData[] = ['account' => $account, 'amount' => $amount];
                $totalLiabilities += $amount;
            }
        }

        $equityAccounts = ChartOfAccount::active()->byType('equity')->whereNotNull('parent_id')->get();
        $equityData = [];
        $totalEquity = 0.00;
        foreach ($equityAccounts as $account) {
            $amount = $account->calculateBalance($asOfDate);
            if (abs($amount) > 0.001) {
                $equityData[] = ['account' => $account, 'amount' => $amount];
                $totalEquity += $amount;
            }
        }

        $revTotal = 0.00;
        foreach (ChartOfAccount::active()->byType('revenue')->whereNotNull('parent_id')->get() as $a) {
            $revTotal += $a->calculateBalance($asOfDate);
        }
        $expTotal = 0.00;
        foreach (ChartOfAccount::active()->byType('expense')->whereNotNull('parent_id')->get() as $a) {
            $expTotal += $a->calculateBalance($asOfDate);
        }
        $currentEarnings = $revTotal - $expTotal;
        $totalEquityWithEarnings = $totalEquity + $currentEarnings;
        $totalLiabAndEquity = $totalLiabilities + $totalEquityWithEarnings;

        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';

        $html = view('pdf.accounts.balance-sheet', compact(
            'assetData',
            'liabilityData',
            'equityData',
            'totalAssets',
            'totalLiabilities',
            'totalEquityWithEarnings',
            'currentEarnings',
            'totalLiabAndEquity',
            'asOfDate',
            'padBase64'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
            'margin_top' => 45,
            'margin_bottom' => 25,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output("Balance-Sheet-{$asOfDate}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Cash Flow Statement (Direct Method)
     */
    public function cashFlow(Request $request)
    {
        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        // Liquid accounts (Cash in hand + Bank accounts)
        $liquidAccounts = ChartOfAccount::whereIn('account_code', ['1110', '1120'])
            ->orWhere('parent_id', function ($q) {
                $q->select('id')->from('chart_of_accounts')->where('account_code', '1120')->limit(1);
            })
            ->pluck('id');

        $openingCash = 0.00;
        foreach ($liquidAccounts as $accId) {
            $openingCash += getAccountBalance($accId, date('Y-m-d', strtotime($fromDate . ' -1 day')));
        }

        // Operating Inflows
        $inflows = \App\Models\JournalEntryItem::whereIn('account_id', $liquidAccounts)
            ->where('debit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                $q->whereIn('status', ['posted', 'approved'])
                  ->whereBetween('entry_date', [$fromDate, $toDate])
                  ->where('reference_type', '!=', 'contra');
            })
            ->sum('debit');

        // Operating Outflows
        $outflows = \App\Models\JournalEntryItem::whereIn('account_id', $liquidAccounts)
            ->where('credit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                $q->whereIn('status', ['posted', 'approved'])
                  ->whereBetween('entry_date', [$fromDate, $toDate])
                  ->where('reference_type', '!=', 'contra');
            })
            ->sum('credit');

        $netCashFlow = $inflows - $outflows;
        $closingCash = $openingCash + $netCashFlow;

        return view('frontend.pages.accounts.reports.cash-flow', compact(
            'openingCash',
            'inflows',
            'outflows',
            'netCashFlow',
            'closingCash',
            'fromDate',
            'toDate'
        ));
    }
}
