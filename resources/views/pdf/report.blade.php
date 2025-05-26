<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Event Ekstrakurikuler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
        .header p {
            font-size: 12px;
            margin: 5px 0 0;
            padding: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info table td {
            padding: 3px 0;
        }
        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.report th, 
        table.report td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }
        table.report th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        table.report tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary table td {
            padding: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .filter-info {
            margin-bottom: 15px;
            font-size: 10px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN EVENT EKSTRAKURIKULER</h1>
        <p>Sistem Pengajuan Event Ekstrakurikuler</p>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
    </div>
    
    <div class="info">
        <table>
            <tr>
                <td width="150">Tanggal Cetak</td>
                <td>: {{ Carbon\Carbon::now()->format('d M Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Total Event</td>
                <td>: {{ count($events) }}</td>
            </tr>
            <tr>
                <td>Disetujui</td>
                <td>: {{ $events->where('status', 'Disetujui')->count() }}</td>
            </tr>
            <tr>
                <td>Ditolak</td>
                <td>: {{ $events->where('status', 'Ditolak')->count() }}</td>
            </tr>
            <tr>
                <td>Menunggu</td>
                <td>: {{ $events->where('status', 'Menunggu')->count() }}</td>
            </tr>
            @if(isset($filters) && ($filters['status'] != 'all' || $filters['period'] != 'all' || $filters['ekskul'] != 'all'))
            <tr>
                <td colspan="2" class="filter-info">
                    Filter yang digunakan: 
                    @if($filters['status'] != 'all') Status: {{ $filters['status'] }}; @endif
                    @if($filters['period'] != 'all') Periode: {{ $filters['period'] == 'custom' ? 'Kustom' : $filters['period'].' hari terakhir' }}; @endif
                    @if($filters['ekskul'] != 'all') Ekskul: {{ $filters['ekskul'] }}; @endif
                </td>
            </tr>
            @endif
        </table>
    </div>
    
    <table class="report">
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul Event</th>
                <th>Pengaju</th>
                <th>Ekskul</th>
                <th>Jenis Kegiatan</th>
                <th>Tanggal Pengajuan</th>
                <th>Biaya (Rp)</th>
                <th>Status</th>
                <th>Verifikator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $index => $event)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $event->judul_event }}</td>
                <td>{{ $event->user->nama_lengkap }}</td>
                <td>{{ $event->user->ekskul }}</td>
                <td>{{ $event->jenis_kegiatan }}</td>
                <td>{{ Carbon\Carbon::parse($event->tanggal_pengajuan)->format('d/m/Y') }}</td>
                <td>{{ number_format($event->total_pembiayaan, 0, ',', '.') }}</td>
                <td class="
                    @if($event->status == 'Disetujui') status-approved
                    @elseif($event->status == 'Ditolak') status-rejected
                    @else status-pending @endif">
                    {{ $event->status }}
                </td>
                <td>
                    @if(isset($event->verifikasi) && isset($event->verifikasi->admin))
                        {{ $event->verifikasi->admin->nama_lengkap }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data event</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="summary">
        <table>
            <tr>
                <td width="70%"></td>
                <td>
                    <p>{{ Carbon\Carbon::now()->format('d M Y') }}</p>
                    <p>Admin,</p>
                    <br><br><br>
                    <p>{{ auth()->user()->nama_lengkap }}</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Dicetak melalui Sistem Pengajuan Event Ekstrakurikuler pada {{ Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html> 