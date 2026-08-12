<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $query = ChartOfAccount::with(['parent', 'children', 'bankDetail']);

        if ($type) {
            $query->where('account_type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('account_code')->get();

        // Count summaries by type
        $counts = [
            'total' => ChartOfAccount::count(),
            'asset' => ChartOfAccount::where('account_type', 'asset')->count(),
            'liability' => ChartOfAccount::where('account_type', 'liability')->count(),
            'equity' => ChartOfAccount::where('account_type', 'equity')->count(),
            'revenue' => ChartOfAccount::where('account_type', 'revenue')->count(),
            'expense' => ChartOfAccount::where('account_type', 'expense')->count(),
        ];

        return view('frontend.pages.accounts.chart-of-accounts.index', compact('accounts', 'counts', 'type', 'search'));
    }

    public function create()
    {
        $parentAccounts = ChartOfAccount::whereNull('parent_id')
            ->orWhere('level', '<', 3)
            ->orderBy('account_code')
            ->get();

        $bankDetails = BankDetail::whereDoesntHave('chartOfAccount')->get();

        return view('frontend.pages.accounts.chart-of-accounts.create', compact('parentAccounts', 'bankDetails'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:50|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'bank_detail_id' => 'nullable|exists:bank_details,id',
            'opening_balance' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $level = 1;
        if (!empty($validated['parent_id'])) {
            $parent = ChartOfAccount::find($validated['parent_id']);
            if ($parent) {
                $level = $parent->level + 1;
                // Force child to inherit parent's account_type
                $validated['account_type'] = $parent->account_type;
            }
        }

        $validated['level'] = $level;
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;
        $validated['is_system'] = false;
        $validated['is_active'] = true;

        ChartOfAccount::create($validated);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Chart of Account created successfully.');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        $parentAccounts = ChartOfAccount::where('id', '!=', $chartOfAccount->id)
            ->orderBy('account_code')
            ->get();

        return view('frontend.pages.accounts.chart-of-accounts.edit', compact('chartOfAccount', 'parentAccounts'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'opening_balance' => 'nullable|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($chartOfAccount->is_system && isset($validated['parent_id']) && $validated['parent_id'] != $chartOfAccount->parent_id) {
            return redirect()->back()->with('error', 'System root account hierarchy cannot be modified.');
        }

        $chartOfAccount->update($validated);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->is_system) {
            return redirect()->back()->with('error', 'System accounts are protected and cannot be deleted.');
        }

        if ($chartOfAccount->journalItems()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete account with existing transactions.');
        }

        if ($chartOfAccount->children()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete parent account with sub-accounts.');
        }

        $chartOfAccount->delete();

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted successfully.');
    }
}
