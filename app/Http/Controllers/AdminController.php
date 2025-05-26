<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EventPengajuan;
use App\Models\VerifikasiEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin
     */
    public function dashboard()
    {
        // Hitung statistik
        $totalUsers = User::where('role', 'user')->count();
        $totalEvents = EventPengajuan::count();
        $pendingEvents = EventPengajuan::where('status', 'Menunggu')->count();
        $approvedEvents = EventPengajuan::where('status', 'Disetujui')->count();
        
        // Ambil event terbaru
        $recentEvents = EventPengajuan::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalEvents' => $totalEvents,
            'pendingEvents' => $pendingEvents,
            'approvedEvents' => $approvedEvents,
            'recentEvents' => $recentEvents
        ]);
    }
    
    /**
     * Menampilkan daftar users
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users', compact('users'));
    }
    
    /**
     * Menyimpan user baru
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'max:100'],
            'role' => ['required', 'string', 'in:admin,user'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'ekskul' => ['required', 'string', 'max:100'],
        ]);
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        $user = User::create($validated);
        
        return redirect()->route('admin.users')->with('success', 'User berhasil dibuat!');
    }
    
    /**
     * Mengupdate user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->user_id . ',user_id'],
            'role' => ['required', 'string', 'in:admin,user'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'ekskul' => ['required', 'string', 'max:100'],
        ]);
        
        // Update password jika diisi
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }
        
        $user->update($validated);
        
        return redirect()->route('admin.users')->with('success', 'User berhasil diupdate!');
    }
    
    /**
     * Menghapus user
     */
    public function deleteUser(User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }
    
    /**
     * Menampilkan daftar events untuk admin
     */
    public function events()
    {
        $events = EventPengajuan::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Ambil daftar ekskul unik untuk filter
        $uniqueEkskul = User::where('role', 'user')
            ->where('ekskul', '!=', '-')
            ->distinct()
            ->pluck('ekskul')
            ->toArray();
            
        return view('admin.events', compact('events', 'uniqueEkskul'));
    }
    
    /**
     * Menampilkan detail event dalam format JSON untuk modal
     */
    public function eventDetail(EventPengajuan $event)
    {
        $event->load(['user', 'verifikasi', 'verifikasi.admin']);
        
        // Format data untuk frontend
        $event->tanggal_pengajuan_formatted = Carbon::parse($event->tanggal_pengajuan)->format('d M Y');
        $event->total_pembiayaan_formatted = number_format($event->total_pembiayaan, 0, ',', '.');
        
        return response()->json($event);
    }
    
    /**
     * Menyetujui event
     */
    public function approveEvent(EventPengajuan $event)
    {
        $event->update(['status' => 'Disetujui']);
        
        // Buat atau update verifikasi
        VerifikasiEvent::updateOrCreate(
            ['event_id' => $event->event_id],
            [
                'admin_id' => Auth::user()->user_id,
                'tanggal_verifikasi' => now()->format('Y-m-d'),
                'status' => 'unclosed'
            ]
        );
        
        return redirect()->route('admin.events')->with('success', 'Event berhasil disetujui!');
    }
    
    /**
     * Menolak event
     */
    public function rejectEvent(EventPengajuan $event, Request $request)
    {
        $request->validate([
            'catatan_admin' => 'required|string'
        ]);
        
        $event->update(['status' => 'Ditolak']);
        
        // Buat atau update verifikasi
        VerifikasiEvent::updateOrCreate(
            ['event_id' => $event->event_id],
            [
                'admin_id' => Auth::user()->user_id,
                'tanggal_verifikasi' => now()->format('Y-m-d'),
                'catatan_admin' => $request->catatan_admin,
                'status' => 'Closed'
            ]
        );
        
        return redirect()->route('admin.events')->with('success', 'Event berhasil ditolak!');
    }
    
    /**
     * Menampilkan halaman laporan
     */
    public function reports()
    {
        $events = EventPengajuan::with(['user', 'verifikasi', 'verifikasi.admin'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Ambil daftar ekskul unik untuk filter
        $uniqueEkskul = User::where('role', 'user')
            ->where('ekskul', '!=', '-')
            ->distinct()
            ->pluck('ekskul')
            ->toArray();
            
        return view('admin.reports', compact('events', 'uniqueEkskul'));
    }
    
    /**
     * Mengexport laporan
     */
    public function exportReport(Request $request)
    {
        // Query dasar
        $query = EventPengajuan::with(['user', 'verifikasi', 'verifikasi.admin'])
            ->orderBy('created_at', 'desc');
            
        // Filter berdasarkan status jika ada
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('period')) {
            $endDate = Carbon::now();
            
            if ($request->period == '7') {
                $startDate = Carbon::now()->subDays(7);
            } elseif ($request->period == '30') {
                $startDate = Carbon::now()->subDays(30);
            } elseif ($request->period == '90') {
                $startDate = Carbon::now()->subDays(90);
            } else {
                // Jika custom period
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
            }
            
            $query->whereBetween('tanggal_pengajuan', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        } else {
            // Default 30 hari terakhir
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(30);
        }
        
        // Filter berdasarkan ekskul jika ada
        if ($request->filled('ekskul') && $request->ekskul != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('ekskul', $request->ekskul);
            });
        }
        
        // Eksekusi query
        $events = $query->get();
        
        try {
            // Generate PDF
            $pdf = Pdf::loadView('pdf.report', [
                'events' => $events,
                'startDate' => Carbon::parse($startDate)->format('d M Y'),
                'endDate' => Carbon::parse($endDate)->format('d M Y'),
                'filters' => [
                    'status' => $request->status ?? 'all',
                    'period' => $request->period ?? 'all',
                    'ekskul' => $request->ekskul ?? 'all'
                ]
            ]);
            
            // Set paper size dan orientasi
            $pdf->setPaper('a4', 'landscape');
            
            // Download PDF dengan nama yang dinamis
            return $pdf->download('laporan-event-' . Carbon::now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            // Tangkap error jika terjadi masalah saat generate PDF
            return redirect()->route('admin.reports')
                ->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Menambahkan komentar pada event
     */
    public function addComment(EventPengajuan $event, Request $request)
    {
        $request->validate([
            'komentar' => 'required|string'
        ]);
        
        // Cari atau buat verifikasi event
        $verifikasi = VerifikasiEvent::firstOrCreate(
            ['event_id' => $event->event_id],
            [
                'admin_id' => Auth::user()->user_id,
                'status' => 'unclosed'
            ]
        );
        
        // Update komentar
        $verifikasi->update([
            'komentar' => $request->komentar,
            'komentar_at' => Carbon::now()
        ]);
        
        return redirect()->route('admin.events')->with('success', 'Komentar berhasil ditambahkan!');
    }
    
    /**
     * Menutup event (menandai sebagai selesai)
     */
    public function closeEvent(EventPengajuan $event)
    {
        // Update status event menjadi Closed
        $event->update(['status' => 'Closed']);
        
        // Buat atau update verifikasi event
        VerifikasiEvent::updateOrCreate(
            ['event_id' => $event->event_id],
            [
                'admin_id' => Auth::user()->user_id,
                'tanggal_verifikasi' => Carbon::now()->format('Y-m-d'),
                'status' => 'Closed'
            ]
        );
        
        return redirect()->back()->with('success', 'Event berhasil ditutup!');
    }
} 