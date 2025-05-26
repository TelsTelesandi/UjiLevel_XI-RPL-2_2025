<?php

namespace App\Http\Controllers;

use App\Models\EventPengajuan;
use App\Models\VerifikasiEvent;
use App\Traits\FileValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    use FileValidation;

    protected function cleanupInvalidPhotos(EventPengajuan $event)
    {
        foreach ($event->photos as $photo) {
            if (!Storage::disk('public')->exists($photo->photo_path)) {
                $photo->delete();
            }
        }
    }

    public function dashboard()
    {
        $events = EventPengajuan::with(['user', 'verifikasi'])->latest()->get();
        
        $stats = [
            'total' => $events->count(),
            'menunggu' => $events->where('status', 'menunggu')->count(),
            'disetujui' => $events->where('status', 'disetujui')->count(),
            'ditolak' => $events->where('status', 'ditolak')->count(),
        ];
        
        return view('admin.dashboard', compact('events', 'stats'));
    }
    
    public function showEvent(EventPengajuan $event)
    {
        $event->load(['user', 'verifikasi', 'photos']);
        $this->cleanupInvalidPhotos($event);
        return view('admin.events.show', compact('event'));
    }
    
    public function approveEvent(EventPengajuan $event, Request $request)
    {
        $event->update(['status' => 'disetujui']);
        
        VerifikasiEvent::create([
            'event_id' => $event->event_id,
            'admin_id' => auth()->user()->user_id,
            'tanggal_verifikasi' => now()->toDateString(),
            'catatan_admin' => $request->catatan_admin ?? 'Event disetujui',
            'status' => 'unclosed',
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function rejectEvent(EventPengajuan $event, Request $request)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $event->update(['status' => 'ditolak']);
        
        VerifikasiEvent::create([
            'event_id' => $event->event_id,
            'admin_id' => auth()->user()->user_id,
            'tanggal_verifikasi' => now()->toDateString(),
            'catatan_admin' => $request->reason,
            'status' => 'unclosed',
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function viewProposal(EventPengajuan $event)
    {
        return Storage::disk('public')->download($event->proposal);
    }
    
    public function eventReports()
    {
        $events = EventPengajuan::whereHas('verifikasi', function($query) {
                    $query->where('status', 'closed');
                })
                ->with(['user', 'verifikasi', 'photos'])
                ->latest()
                ->get();
        
        $stats = [
            'total_closed' => $events->count(),
            'total_biaya' => $events->sum('total_pembiayaan'),
            'total_ekskul' => $events->pluck('user.ekskul')->unique()->count()
        ];
        
        return view('admin.reports', compact('events', 'stats'));
    }
} 