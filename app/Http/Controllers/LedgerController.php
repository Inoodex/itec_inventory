<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accountId = $request->query('account_id');
        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        $accounts = ChartOfAccount::active()
            ->orderBy('account_code')
            ->get();

        $selectedAccount = null;
        $ledgerItems = collect();
        $openingBalance = 0.00;
        $closingBalance = 0.00;

        if ($accountId) {
            $selectedAccount = ChartOfAccount::findOrFail($accountId);

            // Calculate opening balance before fromDate
            $openingBalance = $selectedAccount->calculateBalance(date('Y-m-d', strtotime($fromDate . ' -1 day')));

            // Get ledger items within the period
            $ledgerItems = JournalEntryItem::with(['journalEntry', 'account'])
                ->where('account_id', $accountId)
                ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                    $q->whereIn('status', ['posted', 'approved'])
                      ->whereBetween('entry_date', [$fromDate, $toDate]);
                })
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
                ->orderBy('journal_entries.entry_date', 'asc')
                ->orderBy('journal_entries.id', 'asc')
                ->select('journal_entry_items.*')
                ->get();

            // Calculate ending balance
            $closingBalance = $selectedAccount->calculateBalance($toDate);
        }

        return view('frontend.pages.accounts.ledger.index', compact(
            'accounts',
            'selectedAccount',
            'ledgerItems',
            'openingBalance',
            'closingBalance',
            'accountId',
            'fromDate',
            'toDate'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $accountId = $request->query('account_id');
        $fromDate = $request->query('from_date', date('Y-01-01'));
        $toDate = $request->query('to_date', date('Y-m-d'));

        $selectedAccount = ChartOfAccount::findOrFail($accountId);
        $openingBalance = $selectedAccount->calculateBalance(date('Y-m-d', strtotime($fromDate . ' -1 day')));

        $ledgerItems = JournalEntryItem::with(['journalEntry', 'account'])
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                $q->whereIn('status', ['posted', 'approved'])
                  ->whereBetween('entry_date', [$fromDate, $toDate]);
            })
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->select('journal_entry_items.*')
            ->get();

        $closingBalance = $selectedAccount->calculateBalance($toDate);

        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';

        $html = view('pdf.accounts.ledger', compact(
            'selectedAccount',
            'ledgerItems',
            'openingBalance',
            'closingBalance',
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

        return response($mpdf->Output("Ledger-{$selectedAccount->account_code}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
