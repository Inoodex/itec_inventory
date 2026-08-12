<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');
        $refType = $request->query('reference_type');

        $query = JournalEntry::with(['items.account', 'creator', 'approver'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($refType) {
            $query->where('reference_type', $refType);
        }

        if ($from && $to) {
            $query->whereBetween('entry_date', [$from, $to]);
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('frontend.pages.accounts.journal-entries.index', compact('entries', 'status', 'from', 'to', 'refType'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::active()
            ->orderBy('account_code')
            ->get();

        $journalNo = JournalEntry::generateJournalNo();
        $today = date('Y-m-d');

        return view('frontend.pages.accounts.journal-entries.create', compact('accounts', 'journalNo', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference_type' => 'required|string',
            'description' => 'required|string|max:1000',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string|max:500',
        ]);

        try {
            $journalEntry = postJournalEntry([
                'entry_date' => $validated['entry_date'],
                'reference_type' => $validated['reference_type'],
                'description' => $validated['description'],
                'status' => 'approved',
                'created_by' => Auth::id() ?? 1,
                'items' => $validated['items'],
            ]);

            return redirect()->route('journal-entries.show', $journalEntry->id)
                ->with('success', "Journal Entry [{$journalEntry->journal_no}] posted successfully!");
        } catch (\DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error posting journal entry: ' . $e->getMessage());
        }
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['items.account', 'creator', 'approver', 'reversedEntry']);
        return view('frontend.pages.accounts.journal-entries.show', compact('journalEntry'));
    }

    public function reverse(Request $request, JournalEntry $journalEntry)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $reversalVoucher = reverseJournalEntry($journalEntry->id, $request->reason);

            return redirect()->route('journal-entries.show', $reversalVoucher->id)
                ->with('success', "Original voucher reversed. Reversal voucher [{$reversalVoucher->journal_no}] created.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Reversal failed: ' . $e->getMessage());
        }
    }

    public function downloadPdf(JournalEntry $journalEntry)
    {
        $journalEntry->load(['items.account', 'creator', 'approver']);

        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';

        $html = view('pdf.accounts.voucher', compact('journalEntry', 'padBase64'))->render();

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

        return response($mpdf->Output("Voucher-{$journalEntry->journal_no}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
