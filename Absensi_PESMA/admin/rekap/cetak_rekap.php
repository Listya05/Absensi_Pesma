
<?php

$roleWajib = 'Admin';

include '../../config/session.php';
include '../../config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');

// =====================================
// FILTER PERIODE DAN LANTAI
// =====================================
$tipe    = $_GET['tipe'] ?? 'harian';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$minggu  = $_GET['minggu'] ?? date('o-\WW');
$bulan   = $_GET['bulan'] ?? date('Y-m');
$lantai  = $_GET['lantai'] ?? '';

if (!in_array($tipe, ['harian', 'mingguan', 'bulanan'], true)) {
    $tipe = 'harian';
}

$where = " WHERE 1=1 ";

$week = (int) date('W');
$year = (int) date('o');

// Filter harian
if ($tipe === 'harian') {
    $tanggalValid = DateTime::createFromFormat('!Y-m-d', $tanggal);

    if (!$tanggalValid || $tanggalValid->format('Y-m-d') !== $tanggal) {
        $tanggal = date('Y-m-d');
    }

    $tanggalDB = mysqli_real_escape_string($conn, $tanggal);
    $where .= " AND absensi.tanggal = '$tanggalDB' ";
}

// Filter mingguan
elseif ($tipe === 'mingguan') {
    if (preg_match('/^(\d{4})-W(\d{2})$/', $minggu, $cocok)) {
        $year = (int) $cocok[1];
        $week = (int) $cocok[2];

        if ($week >= 1 && $week <= 53) {
            $dto = new DateTime();
            $dto->setISODate($year, $week, 1);

            $start_date = $dto->format('Y-m-d');

            $dto->modify('+6 days');
            $end_date = $dto->format('Y-m-d');

            $where .= " AND absensi.tanggal BETWEEN '$start_date' AND '$end_date' ";
        } else {
            $dto = new DateTime();
            $dto->setISODate((int) date('o'), (int) date('W'), 1);

            $start_date = $dto->format('Y-m-d');
            $dto->modify('+6 days');
            $end_date = $dto->format('Y-m-d');

            $week = (int) date('W');
            $year = (int) date('o');

            $where .= " AND absensi.tanggal BETWEEN '$start_date' AND '$end_date' ";
        }
    } else {
        $dto = new DateTime();
        $dto->setISODate((int) date('o'), (int) date('W'), 1);

        $start_date = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $end_date = $dto->format('Y-m-d');

        $week = (int) date('W');
        $year = (int) date('o');

        $where .= " AND absensi.tanggal BETWEEN '$start_date' AND '$end_date' ";
    }
}

// Filter bulanan
elseif ($tipe === 'bulanan') {
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan)) {
        $bulan = date('Y-m');
    }

    $bulanDB = mysqli_real_escape_string($conn, $bulan);
    $where .= " AND DATE_FORMAT(absensi.tanggal, '%Y-%m') = '$bulanDB' ";
}

// Filter lantai
if ($lantai !== '' && in_array((string) $lantai, ['2', '3'], true)) {
    $lantaiDB = mysqli_real_escape_string($conn, $lantai);
    $where .= " AND kamar.lantai = '$lantaiDB' ";
} else {
    $lantai = '';
}

// =====================================
// QUERY REKAP ABSENSI
// =====================================
$query_rekap = "
    SELECT
        kamar.nomor_kamar,
        mahasiswi.nama,

        SUM(CASE
            WHEN absensi.waktu = 'Subuh'
            AND detail_absensi.status = 'Hadir'
            THEN 1 ELSE 0
        END) AS subuh_hadir,

        SUM(CASE
            WHEN absensi.waktu = 'Subuh'
            AND detail_absensi.status = 'Sakit'
            THEN 1 ELSE 0
        END) AS subuh_sakit,

        SUM(CASE
            WHEN absensi.waktu = 'Subuh'
            AND detail_absensi.status = 'Izin'
            THEN 1 ELSE 0
        END) AS subuh_izin,

        SUM(CASE
            WHEN absensi.waktu = 'Subuh'
            AND detail_absensi.status IN ('Alpa', 'Alpha', 'Tanpa Keterangan')
            THEN 1 ELSE 0
        END) AS subuh_alpha,

        SUM(CASE
            WHEN absensi.waktu = 'Magrib'
            AND detail_absensi.status = 'Hadir'
            THEN 1 ELSE 0
        END) AS magrib_hadir,

        SUM(CASE
            WHEN absensi.waktu = 'Magrib'
            AND detail_absensi.status = 'Sakit'
            THEN 1 ELSE 0
        END) AS magrib_sakit,

        SUM(CASE
            WHEN absensi.waktu = 'Magrib'
            AND detail_absensi.status = 'Izin'
            THEN 1 ELSE 0
        END) AS magrib_izin,

        SUM(CASE
            WHEN absensi.waktu = 'Magrib'
            AND detail_absensi.status IN ('Alpa', 'Alpha', 'Tanpa Keterangan')
            THEN 1 ELSE 0
        END) AS magrib_alpha,

        SUM(CASE
            WHEN absensi.waktu = 'Isya'
            AND detail_absensi.status = 'Hadir'
            THEN 1 ELSE 0
        END) AS isya_hadir,

        SUM(CASE
            WHEN absensi.waktu = 'Isya'
            AND detail_absensi.status = 'Sakit'
            THEN 1 ELSE 0
        END) AS isya_sakit,

        SUM(CASE
            WHEN absensi.waktu = 'Isya'
            AND detail_absensi.status = 'Izin'
            THEN 1 ELSE 0
        END) AS isya_izin,

        SUM(CASE
            WHEN absensi.waktu = 'Isya'
            AND detail_absensi.status IN ('Alpa', 'Alpha', 'Tanpa Keterangan')
            THEN 1 ELSE 0
        END) AS isya_alpha

    FROM mahasiswi

    LEFT JOIN kamar
        ON mahasiswi.id_kamar = kamar.id_kamar

    LEFT JOIN detail_absensi
        ON mahasiswi.id_mahasiswi = detail_absensi.id_mahasiswi

    LEFT JOIN absensi
        ON detail_absensi.id_absensi = absensi.id_absensi

    $where

    GROUP BY
        mahasiswi.id_mahasiswi,
        mahasiswi.nama,
        kamar.nomor_kamar

    ORDER BY mahasiswi.nama ASC
";

$rekap = mysqli_query($conn, $query_rekap);

if (!$rekap) {
    die('Terjadi kesalahan saat mengambil data rekap absensi.');
}

// =====================================
// FORMAT TANGGAL INDONESIA
// =====================================
$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$tanggal_cetak = date('d') . ' ' .
    $nama_bulan[(int) date('n')] . ' ' .
    date('Y');

// Format periode
if ($tipe === 'harian') {
    $timestamp = strtotime($tanggal);

    $periode = date('d', $timestamp) . ' ' .
        $nama_bulan[(int) date('n', $timestamp)] . ' ' .
        date('Y', $timestamp);

} elseif ($tipe === 'mingguan') {
    $periode = 'Minggu ke-' . $week . ' Tahun ' . $year;

} else {
    $bulanAngka = (int) substr($bulan, 5, 2);
    $tahunAngka = substr($bulan, 0, 4);

    $periode = $nama_bulan[$bulanAngka] . ' ' . $tahunAngka;
}

$no = 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekapitulasi Kehadiran Mahasiswi</title>

    <style>
        @page {
            size: landscape;
            margin: 12mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: #fff;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header-print {
            text-align: center;
            margin: 10px 0 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header-print h3 {
            font-size: 18px;
            margin: 0 0 4px;
            font-weight: bold;
        }

        .header-print h5 {
            font-size: 14px;
            margin: 0 0 5px;
            font-weight: normal;
        }

        .periode {
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #bfc5cc;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #f1f3f5;
            font-weight: bold;
        }

        td.nama {
            text-align: left;
            font-weight: bold;
        }

        .ttd {
            width: 250px;
            margin: 35px 20px 0 auto;
            text-align: center;
            font-size: 12px;
            page-break-inside: avoid;
        }

        .ttd p {
            margin: 0 0 8px;
        }

        .ttd .jabatan {
            margin-top: 20px;
        }

        .ttd .nama-penanda-tangan {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead {
                display: table-header-group;
            }

            tr,
            .ttd {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <!-- Kop laporan tanpa judul tambahan -->
    <div class="header-print">
        <h3>PESANTREN MAHASISWA AL MA'SOEM</h3>
        <h5>REKAPITULASI KEHADIRAN MAHASISWI</h5>

        <div class="periode">
            Periode: <?= htmlspecialchars(ucfirst($tipe)); ?>
            (<?= htmlspecialchars($periode); ?>)
            |
            <?= $lantai !== ''
                ? 'Lantai ' . htmlspecialchars($lantai)
                : 'Semua Lantai'; ?>
        </div>
    </div>

    <!-- Tabel rekap tanpa kolom keterangan -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">NO.</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2" style="width: 70px;">KAMAR</th>
                <th colspan="4">SUBUH</th>
                <th colspan="4">MAGRIB</th>
                <th colspan="4">ISYA</th>
            </tr>
            <tr>
                <th>HADIR</th>
                <th>SAKIT</th>
                <th>IZIN</th>
                <th>ALPA</th>

                <th>HADIR</th>
                <th>SAKIT</th>
                <th>IZIN</th>
                <th>ALPA</th>

                <th>HADIR</th>
                <th>SAKIT</th>
                <th>IZIN</th>
                <th>ALPA</th>
            </tr>
        </thead>

        <tbody>
            <?php if (mysqli_num_rows($rekap) > 0): ?>

                <?php while ($r = mysqli_fetch_assoc($rekap)): ?>
                    <tr>
                        <td><?= $no++; ?></td>

                        <td class="nama">
                            <?= htmlspecialchars($r['nama'] ?? '-'); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($r['nomor_kamar'] ?? '-'); ?>
                        </td>

                        <td><?= (int) $r['subuh_hadir']; ?></td>
                        <td><?= (int) $r['subuh_sakit']; ?></td>
                        <td><?= (int) $r['subuh_izin']; ?></td>
                        <td><?= (int) $r['subuh_alpha']; ?></td>

                        <td><?= (int) $r['magrib_hadir']; ?></td>
                        <td><?= (int) $r['magrib_sakit']; ?></td>
                        <td><?= (int) $r['magrib_izin']; ?></td>
                        <td><?= (int) $r['magrib_alpha']; ?></td>

                        <td><?= (int) $r['isya_hadir']; ?></td>
                        <td><?= (int) $r['isya_sakit']; ?></td>
                        <td><?= (int) $r['isya_izin']; ?></td>
                        <td><?= (int) $r['isya_alpha']; ?></td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr>
                    <td colspan="15">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda tangan dengan tanggal otomatis -->
    <div class="ttd">
        <p>Jatinangor, <?= htmlspecialchars($tanggal_cetak); ?></p>
        <p>Mengetahui,</p>

        <p class="jabatan">Kepala Pesantren</p>

        <br><br><br>

        <p class="nama-penanda-tangan">Asep Abdul Halim, S.Sos. I., M.M.</p>
    </div>

    <!-- Cetak otomatis -->
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>

</body>
</html>
