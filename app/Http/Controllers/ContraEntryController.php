<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ContraEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContraEntryController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = ContraEntry::with(['fromAccount', 'toAccount', 'journalEntry', 'creator'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        $contraEntries = $query->paginate(15)->withQueryString();

        return view('frontend.pages.accounts.contra-entries.index', compact('contraEntries', 'from', 'to'));
    }

    public function create()
    {
        // Liquid accounts (Cash in hand 1110 and Bank Accounts 1120 / sub-accounts)
        $liquidAccounts = ChartOfAccount::active()
            ->where(function ($q) {
                $q->whereIn('account_code', ['1110', '1120'])
                  ->orWhere('parent_id', function ($sub) {
                      $sub->select('id')->from('chart_of_accounts')->where('account_code', '1120')->limit(1);
                  });
            })
            ->orderBy('account_code')
            ->get();

        $contraNo = ContraEntry::generateContraNo();
        $today = date('Y-m-d');

        return view('frontend.pages.accounts.contra-entries.create', compact('liquidAccounts', 'contraNo', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'from_account_id' => 'required|exists:chart_of_accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $contraNo = ContraEntry::generateContraNo($validated['date']);

            // 1. Post double-entry journal voucher for the contra transfer
            // Debit: Destination Account (Receiving Funds)
            // Credit: Source Account (Sending Funds)
            $voucher = postJournalEntry([
                'entry_date' => $validated['date'],
                'reference_type' => 'contra',
                'description' => "Contra Transfer [{$contraNo}]: " . $validated['description'],
                'status' => 'approved',
                'created_by' => Auth::id() ?? 1,
                'items' => [
                    [
                        'account_id' => $validated['to_account_id'],
                        'debit' => $validated['amount'],
                        'credit' => 0.00,
                        'description' => "Transfer received from Account #{$validated['from_account_id']}",
                    ],
                    [
                        'account_id' => $validated['from_account_id'],
                        'debit' => 0.00,
                        'credit' => $validated['amount'],
                        'description' => "Transfer sent to Account #{$validated['to_account_id']}",
                    ],
                ],
            ]);

            // 2. Create Contra Entry Record
            ContraEntry::create([
                'contra_no' => $contraNo,
                'from_account_id' => $validated['from_account_id'],
                'to_account_id' => $validated['to_account_id'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'description' => $validated['description'],
                'journal_entry_id' => $voucher->id,
                'created_by' => Auth::id() ?? 1,
            ]);

            return redirect()->route('contra-entries.index')
                ->with('success', "Contra Entry [{$contraNo}] transferred \${$validated['amount']} successfully!");
        });
    }
}
