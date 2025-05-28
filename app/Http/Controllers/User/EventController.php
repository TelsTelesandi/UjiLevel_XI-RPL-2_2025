<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EventPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = auth()->user()->events()
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.events.index', compact('events'));
    }

    public function create()
    {
        return view('user.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_event' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'total_pembiayaan' => 'required|string|max:255',
            'proposal' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'deskripsi' => 'required|string',
            'tanggal_pengajuan' => 'required|date',
        ]);

        $filePath = $request->file('proposal')->store('proposals', 'public');

        EventPengajuan::create([
            'user_id' => auth()->id(),
            'judul_event' => $request->judul_event,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'total_pembiayaan' => $request->total_pembiayaan,
            'proposal' => $filePath,
            'deskripsi' => $request->deskripsi,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'status' => 'pending',
        ]);

        return redirect()->route('user.events.index')
            ->with('success', 'Pengajuan event berhasil dibuat');
    }

    public function edit(EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.events.edit', compact('event'));
    }

    public function update(Request $request, EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'judul_event' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'total_pembiayaan' => 'required|string|max:255',
            'proposal' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'deskripsi' => 'required|string',
            'tanggal_pengajuan' => 'required|date',
        ]);

        $data = $request->except('proposal');

        if ($request->hasFile('proposal')) {
            Storage::disk('public')->delete($event->proposal);
            $data['proposal'] = $request->file('proposal')->store('proposals', 'public');
        }

        $event->update($data);

        return redirect()->route('user.events.index')
            ->with('success', 'Pengajuan event berhasil diperbarui');
    }

    public function destroy(EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($event->proposal);
        $event->delete();

        return redirect()->route('user.events.index')
            ->with('success', 'Pengajuan event berhasil dihapus');
    }

    public function close(EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $event->update(['status' => 'closed']);

        return redirect()->route('user.events.index')
            ->with('success', 'Event berhasil ditutup');
    }
}