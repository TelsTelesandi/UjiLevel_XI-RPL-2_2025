<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventPengajuan;
use App\Models\VerifikasiEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = EventPengajuan::with(['user', 'verifikasi'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.events.index', compact('events'));
    }

    public function show(EventPengajuan $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function verify(Request $request, EventPengajuan $event)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_admin' => 'nullable|string',
        ]);

        $event->update(['status' => $request->status]);

        VerifikasiEvent::create([
            'event_id' => $event->event_id,
            'admin_id' => auth()->id(),
            'tanggal_verifikasi' => now(),
            'catatan_admin' => $request->catatan_admin,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diverifikasi');
    }

    public function report()
    {
        $events = EventPengajuan::with(['user', 'verifikasi'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.events.report', compact('events'));
    }
}