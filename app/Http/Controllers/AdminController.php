<?php

namespace App\Http\Controllers;

use App\Models\EventPengajuan;
use App\Models\VerifikasiEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Smalot\PdfParser\Parser;

class AdminController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $eventPengajuans = EventPengajuan::with(['user', 'verifikasiEvent'])
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'unclosed');
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul_event', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('ekskul', 'like', "%{$search}%");
                    });
                    
                    $this->addDateSearchConditions($q, $search);
                });
            })
            ->orderBy('tanggal_pengajuan', 'asc')
            ->paginate(10)
            ->appends(['search' => $search]);
            
        return view('admin.index', compact('eventPengajuans', 'search'));
    }

    public function index2(Request $request)
    {
        $search = $request->input('search');
        $eventPengajuans = EventPengajuan::with(['user', 'verifikasiEvent'])
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'closed');
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul_event', 'like', "%{$search}%")
                    ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('ekskul', 'like', "%{$search}%");
                    });
                    
                    $this->addDateSearchConditions($q, $search);
                });
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
            
        return view('admin.closed', compact('eventPengajuans', 'search'));
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function($query) use ($search){
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('ekskul', 'like', "%{$search}%")
            ->orWhere('role', 'like', "%{$search}%")
            ->orWhere('nama_lengkap', 'like', "%{$search}%");
        })
        ->orderBy('nama_lengkap')
        ->paginate(10)
        ->appends(['search' => $search]);
            
        return view('admin.users.index', compact('users', 'search'));
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
    
    public function show(EventPengajuan $eventPengajuan)
    {
        $eventPengajuan->load(['user', 'verifikasiEvent']);
        return view('admin.show', compact('eventPengajuan'));
    }
    
    public function approve(Request $request, EventPengajuan $eventPengajuan)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);
        
        $eventPengajuan->update([
            'status' => 'disetujui',
        ]);
        
        VerifikasiEvent::updateOrCreate(
            ['event_id' => $eventPengajuan->event_id],
            [
                'admin_id' => Auth::id(),
                'tanggal_verifikasi' => now(),
                'catatan_admin' => $request->catatan_admin ?? null,
                'status' => 'closed',
            ]
        );
        
        return redirect()->route('admin.index')
            ->with('success', 'Proposal kegiatan berhasil disetujui.');
    }
    
    public function reject(Request $request, EventPengajuan $eventPengajuan)
    {
        $request->validate([
            'catatan_admin' => 'required|string',
        ],[
            'Catatan Penolakan tidak boleh kosong.'
        ]);
        
        $eventPengajuan->update([
            'status' => 'ditolak',
        ]);
        
        VerifikasiEvent::updateOrCreate(
            ['event_id' => $eventPengajuan->event_id],
            [
                'admin_id' => Auth::id(),
                'tanggal_verifikasi' => now(),
                'catatan_admin' => $request->catatan_admin,
                'status' => 'closed',
            ]
        );
        
        return redirect()->route('admin.index')
            ->with('success', 'Proposal kegiatan berhasil ditolak.');
    }
    
    public function downloadFile(EventPengajuan $eventPengajuan)
    {
        if ($eventPengajuan->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/' . $eventPengajuan->proposal);
        return response()->download($filePath);
    }

    public function downloadPDF()
    {
        $eventPengajuans = EventPengajuan::with(['user', 'verifikasiEvent'])
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'unclosed');
            })
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        // Calculate the number of chunks (each chunk contains 10 items per page)
        $chunksCount = ceil($eventPengajuans->count() / 10);
    
        // Pass the necessary data to the PDF view
        $data = [
            'pengajuan' => $eventPengajuans,
            'title' => ' LAPORAN DATA PENGAJUAN ACARA KEGIATAN', // Title for the PDF
            'chunksCount' => $chunksCount, // For footer page counting
            'nama_sekolah' => 'SMK Telekomunikasi Telesandi Bekasi', // School name
            'alamat_sekolah' => 'Tambun Selatan', // School address
            'tanggal_jam' => Carbon::now()->format('d-m-Y H:i:s'), // Current date and time
        ];
    
        // Load the view and generate the PDF
        $pdf = PDF::loadView('admin.pdf', $data);
    
        // Return the PDF as a download
        return $pdf->download('data_pengajuan.pdf');
    }

    public function downloadRiwayatPDF()
    {
        $eventPengajuans = EventPengajuan::with(['user', 'verifikasiEvent'])
            ->whereHas('verifikasiEvent', function($query) {
                $query->where('status', 'closed');
            })
            ->orderBy('tanggal_pengajuan', 'asc')
            ->get();

        // Calculate the number of chunks (each chunk contains 10 items per page)
        $chunksCount = ceil($eventPengajuans->count() / 10);
    
        // Pass the necessary data to the PDF view
        $data = [
            'pengajuan' => $eventPengajuans,
            'title' => 'LAPORAN RIWAYAT PENGAJUAN ACARA KEGIATAN', // Title for the PDF
            'chunksCount' => $chunksCount, // For footer page counting
            'nama_sekolah' => 'SMK Telekomunikasi Telesandi Bekasi', // School name
            'alamat_sekolah' => 'Tambun Selatan', // School address
            'tanggal_jam' => Carbon::now()->format('d-m-Y H:i:s'), // Current date and time
        ];
    
        // Load the view and generate the PDF
        $pdf = PDF::loadView('admin.pdf', $data);
    
        // Return the PDF as a download
        return $pdf->download('riwayat_pengajuan.pdf');
    }
}