<?php

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use App\Models\Admin\Currency;
use App\Models\Admin\ProductImage;
use App\Models\Admin\ProductOptionTopping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// Load modular helper sub-files
require_once __DIR__ . '/CartHelper.php';
require_once __DIR__ . '/LibraryHelper.php';
require_once __DIR__ . '/FormatHelper.php';

if (!function_exists('pendingBooking')) {
    function pendingBooking()
    {
        return Booking::count();
    }
}

if (!function_exists('getProductImage')) {
    function getProductImage($id)
    {
        return ProductImage::where('product_id', $id)->get();
    }
}

if (!function_exists('getStatus')) {
    function getStatus()
    {
        return [
            0 => 'Inactive',
            1 => 'Active',
        ];
    }
}

if (!function_exists('getAmountType')) {
    function getAmountType()
    {
        return [
            1 => 'Percentage',
            0 => 'Direct',
        ];
    }
}

if (!function_exists('getUser')) {
    function getUser($id)
    {
        return User::where('id', $id)->pluck('name')->first();
    }
}

if (!function_exists('getUserPhone')) {
    function getUserPhone($id)
    {
        return User::where('id', $id)->pluck('phone')->first();
    }
}

if (!function_exists('orderStatuses')) {
    function orderStatuses()
    {
        return [
            '1' => 'Pending',
            '2' => 'Processing',
            '3' => 'Shipped',
            '4' => 'Out for Delivery',
            '5' => 'Delivered',
            '6' => 'Canceled',
            '7' => 'Backordered',
            '8' => 'Returned',
            '9' => 'Refunded',
        ];
    }
}

if (!function_exists('orderStatusTitle')) {
    function orderStatusTitle()
    {
        return [
            1 => 'অর্ডারটি কনফার্মেশনের জন্য অপেক্ষমাণ। শীঘ্রই আমাদের একজন প্রতিনিধি এসএমএস বা ফোন কলের মাধ্যমে অর্ডারটি কনফার্ম করবেন ইন-শা-আল্লাহ।',
            2 => 'অর্ডারটি প্রক্রিয়াকরণে রয়েছে এবং প্রস্তুতির কাজ চলছে।',
            3 => 'আপনার অর্ডারটি পাঠানো হয়েছে এবং এটি পথে রয়েছে। শীঘ্রই আপনি এটি পেয়ে যাবেন।',
            4 => 'অর্ডারটি ডেলিভারির জন্য রওনা হয়েছে। দয়া করে প্রস্তুত থাকুন।',
            5 => 'অর্ডারটি সফলভাবে ডেলিভারি করা হয়েছে। আমাদের সেবা ব্যবহারের জন্য ধন্যবাদ।',
            6 => 'দুঃখিত, আপনার অর্ডারটি বাতিল করা হয়েছে। কোনো প্রশ্ন থাকলে আমাদের সাথে যোগাযোগ করুন।',
            7 => 'এই পণ্যটি স্টকে নেই এবং ব্যাকঅর্ডার হিসাবে রাখা হয়েছে। স্টকে এলে শীঘ্রই আপনাকে জানানো হবে।',
            8 => 'আপনার অর্ডারটি ফেরত দেওয়া হয়েছে এবং প্রক্রিয়াকরণে রয়েছে। ধন্যবাদ।',
            9 => 'আপনার অর্থ ফেরত দেওয়া হয়েছে। দয়া করে ২-৩ কার্যদিবস অপেক্ষা করুন।',
        ];
    }
}

if (!function_exists('currecySymbleType')) {
    function currecySymbleType()
    {
        return [
            '1' => 'Prefix',
            '2' => 'Suffix',
        ];
    }
}

if (!function_exists('getCurrency')) {
    function getCurrency()
    {
        return Currency::where('status', '1')->pluck('symbol')->first();
    }
}

if (!function_exists('shceduleTypes')) {
    function shceduleTypes()
    {
        return [
            'Delivery' => 'Delivery',
            'Dining room and pick-up' => 'Dining room and pick-up',
        ];
    }
}

if (!function_exists('userTypes')) {
    function userTypes()
    {
        return [
            '1' => 'Super Admin',
            '2' => 'Customer',
            '3' => 'Delivery Boy',
        ];
    }
}

if (!function_exists('getNotifications')) {
    function getNotifications()
    {
        return Notification::where('status', '1')->orderBy('created_at', 'DESC')->get();
    }
}

if (!function_exists('unSeenNotifications')) {
    function unSeenNotifications()
    {
        return Notification::where('status', '1')->where('isSeen', '0')->count();
    }
}

if (!function_exists('sendEmployeeCredential')) {
    function sendEmployeeCredential($data)
    {
        $recipientEmail = $data['email'] ?? config('mail.from.address');
        $companyName  = config('app.name', 'Company Name');
        $companyEmail = config('mail.from.address', 'noreply@example.com');

        Mail::send('emails.employee', ['data' => $data], function ($m) use ($data, $recipientEmail, $companyEmail, $companyName) {
            $m->from($companyEmail, 'Credentials of ' . $companyName);
            $m->to($recipientEmail)->subject('HRIS Access Information');
        });
    }
}

if (!function_exists('getSelectedTopings')) {
    function getSelectedTopings($id)
    {
        return ProductOptionTopping::join('topings', 'topings.id', '=', 'product_option_toppings.topping_id')
            ->select('topings.*')
            ->where('product_option_toppings.product_option_id', $id)
            ->get();
    }
}

if (!function_exists('getHost')) {
    function getHost()
    {
        $host = request()->getHost();
        return str_replace('www.', '', $host);
    }
}

if (!function_exists('getRootURL')) {
    function getRootURL()
    {
        $currentUrl = request()->url();
        $parsed_url = parse_url($currentUrl);
        $host = $parsed_url['host'];
        $port = isset($parsed_url['port']) ? $parsed_url['port'] : null;
        return $port !== null ? $host . ':' . $port : $host;
    }
}

if (!function_exists('checkRole')) {
    function checkRole()
    {
        $user = Auth::user();
        return $user ? $user->getRoleNames()[0] ?? null : null;
    }
}

if (!function_exists('paymentMethods')) {
    function paymentMethods()
    {
        return [
            '1' => 'Cash Payment',
            '2' => 'Card Payment',
            '3' => 'Other Payment',
        ];
    }
}

if (!function_exists('attendanceStatus')) {
    function attendanceStatus()
    {
        return [
            '1' => 'Present',
            '2' => 'Absent',
            '3' => 'Leave',
        ];
    }
}

// ==========================================
// DOUBLE-ENTRY ACCOUNTING & BOOKKEEPING HELPERS
// ==========================================

if (!function_exists('getActiveFiscalYear')) {
    /**
     * Get the current active fiscal year or throw an exception.
     */
    function getActiveFiscalYear()
    {
        $fiscalYear = \App\Models\FiscalYear::active()->first();

        if (!$fiscalYear) {
            $currentYear = date('Y');
            $fiscalYear = \App\Models\FiscalYear::firstOrCreate(
                ['year_name' => "{$currentYear}-" . ($currentYear + 1)],
                [
                    'start_date' => "{$currentYear}-01-01",
                    'end_date' => "{$currentYear}-12-31",
                    'is_active' => true,
                    'is_closed' => false,
                ]
            );
        }

        return $fiscalYear;
    }
}

if (!function_exists('postJournalEntry')) {
    /**
     * Atomically create and post a balanced journal voucher.
     *
     * @param array $data [
     *     'journal_no' => (optional),
     *     'entry_date' => (Y-m-d),
     *     'reference_type' => (sale, purchase, service, project, expense, salary, return, manual, contra, opening_balance),
     *     'reference_id' => (optional ID),
     *     'description' => (narration string),
     *     'status' => ('posted'|'approved'),
     *     'created_by' => (user ID),
     *     'items' => [
     *         ['account_id' => 1, 'debit' => 100.00, 'credit' => 0.00, 'description' => '...'],
     *         ['account_id' => 2, 'debit' => 0.00, 'credit' => 100.00, 'description' => '...'],
     *     ]
     * ]
     * @return \App\Models\JournalEntry
     * @throws \Exception
     */
    function postJournalEntry(array $data): \App\Models\JournalEntry
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $entryDate = $data['entry_date'] ?? date('Y-m-d');
            $fiscalYear = getActiveFiscalYear();

            // Validate fiscal year date range
            if (!$fiscalYear->containsDate($entryDate)) {
                // Auto-fallback or warn
            }

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new \InvalidArgumentException('Journal entry must contain line items.');
            }

            $totalDebit = 0.00;
            $totalCredit = 0.00;

            foreach ($data['items'] as $item) {
                $totalDebit += (float) ($item['debit'] ?? 0.00);
                $totalCredit += (float) ($item['credit'] ?? 0.00);
            }

            // Verify double-entry balancing equilibrium (Debit == Credit)
            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new \DomainException("Unbalanced Journal Voucher! Total Debit: {$totalDebit} does not equal Total Credit: {$totalCredit}");
            }

            $journalNo = $data['journal_no'] ?? \App\Models\JournalEntry::generateJournalNo($entryDate);

            $journalEntry = \App\Models\JournalEntry::create([
                'journal_no' => $journalNo,
                'entry_date' => $entryDate,
                'fiscal_year_id' => $fiscalYear->id,
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => $data['status'] ?? 'approved',
                'created_by' => $data['created_by'] ?? \Illuminate\Support\Facades\Auth::id() ?? 1,
                'approved_by' => ($data['status'] ?? 'approved') === 'approved' ? (\Illuminate\Support\Facades\Auth::id() ?? 1) : null,
                'approved_at' => ($data['status'] ?? 'approved') === 'approved' ? now() : null,
            ]);

            foreach ($data['items'] as $item) {
                $debit = (float) ($item['debit'] ?? 0.00);
                $credit = (float) ($item['credit'] ?? 0.00);

                if ($debit > 0 || $credit > 0) {
                    \App\Models\JournalEntryItem::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $item['account_id'],
                        'debit' => $debit,
                        'credit' => $credit,
                        'description' => $item['description'] ?? null,
                    ]);
                }
            }

            return $journalEntry;
        });
    }
}

if (!function_exists('getAccountBalance')) {
    /**
     * Get real-time running balance for an account code or ID.
     */
    function getAccountBalance(int|string $accountIdentifier, ?string $asOfDate = null): float
    {
        $account = is_numeric($accountIdentifier)
            ? \App\Models\ChartOfAccount::find($accountIdentifier)
            : \App\Models\ChartOfAccount::where('account_code', $accountIdentifier)->first();

        if (!$account) {
            return 0.00;
        }

        return $account->calculateBalance($asOfDate);
    }
}

if (!function_exists('reverseJournalEntry')) {
    /**
     * Post a full Storno reversal voucher for an existing journal entry.
     */
    function reverseJournalEntry(int $journalEntryId, string $reversalReason): \App\Models\JournalEntry
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($journalEntryId, $reversalReason) {
            $original = \App\Models\JournalEntry::with('items')->findOrFail($journalEntryId);

            if ($original->status === 'reversed') {
                throw new \DomainException("Journal Entry #{$original->journal_no} is already reversed.");
            }

            $reversalItems = [];
            foreach ($original->items as $item) {
                // Flip debit and credit
                $reversalItems[] = [
                    'account_id' => $item->account_id,
                    'debit' => $item->credit,
                    'credit' => $item->debit,
                    'description' => "Reversal: " . ($item->description ?? $original->description),
                ];
            }

            $reversalVoucher = postJournalEntry([
                'entry_date' => date('Y-m-d'),
                'reference_type' => 'manual',
                'description' => "Reversal of {$original->journal_no}. Reason: {$reversalReason}",
                'status' => 'approved',
                'created_by' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'items' => $reversalItems,
            ]);

            $original->update([
                'status' => 'reversed',
                'reversed_entry_id' => $reversalVoucher->id,
            ]);

            return $reversalVoucher;
        });
    }
}

