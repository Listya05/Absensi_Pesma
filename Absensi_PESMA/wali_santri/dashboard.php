
<?php
$roleWajib = 'Wali Santri';

include '../config/session.php';
include '../config/koneksi.php';

$tanggalHariIni = date('Y-m-d');

// Total mahasiswi aktif
$qMahasiswi = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM mahasiswi
    WHERE status = 'Aktif'
");
$totalMahasiswi = $qMahasiswi
    ? (int) mysqli_fetch_assoc($qMahasiswi)['total'] : 0;

// Total kamar
$qKamar = mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM kamar
");
$totalKamar = $qKamar
    ? (int) mysqli_fetch_assoc($qKamar)['total'] : 0;

// Absensi hari ini berdasarkan waktu
$waktuAbsensi = ['Subuh', 'Magrib', 'Isya'];
$statistik = [];

foreach ($waktuAbsensi as $waktu) {
    $stmt = mysqli_prepare($conn, "
        SELECT
            COUNT(da.id_detail) AS total_absensi,
            COALESCE(SUM(da.status = 'Hadir'), 0) AS hadir,
            COALESCE(SUM(da.status = 'Sakit'), 0) AS sakit,
            COALESCE(SUM(da.status = 'Izin'), 0) AS izin,
            COALESCE(SUM(da.status = 'Alpa'), 0) AS alpa
        FROM absensi a
        LEFT JOIN detail_absensi da
            ON a.id_absensi = da.id_absensi
        WHERE a.tanggal = ? AND a.waktu = ?
    ");

    mysqli_stmt_bind_param(
        $stmt, 'ss', $tanggalHariIni, $waktu
    );
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $statistik[$waktu] = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);
}

// Total Alpa hari ini
$qAlpa = mysqli_prepare($conn, "
    SELECT COUNT(*) AS total
    FROM detail_absensi da
    JOIN absensi a ON da.id_absensi = a.id_absensi
    WHERE a.tanggal = ? AND da.status = 'Alpa'
");
mysqli_stmt_bind_param($qAlpa, 's', $tanggalHariIni);
mysqli_stmt_execute($qAlpa);
$totalAlpa = (int) mysqli_fetch_assoc(
    mysqli_stmt_get_result($qAlpa)
)['total'];
mysqli_stmt_close($qAlpa);

// Riwayat absensi terbaru
$qRiwayat = mysqli_query($conn, "
    SELECT
        a.tanggal,
        a.waktu,
        u.nama AS petugas,
        COUNT(da.id_detail) AS total_data
    FROM absensi a
    LEFT JOIN users u ON a.id_user = u.id_user
    LEFT JOIN detail_absensi da
        ON a.id_absensi = da.id_absensi
    GROUP BY a.id_absensi, a.tanggal, a.waktu, u.nama
    ORDER BY a.tanggal DESC, a.id_absensi DESC
    LIMIT 5
");

include '../includes/header.php';
include '../includes/sidebar_walisantri.php';
include '../includes/navbar.php';
?>

<div class="container-fluid py-3">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Dashboard Wali Santri</h2>
        <p class="text-muted mb-0">
            Ringkasan data absensi mahasiswi PESMA Al Ma'soem.
        </p>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Mahasiswi Aktif</p>
                    <h2 class="fw-bold text-primary mb-0">
                        <?= $totalMahasiswi ?>
                    </h2>
                    <small class="text-muted">Mahasiswi terdaftar aktif</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Kamar</p>
                    <h2 class="fw-bold text-primary mb-0">
                        <?= $totalKamar ?>
                    </h2>
                    <small class="text-muted">Kamar terdaftar</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Alpa Hari Ini</p>
                    <h2 class="fw-bold text-danger mb-0">
                        <?= $totalAlpa ?>
                    </h2>
                    <small class="text-muted">
                        Akumulasi Subuh, Magrib, dan Isya
                    </small>
                </div>
            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-1">Ringkasan Absensi Hari Ini</h5>
            <p class="text-muted mb-3">
                <?= date('d-m-Y') ?>
            </p>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($waktuAbsensi as $waktu): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($waktu) ?>
                                </td>
                                <td class="text-success fw-bold">
                                    <?= (int) $statistik[$waktu]['hadir'] ?>
                                </td>
                                <td class="text-warning fw-bold">
                                    <?= (int) $statistik[$waktu]['sakit'] ?>
                                </td>
                                <td class="text-info fw-bold">
                                    <?= (int) $statistik[$waktu]['izin'] ?>
                                </td>
                                <td class="text-danger fw-bold">
                                    <?= (int) $statistik[$waktu]['alpa'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Riwayat Absensi Terbaru</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Petugas</th>
                            <th>Jumlah Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($qRiwayat && mysqli_num_rows($qRiwayat) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($r = mysqli_fetch_assoc($qRiwayat)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <?= date(
                                            'd-m-Y',
                                            strtotime($r['tanggal'])
                                        ) ?>
                                    </td>
                                    <td><?= htmlspecialchars($r['waktu']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            $r['petugas'] ?? '-'
                                        ) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= (int) $r['total_data'] ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada riwayat absensi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
