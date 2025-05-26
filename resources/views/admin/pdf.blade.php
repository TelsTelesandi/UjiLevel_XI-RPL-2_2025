<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Manajemen Pengajuan Acara Kegiatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            counter-reset: page;
        }

        @media print {
            @page {
                margin: 100px 20px; /* Set margin for the printed page */
            }
            .header, .footer {
                position: fixed;
                left: 0;
                right: 0;
                height: 50px;
                font-size: 10px;
                padding-top: 10px;
            }
            .header {
                top: 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 2px solid black; /* Underline header */
                padding-bottom: 10px;
            }
            .header img {
                max-width: 100px; /* Logo size */
            }
            .info {
                text-align: left;
                padding-right: 20px;
            }
            .address {
                font-size: 10px;
            }
            .footer {
                position: fixed;
                bottom: 0;
                border-top: 1px solid #000;
                width: 100%;
            }
            .footer .page-number:before {
                content: "Halaman " counter(page) " dari " attr(data-total-pages);
            }
            .page {
                page-break-after: always;
                page-break-inside: avoid;
                margin-top: 120px; /* Add space so it doesn't overlap header */
            }
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
        }

        .header {
            top: -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            max-width: 150px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            float: left;
            width: 120px;
        }

        .info {
            text-align: center;
        }

        .address {
            font-size: 10px;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .page-break {
            page-break-after: always;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        h3 {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

    </style>
</head>
<body>

    @foreach ($pengajuan->chunk(10) as $chunk)
    <div class="header" style="text-align: center; padding: 20px; border-bottom: 2px solid #000;">
        <img src="data:image/{{ pathinfo(public_path('src/logotelesandi.png'), PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents(public_path('src/logotelesandi.png'))) }}" alt="Logo Telesandi" style="width: 300px; height: auto; margin-bottom: 10px;">

        <div class="info">
            <h2 style="font-size: 24px; margin: 0; font-weight: bold;">SMK Telekomunikasi Telesandi Bekasi</h2>
            <p class="address" style="font-size: 14px; margin: 5px 0;">Mekarsari Raya Jl. KH. Mochammad - Mekarsari, Tambun Selatan</p>
            <p class="address" style="font-size: 14px; margin: 5px 0;">Kabupaten Bekasi, Jawa Barat 17510</p>
            <p class="address" style="font-size: 14px; margin: 5px 0;">Telepon: (021) 88332404, Fax: (021) 88323429</p>
            <p class="address" style="font-size: 14px; margin: 5px 0;">Email: telesandismk@gmail.com</p>
            <p class="address" style="font-size: 14px; margin: 5px 0;">Website: <a href="http://www.smktelekomunikasitelesandi.sch.id" target="_blank" style="text-decoration: none; color: #1e90ff;">www.smktelekomunikasitelesandi.sch.id</a></p>
        </div>
    </div>

    <hr>
    <br>
    <div class="page"> <!-- Start a new page for each chunk -->
        <h3>{{ $title }}</h3> <!-- Title centered -->
        <table>
            <thead>
                <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Judul Kegiatan</th>
                    <th style="text-align: center">Nama Pengaju</th>
                    <th style="text-align: center">Jenis Pengajuan</th>
                    <th style="text-align: center">Tanggal Pengajuan</th>
                    <th style="text-align: center">Status Verifikasi</th>
                    <th style="text-align: center">Status Pengajuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $datas)
                    <tr>
                        <td>{{ $loop->iteration + ($loop->parent->iteration - 1) * 10 }}</td>
                        <td>{{ $datas->judul_event }}</td>
                        <td>{{ $datas->user->nama_lengkap . ' (' . $datas->user->ekskul . ')' }}</td>
                        <td>{{ $datas->jenis_kegiatan }}</td>
                        <td>{{ $datas->tanggal_pengajuan }}</td>
                        <td>{{ ucfirst($datas->status) }}</td>
                        <td>{{ ucfirst($datas->verifikasiEvent->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p style="float: left;">Kadavi Raditya A | Uji Level 2025</p>
            <p style="text-align: right;">Halaman ke {{ $loop->iteration }} dari {{ $chunksCount }}</p> <!-- Update footer with proper chunksCount -->
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    </div>
    @endforeach

</body>
</html>
