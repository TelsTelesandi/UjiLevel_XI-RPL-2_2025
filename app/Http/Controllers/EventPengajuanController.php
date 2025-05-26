<?php

namespace App\Http\Controllers;

use App\Models\EventPengajuan;
use App\Models\VerifikasiEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventPengajuanController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $eventPengajuans = EventPengajuan::with('verifikasiEvent')
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'unclosed');
            })
            ->where('user_id', Auth::id())
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul_event', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
                    
                    $this->addDateSearchConditions($q, $search);
                });
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
            
        return view('event_pengajuans.index', compact('eventPengajuans', 'search'));
    }

    public function indexRiwayatProposal(Request $request)
    {
        $search = $request->input('search');
        $eventPengajuans = EventPengajuan::with('verifikasiEvent')
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'closed');
            })
            ->where('user_id', Auth::id())
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul_event', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
                    
                    $this->addDateSearchConditions($q, $search);
                });
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
            
        return view('event_pengajuans.closed', compact('eventPengajuans', 'search'));
    }

    protected function addDateSearchConditions($query, $searchTerm)
    {
        $normalizedSearch = trim(preg_replace('/\s+/', ' ', strtolower($searchTerm)));
        
        $indonesianMonths = [
            'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
            'april' => 'april', 'mei' => 'may', 'juni' => 'june',
            'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
            'oktober' => 'october', 'november' => 'november', 'desember' => 'december'
        ];
        
        foreach ($indonesianMonths as $id => $en) {
            $normalizedSearch = str_replace($id, $en, $normalizedSearch);
        }
        
        $formats = [
            'd F Y', 'j F Y',    // 16 May 2025 atau 16 Mei 2025
            'd F', 'j F',         // 16 May atau 16 Mei (tanpa tahun)
            'd-m-Y', 'j-m-Y',     // 16-05-2025
            'd/m/Y', 'j/m/Y',     // 16/05/2025
            'F Y',                // May 2025
            'Y-m-d',              // 2025-05-16 (format database)
        ];
        
        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $normalizedSearch);
                if ($date) {
                    $query->orWhereDate('tanggal_pengajuan', $date->format('Y-m-d'));
                    
                    if (!str_contains($format, 'Y')) {
                        $query->orWhere(function($q) use ($date) {
                            $q->whereDay('tanggal_pengajuan', $date->day)
                            ->whereMonth('tanggal_pengajuan', $date->month);
                        });
                    }
                    
                    return;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        try {
            $date = \Carbon\Carbon::parse($normalizedSearch);
            $query->orWhereDate('tanggal_pengajuan', $date->format('Y-m-d'));
        } catch (\Exception $e) {
            // Jika parsing gagal, lanjut ke pencarian numerik
        }
        
        if (is_numeric($searchTerm)) {
            $query->orWhereDay('tanggal_pengajuan', $searchTerm)
                ->orWhereMonth('tanggal_pengajuan', $searchTerm)
                ->orWhereYear('tanggal_pengajuan', $searchTerm);
        }
    }
    
    public function create()
    {
        return view('event_pengajuans.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'judul_event' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'total_pembiayaan' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:50',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ],[
            'judul_event.required' => 'Judul event tidak boleh kosong.',
            'jenis_kegiatan.required' => 'Jenis kegiatan tidak boleh kosong.',
            'total_pembiayaan.required' => 'Total pembiayaan tidak boleh kosong.',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong.',
            'deskripsi.min' => 'Deskripsi harus minimal 50 karakter.',
            'proposal_file.required' => 'File proposal tidak boleh kosong.',
            'proposal_file.file' => 'File proposal harus berupa file.',
            'proposal_file.mimes' => 'File proposal harus berupa PDF, DOC, atau DOCX.',
            'proposal_file.max' => 'File proposal tidak boleh lebih dari 10 MB.',
            'judul_event.string' => 'Judul event harus berupa string.',
            'judul_event.max' => 'Judul event tidak boleh lebih dari 255 karakter.',
            'jenis_kegiatan.string' => 'Jenis kegiatan harus berupa string.',
            'jenis_kegiatan.max' => 'Jenis kegiatan tidak boleh lebih dari 255 karakter.',
            'total_pembiayaan.string' => 'Total pembiayaan harus berupa string.',
            'total_pembiayaan.max' => 'Total pembiayaan tidak boleh lebih dari 255 karakter.',
            'deskripsi.string' => 'Deskripsi harus berupa string.',
            'deskripsi.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
        ]);
        
        // Handle file upload
        $file = $request->file('proposal_file');
        $originalName = $file->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $counter = 1;
        $newFileName = $originalName;

        while (Storage::disk('public')->exists('proposal/' . $newFileName)) {
            $newFileName = $filename . " ({$counter})." . $extension;
            $counter++;
        }

        $filePath = $file->storeAs('proposal', $newFileName, 'public');
        
        // Create event pengajuan
        EventPengajuan::create([
            'user_id' => Auth::id(),
            'judul_event' => $request->judul_event,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'total_pembiayaan' => str_replace([',', '.'], '', trim($request->total_pembiayaan)),
            'deskripsi' => $request->deskripsi,
            'proposal' => $filePath,
            'tanggal_pengajuan' => now()->format('Y-m-d'),
            'status' => 'menunggu',
        ]);

        VerifikasiEvent::create([
            'event_id' => EventPengajuan::orderBy('event_id', 'desc')->first()->event_id,
            'admin_id' => Auth::id(),
            'tanggal_verifikasi' => null,
            'catatan_admin' => null,
            'status' => 'unclosed',
        ]);
        
        return redirect()->route('user.index')
            ->with('success', 'Proposal kegiatan berhasil diajukan dan sedang menunggu peninjauan.');
    }
    
    public function show(EventPengajuan $eventPengajuan)
    {
        // Check if user owns this event pengajuan
        if ($eventPengajuan->user_id !== Auth::id() && !Auth::user()->isUser()) {
            abort(403);
        }
        
        // Load verifikasi event if exists
        $eventPengajuan->load('verifikasiEvent');
        
        return view('event_pengajuans.show', compact('eventPengajuan'));
    }
    
    public function cancel(EventPengajuan $eventPengajuan)
    {
        // Check if user owns this event pengajuan
        if ($eventPengajuan->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Check if event pengajuan can be cancelled
        if ($eventPengajuan->status !== 'menunggu') {
            return back()->with('error', 'Hanya proposal dengan status Menunggu yang dapat dibatalkan.');
        }

        $filePath = storage_path('app/public/' . $eventPengajuan->proposal);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $eventPengajuan->delete();
        
        return redirect()->route('user.index')
            ->with('success', 'Proposal kegiatan berhasil dibatalkan.');
    }
    
    public function downloadFile(EventPengajuan $eventPengajuan)
    {
        // Check if user owns this event pengajuan or is admin
        if ($eventPengajuan->user_id !== Auth::id() && !Auth::user()->isUser()) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/' . $eventPengajuan->proposal);
        return response()->download($filePath);
    }
}