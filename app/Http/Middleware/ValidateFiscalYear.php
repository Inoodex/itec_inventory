<?php

namespace App\Http\Middleware;

use App\Models\FiscalYear;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateFiscalYear
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $date = $request->input('entry_date', $request->input('date', date('Y-m-d')));

        $activeYear = FiscalYear::active()->first();

        if (!$activeYear) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No active fiscal year found in the system.'], 422);
            }
            return redirect()->back()->with('error', 'No active fiscal year found. Please configure a fiscal year in Accounts -> Fiscal Years.');
        }

        if ($activeYear->is_closed) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Active fiscal year is closed. Transactions cannot be posted.'], 422);
            }
            return redirect()->back()->with('error', 'The active fiscal year is closed. Transactions are locked.');
        }

        return $next($request);
    }
}
