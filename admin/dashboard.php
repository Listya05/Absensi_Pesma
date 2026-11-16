<?php

$roleWajib = 'Admin';

include "../config/session.php";
include "../config/koneksi.php";

include "../includes/header.php";
include "../includes/sidebar.php";
include "../includes/navbar.php";

/*=========================================
AKTIVITAS TERAKHIR
=========================================*/

$qRiwayat = mysqli_query($conn, "
    SELECT
        a.tanggal,
        a.waktu,
        u.nama,
        COUNT(d.id_detail) AS jumlah
    FROM absensi a
    JOIN users u ON u.id_user = a.id_user
    LEFT JOIN detail_absensi d ON d.id_absensi = a.id_absensi
    GROUP BY a.id_absensi
    ORDER BY a.id_absensi DESC
    LIMIT 5
");

/*=========================================
TOTAL MAHASISWI
=========================================*/

$qMahasiswi = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM mahasiswi
    WHERE status = 'Aktif'
");

$totalMahasiswi = mysqli_fetch_assoc($qMahasiswi)['total'];

/*=========================================
TOTAL KAMAR
=========================================*/

$qKamar = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM kamar
");

$totalKamar = mysqli_fetch_assoc($qKamar)['total'];

/*=========================================
ABSENSI TERAKHIR
=========================================*/

$qAbsensi = mysqli_query($conn, "
    SELECT *
    FROM absensi
    ORDER BY id_absensi DESC
    LIMIT 1
");

$absensi = mysqli_fetch_assoc($qAbsensi);

$waktuTerakhir   = "-";
$tanggalTerakhir = "-";
$AlpaTerakhir   = 0;

if ($absensi) {

    $waktuTerakhir = $absensi['waktu'];

    $tanggalTerakhir = date(
        "d F Y",
        strtotime($absensi['tanggal'])
    );

    $qAlpa = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM detail_absensi
        WHERE
        id_absensi='" . $absensi['id_absensi'] . "'
        AND status='Alpa'
    ");

    $AlpaTerakhir = mysqli_fetch_assoc($qAlpa)['total'];

}

/*=========================================
DATA STATISTIK ABSENSI HARI INI
=========================================*/

// Fungsi helper untuk menghitung statistik per waktu & status
function getStatistikAbsensi($conn, $waktu, $status) {
    $query = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM detail_absensi d
        JOIN absensi a ON a.id_absensi = d.id_absensi
        WHERE a.tanggal = CURDATE()
          AND a.waktu = '$waktu'
          AND d.status = '$status'
    ");
    return mysqli_fetch_assoc($query)['total'] ?? 0;
}

// Subuh
$subuhHadir = getStatistikAbsensi($conn, 'Subuh', 'Hadir');
$subuhSakit = getStatistikAbsensi($conn, 'Subuh', 'Sakit');
$subuhIzin  = getStatistikAbsensi($conn, 'Subuh', 'Izin');
$subuhAlpa = getStatistikAbsensi($conn, 'Subuh', 'Alpa');

// Magrib
$magribHadir = getStatistikAbsensi($conn, 'Magrib', 'Hadir');
$magribSakit = getStatistikAbsensi($conn, 'Magrib', 'Sakit');
$magribIzin  = getStatistikAbsensi($conn, 'Magrib', 'Izin');
$magribAlpa = getStatistikAbsensi($conn, 'Magrib', 'Alpa');

// Isya
$isyaHadir = getStatistikAbsensi($conn, 'Isya', 'Hadir');
$isyaSakit = getStatistikAbsensi($conn, 'Isya', 'Sakit');
$isyaIzin  = getStatistikAbsensi($conn, 'Isya', 'Izin');
$isyaAlpa = getStatistikAbsensi($conn, 'Isya', 'Alpa');

?>

<!-- HEADER CLEAN -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
    <p class="text-muted mb-0">
        Selamat Datang, <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?></strong>
    </p>
</div>

<!-- ROW CARD STATISTIK UTAMA -->
<div class="row mb-4">

    <!-- Total Mahasiswi -->
    <div class="col-lg-3 col-md-6 col-6 mb-3 mb-lg-0">
        <div class="card card-dashboard shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="text-muted mb-2">Total Mahasiswi Aktif</h6>
                <h1 class="text-dark fw-bold mb-0"><?= $totalMahasiswi ?></h1>
            </div>
        </div>
    </div>

    <!-- Total Kamar -->
    <div class="col-lg-3 col-md-6 col-6 mb-3 mb-lg-0">
        <div class="card card-dashboard shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="text-muted mb-2">Total Kamar</h6>
                <h1 class="text-dark fw-bold mb-0"><?= $totalKamar ?></h1>
            </div>
        </div>
    </div>

    <!-- Absensi Terakhir -->
    <div class="col-lg-3 col-md-6 col-6 mb-3 mb-lg-0">
        <div class="card card-dashboard shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="text-muted mb-1">Absensi Terakhir</h6>
                <h3 class="text-dark fw-bold mb-0"><?= $waktuTerakhir ?></h3>
                <small class="text-secondary"><?= $tanggalTerakhir ?></small>
            </div>
        </div>
    </div>

    <!-- Alpa Terakhir -->
    <div class="col-lg-3 col-md-6 col-6 mb-3 mb-lg-0">
        <div class="card card-dashboard shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h6 class="text-muted mb-2">Alpa Terakhir</h6>
                <h1 class="text-dark fw-bold mb-0"><?= $AlpaTerakhir ?></h1>
            </div>
        </div>
    </div>

</div>

<!-- GRAFIK & AKTIVITAS -->
<div class="row">

    <!-- GRAFIK ABSENSI HARI INI -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">Grafik Kehadiran Hari Ini</h5>
            </div>
            <div class="card-body">
                <canvas id="chartAbsensi" height="150"></canvas>
            </div>
        </div>
    </div>

    <!-- TABEL AKTIVITAS TERAKHIR -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-success text-white py-3">
                <h5 class="mb-0 fw-bold">Aktivitas Absensi Terakhir</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Shalat</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($qRiwayat) > 0) { ?>
                                <?php while ($r = mysqli_fetch_assoc($qRiwayat)) { ?>
                                <tr>
                                    <td class="ps-3"><?= date("d/m/Y", strtotime($r['tanggal'])) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $r['waktu'] ?></span></td>
                                    <td><?= htmlspecialchars($r['nama']) ?></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada aktivitas.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('chartAbsensi');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Subuh', 'Magrib', 'Isya'],
            datasets: [
                {
                    label: 'Hadir',
                    data: [<?= $subuhHadir ?>, <?= $magribHadir ?>, <?= $isyaHadir ?>],
                    backgroundColor: '#198754', // Hijau
                    borderRadius: 6
                },
                {
                    label: 'Sakit',
                    data: [<?= $subuhSakit ?>, <?= $magribSakit ?>, <?= $isyaSakit ?>],
                    backgroundColor: '#ffc107', // Kuning
                    borderRadius: 6
                },
                {
                    label: 'Izin',
                    data: [<?= $subuhIzin ?>, <?= $magribIzin ?>, <?= $isyaIzin ?>],
                    backgroundColor: '#0dcaf0', // Biru Muda
                    borderRadius: 6
                },
                {
                    label: 'Alpa',
                    data: [<?= $subuhAlpa ?>, <?= $magribAlpa ?>, <?= $isyaAlpa ?>],
                    backgroundColor: '#dc3545', // Merah
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
</script>

<?php

include "../includes/footer.php";

?>