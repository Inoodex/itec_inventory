<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FiscalYearController extends Controller
{
    public function index()
    {
        $fiscalYears = FiscalYear::with('closer')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('frontend.pages.accounts.fiscal-years.index', compact('fiscalYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:50|unique:fiscal_years,year_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['is_active'] = false;
        $validated['is_closed'] = false;

        FiscalYear::create($validated);

        return redirect()->route('fiscal-years.index')->with('success', 'Fiscal Year created successfully.');
    }

    public function setActive(FiscalYear $fiscalYear)
    {
        if ($fiscalYear->is_closed) {
            return redirect()->back()->with('error', 'Closed fiscal year cannot be set as active.');
        }

        DB::transaction(function () use ($fiscalYear) {
            FiscalYear::where('id', '!=', $fiscalYear->id)->update(['is_active' => false]);
            $fiscalYear->update(['is_active' => true]);
        });

        return redirect()->route('fiscal-years.index')->with('success', "Fiscal Year [{$fiscalYear->year_name}] is now Active.");
    }

    /**
     * Year-End Closing Workflow:
     * 1. Calculate net income (Revenue - Expense) for the closing year.
     * 2. Post closing journal entry zeroing all Revenue/Expense balances into Retained Earnings (3200).
     * 3. Lock fiscal year as closed.
     */
    public function closeYear(Request $request, FiscalYear $fiscalYear)
    {
        if ($fiscalYear->is_closed) {
            return redirect()->back()->with('error', 'Fiscal Year is already closed.');
        }

        return DB::transaction(function () use ($fiscalYear) {
            $endDate = $fiscalYear->end_date->format('Y-m-d');

            // 1. Get Revenue & Expense balances
            $revAccounts = ChartOfAccount::active()->byType('revenue')->whereNotNull('parent_id')->get();
            $expAccounts = ChartOfAccount::active()->byType('expense')->whereNotNull('parent_id')->get();
            $retainedEarnings = ChartOfAccount::where('account_code', '3200')->first();

            if (!$retainedEarnings) {
                throw new \DomainException("Retained Earnings account [3200] is missing.");
            }

            $closingItems = [];
            $totalRev = 0.00;
            $totalExp = 0.00;

            // Zero out Revenues by DEBITING them
            foreach ($revAccounts as $rev) {
                $bal = $rev->calculateBalance($endDate);
                if ($bal > 0) {
                    $closingItems[] = [
                        'account_id' => $rev->id,
                        'debit' => $bal,
                        'credit' => 0.00,
                        'description' => "Year-End Closing zeroing revenue account [{$rev->account_code}]",
                    ];
                    $totalRev += $bal;
                }
            }

            // Zero out Expenses by CREDITING them
            foreach ($expAccounts as $exp) {
                $bal = $exp->calculateBalance($endDate);
                if ($bal > 0) {
                    $closingItems[] = [
                        'account_id' => $exp->id,
                        'debit' => 0.00,
                        'credit' => $bal,
                        'description' => "Year-End Closing zeroing expense account [{$exp->account_code}]",
                    ];
                    $totalExp += $bal;
                }
            }

            $netIncome = $totalRev - $totalExp;

            // Transfer net income to Retained Earnings
            if ($netIncome > 0) {
                // Net Profit -> Credit Retained Earnings
                $closingItems[] = [
                    'account_id' => $retainedEarnings->id,
                    'debit' => 0.00,
                    'credit' => $netIncome,
                    'description' => "Year-End Net Profit transferred to Retained Earnings for {$fiscalYear->year_name}",
                ];
            } elseif ($netIncome < 0) {
                // Net Loss -> Debit Retained Earnings
                $closingItems[] = [
                    'account_id' => $retainedEarnings->id,
                    'debit' => abs($netIncome),
                    'credit' => 0.00,
                    'description' => "Year-End Net Loss offset from Retained Earnings for {$fiscalYear->year_name}",
                ];
            }

            if (!empty($closingItems)) {
                postJournalEntry([
                    'entry_date' => $endDate,
                    'reference_type' => 'manual',
                    'description' => "Year-End Closing Voucher for Fiscal Year {$fiscalYear->year_name}",
                    'status' => 'approved',
                    'created_by' => Auth::id() ?? 1,
                    'items' => $closingItems,
                ]);
            }

            // Lock fiscal year
            $fiscalYear->update([
                'is_active' => false,
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => Auth::id() ?? 1,
            ]);

            return redirect()->route('fiscal-years.index')
                ->with('success', "Fiscal Year [{$fiscalYear->year_name}] closed successfully. Net income \${$netIncome} transferred to Retained Earnings.");
        });
    }
}
