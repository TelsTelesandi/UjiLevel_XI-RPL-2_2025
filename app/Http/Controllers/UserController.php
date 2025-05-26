<?php

namespace App\Http\Controllers;

use App\Models\EventPengajuan;
use App\Models\EventPhoto;
use App\Models\VerifikasiEvent;
use App\Traits\FileValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use FileValidation;

    public function dashboard()
    {
        $events = EventPengajuan::where('user_id', auth()->user()->user_id)
                               ->with('verifikasi')
                               ->latest()
                               ->get();
        
        $stats = [
            'total' => $events->count(),
            'menunggu' => $events->where('status', 'menunggu')->count(),
            'disetujui' => $events->where('status', 'disetujui')->count(),
            'ditolak' => $events->where('status', 'ditolak')->count(),
        ];
        
        return view('user.dashboard', compact('events', 'stats'));
    }
    
    public function createEvent()
    {
        return view('user.events.create');
    }
    
    public function storeEvent(Request $request)
    {
        $request->validate([
            'judul_event' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|in:Lomba,Workshop,Seminar,Pentas,Lainnya',
            'deskripsi' => 'required|string',
            'tanggal_pengajuan' => 'required|date',
            'total_pembiayaan' => 'required|numeric|min:0',
            'proposal' => 'required|file|mimes:pdf|max:5120'
        ]);

        try {
            // Upload dan validasi proposal
            $proposalPath = $this->validateAndStoreFile($request->file('proposal'), 'proposals');

            // Buat event baru
            $event = EventPengajuan::create([
                'user_id' => auth()->id(),
                'judul_event' => $request->judul_event,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'total_pembiayaan' => $request->total_pembiayaan,
                'proposal' => $proposalPath,
                'status' => 'menunggu'
            ]);

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Event berhasil diajukan dan menunggu verifikasi admin.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    public function showEvent(EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }

        $event->load(['verifikasi', 'photos']);
        $this->cleanupInvalidPhotos($event);
        return view('user.events.show', compact('event'));
    }

    public function closeEvent(Request $request, EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($event->status !== 'disetujui') {
            return response()->json(['error' => 'Event belum disetujui'], 400);
        }

        $request->validate([
            'photos.*' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ], [
            'photos.*.required' => 'Foto dokumentasi harus diupload',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format foto harus JPG atau PNG',
            'photos.*.max' => 'Ukuran foto maksimal 5MB'
        ]);

        try {
            DB::beginTransaction();

            // Update status verifikasi menjadi closed
            $verifikasi = VerifikasiEvent::where('event_id', $event->event_id)->first();
            if (!$verifikasi) {
                $verifikasi = new VerifikasiEvent();
                $verifikasi->event_id = $event->event_id;
                $verifikasi->admin_id = 1; // Default admin
            }
            $verifikasi->status = 'closed';
            $verifikasi->save();

            // Upload foto dokumentasi
            foreach ($request->file('photos') as $photo) {
                $photoPath = $this->validateAndStoreFile($photo, 'event-photos');
                
                EventPhoto::create([
                    'event_id' => $event->event_id,
                    'photo_path' => $photoPath,
                    'photo_name' => $photo->getClientOriginalName()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event berhasil ditutup'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menutup event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPhotos(Request $request, EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'photos.*' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
            foreach ($request->file('photos') as $photo) {
                $photoPath = $this->validateAndStoreFile($photo, 'event-photos');
                
                EventPhoto::create([
                    'event_id' => $event->event_id,
                    'photo_path' => $photoPath,
                    'photo_name' => $photo->getClientOriginalName()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diunggah'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengunggah foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports()
    {
        $events = EventPengajuan::where('user_id', auth()->user()->user_id)
                               ->where('status', 'disetujui')
                               ->with(['verifikasi', 'photos'])
                               ->latest()
                               ->get();
        
        $stats = [
            'total_closed' => $events->whereNotNull('verifikasi.status')->where('verifikasi.status', 'closed')->count(),
            'total_approved' => $events->count(),
            'pending_close' => $events->whereNull('verifikasi.status')->count()
        ];
        
        return view('user.reports', compact('events', 'stats'));
    }

    public function viewProposal(EventPengajuan $event)
    {
        if ($event->user_id !== auth()->id()) {
            abort(403);
        }
        
        return Storage::disk('public')->download($event->proposal);
    }

    protected function cleanupInvalidPhotos(EventPengajuan $event)
    {
        foreach ($event->photos as $photo) {
            if (!Storage::disk('public')->exists($photo->photo_path)) {
                $photo->delete();
            }
        }
    }
}