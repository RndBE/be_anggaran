<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Perjalanan Dinas Luar Kota</title>
    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
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

        /* ============================================
           PAGE STRUCTURE
           ============================================ */
        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
            position: relative;
        }

        .page-break {
            page-break-before: always;
        }

        /* ============================================
           HEADER / KOP SURAT
           ============================================ */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 100px;
            flex-shrink: 0;
        }

        .kop-logo img {
            width: 100%;
            height: auto;
        }


        .kop-info {
            flex: 1;
            text-align: left;
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

        /* ============================================
           JUDUL DOKUMEN
           ============================================ */
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

        .doc-title .surat-tugas {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ============================================
           INFORMASI PENTING
           ============================================ */
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

        .info-box ol li {
            margin-bottom: 2px;
        }

        /* ============================================
           SECTION HEADINGS
           ============================================ */
        .section-heading {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .section-heading .section-num {
            margin-right: 8px;
        }

        /* ============================================
           DATA TABLE (Identitas)
           ============================================ */
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

        .data-table td.value {
            /* remaining width */
        }

        .data-table td.value .placeholder {
            color: #888;
            border-bottom: 1px dotted #aaa;
            display: inline-block;
            min-width: 250px;
        }

        /* ============================================
           CONTENT SECTIONS
           ============================================ */
        .section-content {
            margin-left: 25px;
            text-align: justify;
        }

        .section-content p {
            text-indent: 35px;
            margin-bottom: 8px;
        }

        .section-content .hint {
            color: #888;
            font-style: italic;
            font-size: 10pt;
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

        /* ============================================
           SIGNATURE BLOCKS
           ============================================ */
        .signature-section {
            margin-top: 40px;
        }

        .signature-pembuat {
            text-align: right;
            margin-bottom: 30px;
        }

        .signature-pembuat .location-date {
            font-size: 12pt;
        }

        .signature-pembuat .role {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .signature-pembuat .sign-space {
            height: 60px;
        }

        .signature-pembuat .sign-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
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

        .approval-block .manager-label {
            font-style: italic;
            font-size: 10pt;
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
            margin-bottom: 3px;
        }

        .approval-single .sign-space {
            height: 55px;
        }

        .approval-single .sign-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 180px;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .page-footer {
            text-align: center;
            font-size: 9pt;
            color: #666;
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
        }

        /* ============================================
           PRINT ADJUSTMENTS
           ============================================ */
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

        /* ============================================
           PRINT BUTTON (screen only)
           ============================================ */
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
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- Print Button -->
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak Laporan</button>

    <!-- ============================================
         HALAMAN 1
         ============================================ -->
    <div class="page">

        <!-- KOP SURAT -->
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

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title">
            <h1>Laporan Hasil Perjalanan Dinas Luar Kota</h1>
            <div class="subtitle">Dasar Perjalanan Dinas</div>
            <div class="surat-tugas">Surat Tugas Nomor [nomor surat tugas]</div>
            <div class="surat-tugas">Tanggal [tanggal surat tugas]</div>
        </div>

        <!-- INFORMASI PENTING -->
        <div class="info-box">
            <p class="info-title">Informasi penting:</p>
            <ol>
                <li>LHP ini digunakan sebagai pertanggungjawaban dinas luar kota;</li>
                <li>LHP ini dibuat secara pribadi, <strong>DILARANG KERAS</strong> untuk mencopy LHP karyawan lain
                    walaupun untuk surat tugasnya sama.</li>
            </ol>
        </div>

        <!-- I. IDENTITAS PERJALANAN DINAS -->
        <div class="section-heading">
            <span class="section-num">I.</span>Identitas Perjalanan Dinas
        </div>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td class="value">[nama]</td>
            </tr>
            <tr>
                <td class="label"><i>Job Position</i></td>
                <td class="separator">:</td>
                <td class="value">[jabatan]</td>
            </tr>
            <tr>
                <td class="label">Divisi</td>
                <td class="separator">:</td>
                <td class="value">[divisi]</td>
            </tr>
            <tr>
                <td class="label">Tujuan Perjalanan Dinas</td>
                <td class="separator">:</td>
                <td class="value">[tujuan]</td>
            </tr>
            <tr>
                <td class="label">Kota Tujuan</td>
                <td class="separator">:</td>
                <td class="value">[kota]</td>
            </tr>
            <tr>
                <td class="label">Tanggal Keberangkatan</td>
                <td class="separator">:</td>
                <td class="value">[tanggal berangkat]</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kepulangan</td>
                <td class="separator">:</td>
                <td class="value">[tanggal pulang]</td>
            </tr>
        </table>

        <!-- II. MAKSUD DAN TUJUAN -->
        <div class="section-heading">
            <span class="section-num">II.</span>Maksud dan Tujuan Perjalanan Dinas
        </div>

        <div class="section-content">
            <p>
                Perjalanan dinas ini dilaksanakan dengan tujuan untuk [tuliskan tujuan perjalanan]
            </p>
        </div>

        <!-- III. PELAKSANAAN KEGIATAN -->
        <div class="section-heading">
            <span class="section-num">III.</span>Pelaksanaan Kegiatan
        </div>

        <div class="section-content">
            <p>Adapun kegiatan yang dilaksanakan selama perjalanan dinas adalah sebagai berikut:</p>
        </div>

        <table class="kegiatan-table">
            <tr>
                <td class="num">1.</td>
                <td class="lbl">Tanggal</td>
                <td class="sep">:</td>
                <td>[tanggal 1]</td>
            </tr>
            <tr>
                <td></td>
                <td class="lbl">Kegiatan</td>
                <td class="sep">:</td>
                <td>[kegiatan 1]</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 8px;"></td>
            </tr>
            <tr>
                <td class="num">2.</td>
                <td class="lbl">Tanggal</td>
                <td class="sep">:</td>
                <td>[tanggal 2]</td>
            </tr>
            <tr>
                <td></td>
                <td class="lbl">Kegiatan</td>
                <td class="sep">:</td>
                <td>[kegiatan 2]</td>
            </tr>
            <tr>
                <td colspan="4" style="height: 8px;"></td>
            </tr>
            <tr>
                <td class="num">3.</td>
                <td class="lbl">Tanggal</td>
                <td class="sep">:</td>
                <td>[tanggal 3]</td>
            </tr>
            <tr>
                <td></td>
                <td class="lbl">Kegiatan</td>
                <td class="sep">:</td>
                <td>[kegiatan 3]</td>
            </tr>
        </table>

        <!-- IV. HASIL YANG DICAPAI -->
        <div class="section-heading">
            <span class="section-num">IV.</span>Hasil yang Dicapai
        </div>

        <div class="section-content">
            <p style="text-indent: 35px;">Hasil yang diperoleh dari pelaksanaan perjalanan dinas ini antara lain:</p>
            <ol>
                <li>[hasil 1]</li>
                <li>[hasil 2]</li>
                <li>[hasil 3]</li>
            </ol>
        </div>

        <!-- V. PERMASALAHAN DAN KENDALA -->
        <div class="section-heading">
            <span class="section-num">V.</span>Permasalahan dan Kendala (Jika Ada)
        </div>

        <div class="section-content">
            <p>
                Selama pelaksanaan perjalanan dinas, [kendala atau tulis "tidak/terdapat kendala/permasalahan."]
            </p>
        </div>

        <!-- Page Footer -->
        <div class="page-footer">
            Halaman 1 dari 2
        </div>
    </div>

    <!-- ============================================
         HALAMAN 2
         ============================================ -->
    <div class="page page-break">

        <!-- KOP SURAT -->
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

        <!-- VI. KESIMPULAN DAN REKOMENDASI -->
        <div class="section-heading">
            <span class="section-num">VI.</span>Kesimpulan dan Rekomendasi
        </div>

        <div class="section-content">
            <p>
                Berdasarkan pelaksanaan perjalanan dinas tersebut, dapat disimpulkan bahwa [kesimpulan]
            </p>
            <p>
                Adapun rekomendasi tindak lanjut yang disarankan adalah:
            </p>
            <ol>
                <li>[rekomendasi]</li>
            </ol>
        </div>

        <!-- VII. DOKUMENTASI -->
        <div class="section-heading">
            <span class="section-num">VII.</span>Dokumentasi
        </div>

        <div class="section-content">
            <p style="font-style: italic; color: #666;">
                (dokumen disusun sesuai urutan kegiatan, dilakukan perkegiatan dan berikan keterangan)
            </p>
            <br>
            <br>
            <br>
        </div>

        <!-- VIII. PENUTUP -->
        <div class="section-heading">
            <span class="section-num">VIII.</span>Penutup
        </div>

        <div class="section-content">
            <p>
                Demikian laporan hasil perjalanan dinas ini disusun sebagai bentuk pertanggungjawaban atas pelaksanaan
                tugas yang telah diberikan.
            </p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-section">

            <!-- Pembuat LHP -->
            <div class="signature-pembuat">
                <div class="location-date">Sleman, [tanggal pulang]</div>
                <div class="role">Pembuat LHP</div>
                <div class="sign-space"></div>
                <div class="sign-line"></div>
            </div>

            <!-- Mengetahui dan Menyetujui -->
            <div class="approval-title">Mengetahui dan menyetujui.</div>

            <div class="approval-grid">
                <!-- Leader -->
                <div class="approval-block">
                    <div class="role-title">Leader</div>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                </div>

                <!-- Manager -->
                <div class="approval-block">
                    <div class="role-title">Manager</div>
                    <div class="manager-label">.....................</div>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                </div>

                <!-- K3 -->
                <div class="approval-block">
                    <div class="role-title">K3</div>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                </div>

                <!-- HRD -->
                <div class="approval-block">
                    <div class="role-title">HRD</div>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                </div>
            </div>

            <!-- Finance (centered) -->
            <div class="approval-single">
                <div class="role-title">Finance</div>
                <div class="sign-space"></div>
                <div class="sign-line"></div>
            </div>

        </div>

        <!-- Page Footer -->
        <div class="page-footer">
            Halaman 2 dari 2
        </div>
    </div>

</body>

</html>