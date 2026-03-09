<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHP - {{ $travelReport->user->name }} - {{ $travelReport->destination_city }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #fff;
        }

        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            position: relative;
        }

        .page-break {
            page-break-before: always;
        }

        /* KOP SURAT */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }

        .kop-logo {
            flex-shrink: 0;
        }

        .kop-logo img {
            height: 60px;
            width: auto;
        }

        .kop-info {
            flex: 1;
        }

        .kop-info .company-name {
            font-size: 14pt;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-info .company-address {
            font-size: 9pt;
            line-height: 1.5;
            color: #333;
        }

        .kop-info .company-address a {
            color: #0066cc;
            text-decoration: none;
        }

        /* JUDUL */
        .doc-title {
            text-align: center;
            margin-bottom: 5px;
        }

        .doc-title h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .doc-title .subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* INFO BOX */
        .info-box {
            border: 1px solid #999;
            padding: 10px 15px;
            margin: 15px 0 20px 0;
            background: #fafafa;
        }

        .info-box p.info-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .info-box ol {
            font-size: 9.5pt;
            padding-left: 20px;
            line-height: 1.5;
        }

        /* SECTIONS */
        .section-heading {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .section-heading .section-num {
            margin-right: 8px;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            margin-bottom: 10px;
            margin-left: 25px;
        }

        .data-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 12pt;
        }

        .data-table td.label {
            width: 220px;
            font-style: italic;
        }

        .data-table td.separator {
            width: 15px;
            text-align: center;
        }

        /* CONTENT */
        .section-content {
            margin-left: 25px;
            text-align: justify;
        }

        .section-content p {
            text-indent: 35px;
            margin-bottom: 8px;
        }

        .section-content ol,
        .section-content ul {
            padding-left: 25px;
            margin-bottom: 8px;
        }

        .section-content ol li,
        .section-content ul li {
            margin-bottom: 4px;
        }

        .kegiatan-table {
            width: 100%;
            margin-left: 25px;
            margin-bottom: 10px;
        }

        .kegiatan-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 12pt;
        }

        .kegiatan-table .num {
            width: 25px;
        }

        .kegiatan-table .lbl {
            width: 80px;
        }

        .kegiatan-table .sep {
            width: 15px;
            text-align: center;
        }

        /* DOKUMENTASI */
        .doc-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-left: 25px;
            margin-bottom: 10px;
        }

        .doc-item {
            width: calc(50% - 5px);
            border: 1px solid #ccc;
            padding: 5px;
        }

        .doc-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .doc-item .doc-caption {
            font-size: 9pt;
            text-align: center;
            margin-top: 4px;
            color: #333;
        }

        /* SIGNATURES */
        .signature-section {
            margin-top: 40px;
        }

        .signature-pembuat {
            text-align: right;
            margin-bottom: 30px;
        }

        .signature-pembuat .sign-space {
            height: 60px;
            position: relative;
        }

        .signature-pembuat .sign-space img {
            position: absolute;
            right: 10px;
            top: -10px;
            height: 70px;
            width: auto;
        }

        .signature-pembuat .sign-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
        }

        .signature-pembuat .sign-name {
            font-weight: bold;
            font-size: 12pt;
        }

        .approval-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .approval-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .approval-block {
            width: 45%;
            text-align: center;
            margin-bottom: 25px;
        }

        .approval-block .role-title {
            font-style: italic;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .approval-block .sign-space {
            height: 55px;
        }

        .approval-block .sign-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 180px;
        }

        .approval-single {
            text-align: center;
            margin-bottom: 20px;
        }

        .approval-single .role-title {
            font-style: italic;
            font-weight: bold;
            font-size: 11pt;
        }

        .approval-single .sign-space {
            height: 55px;
        }

        .approval-single .sign-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 180px;
        }

        /* FOOTER */
        .page-footer {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
        }

        /* PRINT */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #c00;
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            transition: all 0.2s;
        }

        .print-btn:hover {
            background: #a00;
            transform: translateY(-1px);
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #333;
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 8px;
            text-decoration: none;
            z-index: 9999;
        }

        .back-btn:hover {
            background: #555;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                max-width: none;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak Laporan</button>
    <a href="{{ route('travel-reports.show', $travelReport) }}" class="back-btn no-print">← Kembali</a>

    @php
        // Aggregate data from grouped activities for separated PDF sections
        $allResults = [];
        $allIssues = [];
        $allDocs = collect();

        foreach ($travelReport->activities as $act) {
            $prefix = $act->activity_date->format('d M Y');
            if ($act->results && count($act->results)) {
                foreach ($act->results as $r) {
                    $allResults[] = $r . ' (' . $prefix . ')';
                }
            }
            if ($act->issues) {
                $allIssues[] = $act->issues . ' (' . $prefix . ')';
            }
            if ($act->documents->isNotEmpty()) {
                foreach ($act->documents as $doc) {
                    $allDocs->push($doc);
                }
            }
        }
        // Also include orphan docs
        foreach ($travelReport->documents->whereNull('travel_report_activity_id') as $doc) {
            $allDocs->push($doc);
        }
    @endphp

    {{-- ====== HALAMAN 1 ====== --}}
    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ asset('image/logo_beacon.png') }}" alt="Logo Beacon Engineering">
            </div>
            <div class="kop-info">
                <div class="company-name">PT. ARTA TEKNOLOGI COMUNINDO</div>
                <div class="company-address">
                    Kadirojo I, Purwomartani, Kec. Kalasan, Kab. Sleman, Daerah Istimewa Yogyakarta<br>
                    Ph./Fax. (0274) 498 6899, e-mail : <a href="mailto:info@bejogja.com">info@bejogja.com</a>
                </div>
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="doc-title">
            <h1>Laporan Hasil Perjalanan Dinas Luar Kota</h1>
            <div class="subtitle">Dasar Perjalanan Dinas</div>
            @if($travelReport->surat_tugas_no)
                <div class="subtitle">Surat Tugas Nomor {{ $travelReport->surat_tugas_no }}</div>
            @endif
            @if($travelReport->surat_tugas_date)
                <div class="subtitle">Tanggal {{ $travelReport->surat_tugas_date->format('d F Y') }}</div>
            @endif
        </div>

        {{-- INFORMASI PENTING --}}
        <div class="info-box">
            <p class="info-title">Informasi penting:</p>
            <ol>
                <li>LHP ini digunakan sebagai pertanggungjawaban dinas luar kota;</li>
                <li>LHP ini dibuat secara pribadi, <strong>DILARANG KERAS</strong> untuk mencopy LHP karyawan lain
                    walaupun untuk surat tugasnya sama.</li>
            </ol>
        </div>

        {{-- I. IDENTITAS --}}
        <div class="section-heading">
            <span class="section-num">I.</span>Identitas Perjalanan Dinas
        </div>
        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td>{{ $travelReport->user->name }}</td>
            </tr>
            <tr>
                <td class="label"><i>Job Position</i></td>
                <td class="separator">:</td>
                <td>{{ $travelReport->job_position }}</td>
            </tr>
            <tr>
                <td class="label">Divisi</td>
                <td class="separator">:</td>
                <td>{{ $travelReport->division_name }}</td>
            </tr>
            <tr>
                <td class="label">Tujuan Perjalanan Dinas</td>
                <td class="separator">:</td>
                <td>{{ Str::limit($travelReport->purpose, 80) }}</td>
            </tr>
            <tr>
                <td class="label">Kota Tujuan</td>
                <td class="separator">:</td>
                <td>{{ $travelReport->destination_city }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Keberangkatan</td>
                <td class="separator">:</td>
                <td>{{ $travelReport->departure_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kepulangan</td>
                <td class="separator">:</td>
                <td>{{ $travelReport->return_date->format('d F Y') }}</td>
            </tr>
        </table>

        {{-- II. MAKSUD DAN TUJUAN --}}
        <div class="section-heading">
            <span class="section-num">II.</span>Maksud dan Tujuan Perjalanan Dinas
        </div>
        <div class="section-content">
            <p>Perjalanan dinas ini dilaksanakan dengan tujuan untuk {{ $travelReport->purpose }}</p>
        </div>

        {{-- III. PELAKSANAAN KEGIATAN (aggregated from activities) --}}
        <div class="section-heading">
            <span class="section-num">III.</span>Pelaksanaan Kegiatan
        </div>
        <div class="section-content">
            <p>Adapun kegiatan yang dilaksanakan selama perjalanan dinas adalah sebagai berikut:</p>
        </div>
        <table class="kegiatan-table">
            @foreach($travelReport->activities as $activity)
                @if(!$loop->first)
                    <tr>
                        <td colspan="4" style="height: 8px;"></td>
                    </tr>
                @endif
                <tr>
                    <td class="num">{{ $loop->iteration }}.</td>
                    <td class="lbl">Tanggal</td>
                    <td class="sep">:</td>
                    <td>{{ $activity->activity_date->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="lbl">Kegiatan</td>
                    <td class="sep">:</td>
                    <td>{{ $activity->description }}</td>
                </tr>
            @endforeach
        </table>

        {{-- IV. HASIL YANG DICAPAI (aggregated from all activities) --}}
        <div class="section-heading">
            <span class="section-num">IV.</span>Hasil yang Dicapai
        </div>
        <div class="section-content">
            <p>Hasil yang diperoleh dari pelaksanaan perjalanan dinas ini antara lain:</p>
            @if(count($allResults))
                <ol>
                    @foreach($allResults as $result)
                        <li>{{ $result }}</li>
                    @endforeach
                </ol>
            @endif
        </div>

        {{-- V. PERMASALAHAN (aggregated from all activities) --}}
        <div class="section-heading">
            <span class="section-num">V.</span>Permasalahan dan Kendala (Jika Ada)
        </div>
        <div class="section-content">
            @if(count($allIssues))
                <p>Selama pelaksanaan perjalanan dinas, terdapat permasalahan sebagai berikut:</p>
                <ol>
                    @foreach($allIssues as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                </ol>
            @else
                <p>Selama pelaksanaan perjalanan dinas, tidak terdapat kendala/permasalahan.</p>
            @endif
        </div>

        <div class="page-footer">Halaman 1 dari 2</div>
    </div>

    {{-- ====== HALAMAN 2 ====== --}}
    <div class="page page-break">
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ asset('image/logo_beacon.png') }}" alt="Logo Beacon Engineering">
            </div>
            <div class="kop-info">
                <div class="company-name">PT. ARTA TEKNOLOGI COMUNINDO</div>
                <div class="company-address">
                    Kadirojo I, Purwomartani, Kec. Kalasan, Kab. Sleman, Daerah Istimewa Yogyakarta<br>
                    Ph./Fax. (0274) 498 6899, e-mail : <a href="mailto:info@bejogja.com">info@bejogja.com</a>
                </div>
            </div>
        </div>

        {{-- VI. KESIMPULAN DAN REKOMENDASI --}}
        <div class="section-heading">
            <span class="section-num">VI.</span>Kesimpulan dan Rekomendasi
        </div>
        <div class="section-content">
            <p>Berdasarkan pelaksanaan perjalanan dinas tersebut, dapat disimpulkan bahwa
                {{ $travelReport->conclusion }}
            </p>
            @if($travelReport->recommendations && count($travelReport->recommendations))
                <p>Adapun rekomendasi tindak lanjut yang disarankan adalah:</p>
                <ol>
                    @foreach($travelReport->recommendations as $rec)
                        <li>{{ $rec }}</li>
                    @endforeach
                </ol>
            @endif
        </div>

        {{-- VII. DOKUMENTASI (aggregated from all activities) --}}
        <div class="section-heading">
            <span class="section-num">VII.</span>Dokumentasi
        </div>
        @if($allDocs->isNotEmpty())
            <div class="doc-grid">
                @foreach($allDocs as $doc)
                    <div class="doc-item">
                        @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->caption }}">
                        @else
                            <div style="height:150px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;">
                                <span style="font-size:10pt;color:#999;">📄 {{ basename($doc->file_path) }}</span>
                            </div>
                        @endif
                        <div class="doc-caption">
                            {{ $doc->caption ?? '' }}
                            @if($doc->activity_date)
                                ({{ $doc->activity_date->format('d M Y') }})
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="section-content">
                <p style="font-style: italic; color: #888;">(Tidak ada dokumentasi foto)</p>
            </div>
        @endif

        {{-- VIII. PENUTUP --}}
        <div class="section-heading">
            <span class="section-num">VIII.</span>Penutup
        </div>
        <div class="section-content">
            <p>Demikian laporan hasil perjalanan dinas ini disusun sebagai bentuk pertanggungjawaban atas pelaksanaan
                tugas yang telah diberikan.</p>
        </div>

        {{-- TANDA TANGAN --}}
        @php
            // Sort approvals by step_order for display
            $sortedApprovals = $travelReport->approvals->sortBy('step_order');
        @endphp
        <div class="signature-section">
            <div class="signature-pembuat">
                <div>Sleman, {{ $travelReport->return_date->format('d F Y') }}</div>
                <div style="font-weight:bold;">Pembuat LHP</div>
                <div class="sign-space">
                    @if($travelReport->user->signature)
                        <img src="{{ asset('storage/' . $travelReport->user->signature) }}" alt="Tanda Tangan">
                    @endif
                </div>
                <div class="sign-name">{{ $travelReport->user->name }}</div>
            </div>

            <div class="approval-title">Mengetahui dan menyetujui.</div>
            <div class="approval-grid">
                @foreach($sortedApprovals as $ap)
                    <div class="approval-block">
                        <div class="role-title">{{ $ap->step_label }}</div>
                        @if($ap->status === 'approved' && $ap->approver)
                            <div style="font-style:italic;font-size:9pt;color:#555;">{{ $ap->updated_at->format('d M Y') }}
                            </div>
                            <div class="sign-space">
                                @if($ap->approver->signature)
                                    <img src="{{ asset('storage/' . $ap->approver->signature) }}" alt="TTD {{ $ap->step_label }}">
                                @endif
                            </div>
                            <div class="sign-line"></div>
                            <div class="sign-name">{{ $ap->approver->name }}</div>
                        @else
                            <div style="font-style:italic;font-size:9pt;color:#999;">
                                {{ $ap->status === 'pending' ? '(menunggu)' : '(tidak diperlukan)' }}
                            </div>
                            <div class="sign-space"></div>
                            <div class="sign-line"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="page-footer">Halaman 2 dari 2</div>
    </div>
</body>

</html>