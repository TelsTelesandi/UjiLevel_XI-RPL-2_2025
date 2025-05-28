<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventPengajuan;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('admin.dashboard');
    }
}

class DashboardController extends Controller
{
    public function index()
    {
        $totalPengajuan = EventPengajuan::count();
        $approvedPengajuan = EventPengajuan::where('status', 'approved')->count();
        $rejectedPengajuan = EventPengajuan::where('status', 'rejected')->count();
        $closedPengajuan = EventPengajuan::where('status', 'closed')->count();
        
        $recentEvents = EventPengajuan::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengajuan',
            'approvedPengajuan',
            'rejectedPengajuan',
            'closedPengajuan',
            'recentEvents'
        ));
    }
}