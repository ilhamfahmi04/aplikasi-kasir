<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $userId = $request->query('user_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaction::with(['user', 'details']);

        if ($search) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $cashiers = User::where('role', User::ROLE_KASIR)->orWhere('role', User::ROLE_ADMIN)->orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'cashiers', 'search', 'userId', 'startDate', 'endDate'));
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['user', 'details.product']);
        return view('admin.transactions.show', compact('transaction'));
    }
}

