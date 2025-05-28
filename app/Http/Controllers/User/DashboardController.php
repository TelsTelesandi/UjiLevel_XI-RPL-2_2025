<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EventPengajuan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $totalPengajuan = $user->events()->count();
        $approvedPengajuan = $user->events()->where('status', 'approved')->count();
        $pendingPengajuan = $user->events()->where('status', 'pending')->count();
        $closedPengajuan = $user->events()->where('status', 'closed')->count();
        
        $recentEvents = $user->events()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'totalPengajuan',
            'approvedPengajuan',
            'pendingPengajuan',
            'closedPengajuan',
            'recentEvents'
        ));
    }
}