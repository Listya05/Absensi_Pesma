<?php

$roleWajib = 'Wali Santri';

include '../../config/session.php';
include '../../config/koneksi.php';

include '../../includes/header.php';
include '../../includes/sidebar_walisantri.php';
include '../../includes/navbar.php';

// =======================
// FILTER PERIODE & LANTAI
// =======================
$tipe    = isset($_GET['tipe']) ? $_GET['tipe'] : 'harian';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$minggu  = isset($_GET['minggu']) ? $_GET['minggu'] : date('Y-\WW');
$bulan   = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$lantai  = isset($_GET['lantai']) ? $_GET['lantai'] : '';

// LOGIKA WHERE DYNAMIC
$where = " WHERE 1=1 ";

if ($tipe == 'harian') {
    $where .= " AND absensi.tanggal = '$tanggal' ";
} elseif ($tipe == 'mingguan' && !empty($minggu)) {
    $dto = new DateTime();
    $year = substr($minggu, 0, 4);
    $week = substr($minggu, 6, 2);
    $dto->setISODate($year, $week);
    $start_date = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $end_date = $dto->format('Y-m-d');

    $where .= " AND absensi.tanggal BETWEEN '$start_date' AND '$end_date' ";
} elseif ($tipe == 'bulanan' && !empty($bulan)) {
    $where .= " AND DATE_FORMAT(absensi.tanggal, '%Y-%m') = '$bulan' ";
}

if (!empty($lantai)) {
    $where .= " AND kamar.lantai = '$lantai' ";
}

// PARAMETER URL UNTUK TOMBOL CETAK
$params_cetak = "tipe=$tipe&tanggal=$tanggal&minggu=$minggu&bulan=$bulan&lantai=$lantai";
?>

<!-- HEADER CLEAN -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Rekap Absensi Mahasiswi</h2>
    <p class="text-muted mb-0">
        Rekapitulasi Kehadiran Mahasiswi Asrama Al-Ma'soem
    </p>
</div>

<!-- SINGLE CARD CONTAINER (FILTER + TABEL) -->
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body p-4">

        <!-- SECTION FORM FILTER & TOMBOL CETAK SEJAJAR -->
        <form method="GET" id="formFilter" class="mb-4">
            <div class="row g-3 align-items-end">

                <!-- Tipe Periode -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Periode</label>
                    <select name="tipe" class="form-select rounded-3" onchange="togglePeriodeInput(this.value); this.form.submit();">
                        <option value="harian" <?= $tipe == 'harian' ? 'selected' : '' ?>>Harian</option>
                        <option value="mingguan" <?= $tipe == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                        <option value="bulanan" <?= $tipe == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                    </select>
                </div>

                <!-- Input Tanggal Dynamic -->
                <div class="col-md-3" id="inputHarian" style="<?= $tipe != 'harian' ? 'display:none;' : '' ?>">
                    <label class="form-label fw-bold">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control rounded-3" value="<?= $tanggal ?>" onchange="this.form.submit()">
                </div>

                <div class="col-md-3" id="inputMingguan" style="<?= $tipe != 'mingguan' ? 'display:none;' : '' ?>">
                    <label class="form-label fw-bold">Pilih Pekan/Minggu</label>
                    <input type="week" name="minggu" class="form-control rounded-3" value="<?= $minggu ?>" onchange="this.form.submit()">
                </div>

                <div class="col-md-3" id="inputBulanan" style="<?= $tipe != 'bulanan' ? 'display:none;' : '' ?>">
                    <label class="form-label fw-bold">Pilih Bulan</label>
                    <input type="month" name="bulan" class="form-control rounded-3" value="<?= $bulan ?>" onchange="this.form.submit()">
                </div>

                <!-- Filter Lantai -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Lantai Kamar</label>
                    <select name="lantai" class="form-select rounded-3" onchange="this.form.submit()">
                        <option value="">Semua Lantai</option>
                        <option value="2" <?= $lantai == '2' ? 'selected' : '' ?>>Lantai 2</option>
                        <option value="3" <?= $lantai == '3' ? 'selected' : '' ?>>Lantai 3</option>
                    </select>
                </div>

                <!-- Tombol Cetak PDF Sejajar -->
                <div class="col-md-3 text-end">
                    <a href="cetak_rekap.php?<?= $params_cetak ?>" target="_blank" class="btn btn-danger rounded-3 fw-semibold shadow-sm w-100 py-2">
                        <i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF
                    </a>
                </div>

            </div>
        </form>

        <!-- GARIS PEMISAH -->
        <hr class="text-muted opacity-25 my-4">

        <!-- SECTION TABEL REKAPITULASI -->
        <?php
        $query_rekap = "
            SELECT
                mahasiswi.nama,
                kamar.nomor_kamar,
                
                /* SUBUH */
                SUM(CASE WHEN absensi.waktu='Subuh' AND detail_absensi.status='Hadir' THEN 1 ELSE 0 END) AS subuh_hadir,
                SUM(CASE WHEN absensi.waktu='Subuh' AND detail_absensi.status='Sakit' THEN 1 ELSE 0 END) AS subuh_sakit,
                SUM(CASE WHEN absensi.waktu='Subuh' AND detail_absensi.status='Izin' THEN 1 ELSE 0 END) AS subuh_izin,
                SUM(CASE WHEN absensi.waktu='Subuh' AND (detail_absensi.status='Alpha' OR detail_absensi.status='Tanpa Keterangan') THEN 1 ELSE 0 END) AS subuh_alpha,

                /* MAGRIB */
                SUM(CASE WHEN absensi.waktu='Magrib' AND detail_absensi.status='Hadir' THEN 1 ELSE 0 END) AS magrib_hadir,
                SUM(CASE WHEN absensi.waktu='Magrib' AND detail_absensi.status='Sakit' THEN 1 ELSE 0 END) AS magrib_sakit,
                SUM(CASE WHEN absensi.waktu='Magrib' AND detail_absensi.status='Izin' THEN 1 ELSE 0 END) AS magrib_izin,
                SUM(CASE WHEN absensi.waktu='Magrib' AND (detail_absensi.status='Alpha' OR detail_absensi.status='Tanpa Keterangan') THEN 1 ELSE 0 END) AS magrib_alpha,

                /* ISYA */
                SUM(CASE WHEN absensi.waktu='Isya' AND detail_absensi.status='Hadir' THEN 1 ELSE 0 END) AS isya_hadir,
                SUM(CASE WHEN absensi.waktu='Isya' AND detail_absensi.status='Sakit' THEN 1 ELSE 0 END) AS isya_sakit,
                SUM(CASE WHEN absensi.waktu='Isya' AND detail_absensi.status='Izin' THEN 1 ELSE 0 END) AS isya_izin,
                SUM(CASE WHEN absensi.waktu='Isya' AND (detail_absensi.status='Alpha' OR detail_absensi.status='Tanpa Keterangan') THEN 1 ELSE 0 END) AS isya_alpha

            FROM mahasiswi
            LEFT JOIN kamar ON mahasiswi.id_kamar = kamar.id_kamar
            LEFT JOIN detail_absensi ON mahasiswi.id_mahasiswi = detail_absensi.id_mahasiswi
            LEFT JOIN absensi ON detail_absensi.id_absensi = absensi.id_absensi
            $where
            GROUP BY mahasiswi.id_mahasiswi
            ORDER BY mahasiswi.nama ASC
        ";
        $rekap = mysqli_query($conn, $query_rekap);
        $no = 1;
        ?>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center mb-0" style="font-size: 14px;">
                <thead class="table-primary align-middle">
                    <tr>
                        <th rowspan="2" width="40">NO.</th>
                        <th rowspan="2">NAMA</th>
                        <th rowspan="2" width="90">KAMAR</th>
                        <th colspan="4">SUBUH</th>
                        <th colspan="4">MAGRIB</th>
                        <th colspan="4">ISYA</th>
                    </tr>
                    <tr>
                        <th class="text-success">HADIR</th>
                        <th class="text-warning">SAKIT</th>
                        <th class="text-info">IZIN</th>
                        <th class="text-danger">ALPHA</th>

                        <th class="text-success">HADIR</th>
                        <th class="text-warning">SAKIT</th>
                        <th class="text-info">IZIN</th>
                        <th class="text-danger">ALPHA</th>

                        <th class="text-success">HADIR</th>
                        <th class="text-warning">SAKIT</th>
                        <th class="text-info">IZIN</th>
                        <th class="text-danger">ALPHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rekap && mysqli_num_rows($rekap) > 0): ?>
                        <?php while ($r = mysqli_fetch_assoc($rekap)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="text-start fw-bold"><?= htmlspecialchars($r['nama']); ?></td>
                                <td><?= htmlspecialchars($r['nomor_kamar']); ?></td>

                                <td class="text-success fw-bold"><?= $r['subuh_hadir']; ?></td>
                                <td class="text-warning fw-bold"><?= $r['subuh_sakit']; ?></td>
                                <td class="text-info fw-bold"><?= $r['subuh_izin']; ?></td>
                                <td class="text-danger fw-bold"><?= $r['subuh_alpha']; ?></td>

                                <td class="text-success fw-bold"><?= $r['magrib_hadir']; ?></td>
                                <td class="text-warning fw-bold"><?= $r['magrib_sakit']; ?></td>
                                <td class="text-info fw-bold"><?= $r['magrib_izin']; ?></td>
                                <td class="text-danger fw-bold"><?= $r['magrib_alpha']; ?></td>

                                <td class="text-success fw-bold"><?= $r['isya_hadir']; ?></td>
                                <td class="text-warning fw-bold"><?= $r['isya_sakit']; ?></td>
                                <td class="text-info fw-bold"><?= $r['isya_izin']; ?></td>
                                <td class="text-danger fw-bold"><?= $r['isya_alpha']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="15" class="text-muted py-4">Data tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function togglePeriodeInput(val) {
    document.getElementById('inputHarian').style.display = (val === 'harian') ? 'block' : 'none';
    document.getElementById('inputMingguan').style.display = (val === 'mingguan') ? 'block' : 'none';
    document.getElementById('inputBulanan').style.display = (val === 'bulanan') ? 'block' : 'none';
}
</script>

<?php
include '../../includes/footer.php';
?>