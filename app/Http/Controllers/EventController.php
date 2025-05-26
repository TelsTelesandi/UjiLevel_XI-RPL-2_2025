<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventPengajuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event milik user
     */
    public function index()
    {
        $events = EventPengajuan::where('user_id', Auth::user()->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.events.index', compact('events'));
    }
    
    /**
     * Menampilkan form untuk membuat event baru
     */
    public function create()
    {
        return view('user.create-event');
    }
    
    /**
     * Menyimpan event baru
     */
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'judul_event' => 'required|string|max:200',
            'jenis_kegiatan' => 'required|string|max:200',
            'total_pembiayaan' => 'required|string',
            'deskripsi' => 'required|string',
            'proposal' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'judul_event.required' => 'Judul event harus diisi',
            'judul_event.max' => 'Judul event maksimal 200 karakter',
            'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih',
            'total_pembiayaan.required' => 'Total pembiayaan harus diisi',
            'deskripsi.required' => 'Deskripsi event harus diisi',
            'proposal.required' => 'File proposal harus diunggah',
            'proposal.mimes' => 'File proposal harus berformat PDF, DOC, atau DOCX',
            'proposal.max' => 'Ukuran file proposal maksimal 10MB'
        ]);
        
        // Bersihkan format angka pada total pembiayaan
        $totalPembiayaan = str_replace('.', '', $validated['total_pembiayaan']);
        
        try {
            // Simpan file proposal
            $proposalName = 'Proposal_' . str_replace(' ', '_', $validated['judul_event']) . '_' . time() . '.' . $request->file('proposal')->getClientOriginalExtension();
            $proposalPath = $request->file('proposal')->storeAs('proposals', $proposalName, 'public');
            
            // Buat event baru
            $event = EventPengajuan::create([
                'user_id' => Auth::user()->user_id,
                'judul_event' => $validated['judul_event'],
                'jenis_kegiatan' => $validated['jenis_kegiatan'],
                'total_pembiayaan' => $totalPembiayaan,
                'proposal' => $proposalName,
                'deskripsi' => $validated['deskripsi'],
                'tanggal_pengajuan' => now()->format('Y-m-d'),
                'status' => 'Menunggu',
            ]);
            
            return redirect()->route('events.index')->with('success', 'Event berhasil diajukan! Silahkan tunggu persetujuan dari admin.');
        } catch (\Exception $e) {
            // Hapus file yang telah diupload jika terjadi error
            if (isset($proposalPath) && Storage::disk('public')->exists($proposalPath)) {
                Storage::disk('public')->delete($proposalPath);
            }
            
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mengajukan event. Silakan coba lagi.']);
        }
    }
    
    /**
     * Menampilkan detail event
     */
    public function show(EventPengajuan $event)
    {
        // Pastikan user hanya bisa melihat event miliknya
        if ($event->user_id !== Auth::user()->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('user.events.show', compact('event'));
    }
    
    /**
     * Menutup event (menandai sebagai selesai)
     */
    public function close(EventPengajuan $event)
    {
        // Pastikan user hanya bisa menutup event miliknya
        if ($event->user_id !== Auth::user()->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Update status event menjadi Closed
        $event->update(['status' => 'Closed']);
        
        return redirect()->route('events.index')->with('success', 'Event berhasil ditutup!');
    }
} 