<?php

namespace App\Http\Controllers;

use App\Models\AccountReconciliation;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $accountId = $request->query('account_id');
        $asOfDate = $request->query('date', date('Y-m-d'));

        // Liquid accounts (Cash and Banks)
        $bankAccounts = ChartOfAccount::active()
            ->where(function ($q) {
                $q->whereIn('account_code', ['1110', '1120'])
                  ->orWhere('parent_id', function ($sub) {
                      $sub->select('id')->from('chart_of_accounts')->where('account_code', '1120')->limit(1);
                  });
            })
            ->orderBy('account_code')
            ->get();

        $selectedAccount = null;
        $bookBalance = 0.00;

        if ($accountId) {
            $selectedAccount = ChartOfAccount::findOrFail($accountId);
            $bookBalance = $selectedAccount->calculateBalance($asOfDate);
        }

        $reconciliations = AccountReconciliation::with(['account', 'creator'])
            ->orderBy('bank_statement_date', 'desc')
            ->paginate(10);

        return view('frontend.pages.accounts.reconciliation.index', compact(
            'bankAccounts',
            'selectedAccount',
            'bookBalance',
            'reconciliations',
            'accountId',
            'asOfDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'bank_statement_date' => 'required|date',
            'statement_balance' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        $account = ChartOfAccount::findOrFail($validated['account_id']);
        $bookBalance = $account->calculateBalance($validated['bank_statement_date']);
        $statementBalance = (float) $validated['statement_balance'];
        $difference = $statementBalance - $bookBalance;

        AccountReconciliation::create([
            'account_id' => $validated['account_id'],
            'bank_statement_date' => $validated['bank_statement_date'],
            'statement_balance' => $statementBalance,
            'book_balance' => $bookBalance,
            'difference' => $difference,
            'status' => abs($difference) < 0.01 ? 'completed' : 'draft',
            'notes' => $validated['notes'],
            'created_by' => Auth::id() ?? 1,
        ]);

        return redirect()->route('reconciliation.index', [
            'account_id' => $validated['account_id'],
            'date' => $validated['bank_statement_date']
        ])->with('success', 'Reconciliation recorded successfully.');
    }
}
