<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $today = Carbon::today()->toDateString();
        $in7Days = Carbon::today()->addDays(7)->toDateString();
        $in30Days = Carbon::today()->addDays(30)->toDateString();

        $query = Product::with('category')->whereNotNull('expired_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status === 'expired') {
            $query->where('expired_date', '<', $today);
        } elseif ($status === 'critical_7') {
            $query->whereBetween('expired_date', [$today, $in7Days]);
        } elseif ($status === 'warning_30') {
            $query->whereBetween('expired_date', [$today, $in30Days]);
        }

        $products = $query->orderBy('expired_date', 'asc')->paginate(12)->withQueryString();

        // Ringkasan angka
        $expiredCount = Product::whereNotNull('expired_date')->where('expired_date', '<', $today)->count();
        $critical7Count = Product::whereNotNull('expired_date')->whereBetween('expired_date', [$today, $in7Days])->count();
        $warning30Count = Product::whereNotNull('expired_date')->whereBetween('expired_date', [$today, $in30Days])->count();

        return view('admin.expiry.index', compact('products', 'status', 'search', 'expiredCount', 'critical7Count', 'warning30Count'));
    }
}

