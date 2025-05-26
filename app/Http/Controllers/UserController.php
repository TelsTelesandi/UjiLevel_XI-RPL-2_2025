<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventPengajuan;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan dashboard user
     */
    public function dashboard()
    {
        $userId = Auth::user()->user_id;
        
        // Hitung jumlah event user
        $totalEvents = EventPengajuan::where('user_id', $userId)->count();
        $pendingEvents = EventPengajuan::where('user_id', $userId)
            ->where('status', 'Menunggu')
            ->count();
        $approvedEvents = EventPengajuan::where('user_id', $userId)
            ->where('status', 'Disetujui')
            ->count();
            
        // Ambil event terbaru milik user
        $recentEvents = EventPengajuan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('user.dashboard', [
            'totalEvents' => $totalEvents,
            'pendingEvents' => $pendingEvents,
            'approvedEvents' => $approvedEvents,
            'recentEvents' => $recentEvents
        ]);
    }
} 