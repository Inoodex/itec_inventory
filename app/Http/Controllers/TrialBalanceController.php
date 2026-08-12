<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        $accounts = ChartOfAccount::active()
            ->orderBy('account_code')
            ->get();

        $rows = [];
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($accounts as $account) {
            $balance = $account->calculateBalance($asOfDate);

            if (abs($balance) > 0.001) {
                if ($account->isDebitNormal()) {
                    $debit = $balance >= 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                } else {
                    $debit = $balance < 0 ? abs($balance) : 0;
                    $credit = $balance >= 0 ? $balance : 0;
                }

                $rows[] = [
                    'account' => $account,
                    'debit' => $debit,
                    'credit' => $credit,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }
        }

        $isBalanced = abs($totalDebit - $totalCredit) < 0.01;

        return view('frontend.pages.accounts.reports.trial-balance', compact(
            'rows',
            'totalDebit',
            'totalCredit',
            'isBalanced',
            'asOfDate'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $asOfDate = $request->query('as_of_date', date('Y-m-d'));

        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();

        $rows = [];
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($accounts as $account) {
            $balance = $account->calculateBalance($asOfDate);

            if (abs($balance) > 0.001) {
                if ($account->isDebitNormal()) {
                    $debit = $balance >= 0 ? $balance : 0;
                    $credit = $balance < 0 ? abs($balance) : 0;
                } else {
                    $debit = $balance < 0 ? abs($balance) : 0;
                    $credit = $balance >= 0 ? $balance : 0;
                }

                $rows[] = [
                    'account' => $account,
                    'debit' => $debit,
                    'credit' => $credit,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }
        }

        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';

        $html = view('pdf.accounts.trial-balance', compact(
            'rows',
            'totalDebit',
            'totalCredit',
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

        return response($mpdf->Output("Trial-Balance-{$asOfDate}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
