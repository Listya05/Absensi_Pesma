
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";

$keyword = isset($_GET['keyword'])
    ? trim($_GET['keyword'])
    : '';

$lantai = isset($_GET['lantai'])
    ? $_GET['lantai']
    : '';

// Validasi filter lantai
if (!in_array($lantai, ['', '2', '3'], true)) {
    $lantai = '';
}

// Escape input pencarian
$keywordSQL = mysqli_real_escape_string($conn, $keyword);


// ==============================
// STATISTIK KAMAR
// ==============================

// Total kamar
$q_total_kamar = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM kamar
");

$total_kamar = mysqli_fetch_assoc($q_total_kamar)['total'] ?? 0;


// Total kapasitas
$q_total_kapasitas = mysqli_query($conn, "
    SELECT COALESCE(SUM(kapasitas), 0) AS total
    FROM kamar
");

$total_kapasitas = mysqli_fetch_assoc($q_total_kapasitas)['total'] ?? 0;


// Kamar terisi oleh minimal satu mahasiswi aktif
$q_kamar_terisi = mysqli_query($conn, "
    SELECT COUNT(DISTINCT id_kamar) AS total
    FROM mahasiswi
    WHERE id_kamar IS NOT NULL
      AND id_kamar != 0
      AND status = 'Aktif'
");

$kamar_terisi = mysqli_fetch_assoc($q_kamar_terisi)['total'] ?? 0;


// Kamar kosong
$kamar_kosong = max(0, $total_kamar - $kamar_terisi);

?>

<!-- Header -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Data Kamar</h2>
    <p class="text-muted mb-0">
        Daftar Kamar PESMA Al-Ma'soem
    </p>
</div>


<!-- Statistik Kamar -->
<div class="row g-3 mb-4">

    <!-- Total Kamar -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-md-3 p-3">

                <div class="bg-primary-subtle text-primary rounded-4
                    d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:48px;height:48px;">
                    <i class="fa-solid fa-house fs-5"></i>
                </div>

                <div>
                    <span class="text-muted small fw-semibold d-block">
                        Total Kamar
                    </span>
                    <h3 class="fw-bold mb-0 text-dark">
                        <?= (int) $total_kamar; ?>
                    </h3>
                </div>

            </div>
        </div>
    </div>


    <!-- Kapasitas Total -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-md-3 p-3">

                <div class="bg-warning-subtle text-warning rounded-4
                    d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:48px;height:48px;">
                    <i class="fa-solid fa-users fs-5"></i>
                </div>

                <div>
                    <span class="text-muted small fw-semibold d-block">
                        Kapasitas Total
                    </span>
                    <h3 class="fw-bold mb-0 text-dark">
                        <?= (int) $total_kapasitas; ?>
                    </h3>
                </div>

            </div>
        </div>
    </div>


    <!-- Kamar Terisi -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-md-3 p-3">

                <div class="bg-info-subtle text-info rounded-4
                    d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:48px;height:48px;">
                    <i class="fa-solid fa-user-check fs-5"></i>
                </div>

                <div>
                    <span class="text-muted small fw-semibold d-block">
                        Kamar Terisi
                    </span>
                    <h3 class="fw-bold mb-0 text-dark">
                        <?= (int) $kamar_terisi; ?>
                    </h3>
                </div>

            </div>
        </div>
    </div>


    <!-- Kamar Kosong -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-md-3 p-3">

                <div class="bg-danger-subtle text-danger rounded-4
                    d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:48px;height:48px;">
                    <i class="fa-solid fa-door-open fs-5"></i>
                </div>

                <div>
                    <span class="text-muted small fw-semibold d-block">
                        Kamar Kosong
                    </span>
                    <h3 class="fw-bold mb-0 text-dark">
                        <?= (int) $kamar_kosong; ?>
                    </h3>
                </div>

            </div>
        </div>
    </div>

</div>


<!-- Filter Lantai -->
<div class="d-flex gap-2 mb-3 flex-wrap">

    <a
        href="index.php?keyword=<?= urlencode($keyword); ?>"
        class="btn rounded-pill px-4
        <?= $lantai === '' ? 'btn-primary fw-bold' : 'btn-light border text-muted'; ?>">
        Semua Lantai
    </a>

    <a
        href="index.php?lantai=2&keyword=<?= urlencode($keyword); ?>"
        class="btn rounded-pill px-4
        <?= $lantai === '2' ? 'btn-primary fw-bold' : 'btn-light border text-muted'; ?>">
        Lantai 2
    </a>

    <a
        href="index.php?lantai=3&keyword=<?= urlencode($keyword); ?>"
        class="btn rounded-pill px-4
        <?= $lantai === '3' ? 'btn-primary fw-bold' : 'btn-light border text-muted'; ?>">
        Lantai 3
    </a>

</div>


<!-- Pencarian dan Tambah Kamar -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">

        <form method="GET" id="formSearch">

            <input
                type="hidden"
                name="lantai"
                value="<?= htmlspecialchars($lantai); ?>">

            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">

                <div class="flex-grow-1" style="max-width:350px;">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="Cari nomor kamar..."
                        value="<?= htmlspecialchars($keyword); ?>">
                </div>

                <a
                    href="tambah.php"
                    class="btn btn-primary text-nowrap rounded-3 px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i>
                    Tambah Kamar
                </a>

            </div>

        </form>

    </div>
</div>


<!-- Tabel Data Kamar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-primary text-center">
                    <tr>
                        <th width="60">No</th>
                        <th>Nomor Kamar</th>
                        <th width="120">Lantai</th>
                        <th width="120">Kapasitas</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php

                    $no = 1;

                    $where = "WHERE 1=1";

                    // Filter nomor kamar
                    if ($keyword !== '') {
                        $where .= "
                            AND nomor_kamar LIKE '%$keywordSQL%'
                        ";
                    }

                    // Filter lantai
                    if ($lantai !== '') {
                        $where .= "
                            AND lantai = " . (int) $lantai . "
                        ";
                    }

                    $query_kamar = mysqli_query($conn, "
                        SELECT *
                        FROM kamar
                        $where
                        ORDER BY nomor_kamar ASC
                    ");

                    if (!$query_kamar) {
                    ?>

                        <tr>
                            <td colspan="5" class="text-center text-danger py-3">
                                Gagal mengambil data kamar.
                            </td>
                        </tr>

                    <?php
                    } elseif (mysqli_num_rows($query_kamar) > 0) {

                        while ($d = mysqli_fetch_assoc($query_kamar)) {
                    ?>

                        <tr>

                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($d['nomor_kamar']); ?>
                                </strong>
                            </td>

                            <td class="text-center">
                                Lantai <?= htmlspecialchars($d['lantai']); ?>
                            </td>

                            <td class="text-center">
                                <?= (int) $d['kapasitas']; ?> Orang
                            </td>

                            <td class="text-center text-nowrap">

                                <a
                                    href="edit.php?id=<?= (int) $d['id_kamar']; ?>"
                                    class="btn btn-warning btn-sm mb-1">
                                    Edit
                                </a>

                                <a
                                    href="hapus.php?id=<?= (int) $d['id_kamar']; ?>"
                                    class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus kamar ini? Pastikan kamar tidak digunakan oleh mahasiswi.');">
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php
                        }

                    } else {
                    ?>

                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Data kamar tidak ditemukan.
                            </td>
                        </tr>

                    <?php
                    }
                    ?>

                </tbody>

            </table>

        </div>

    </div>
</div>


<!-- Pencarian Otomatis -->
<script>
    const inputKeyword = document.querySelector('input[name="keyword"]');
    const formSearch = document.getElementById('formSearch');

    let timer;

    inputKeyword.addEventListener('input', function () {
        clearTimeout(timer);

        timer = setTimeout(function () {
            formSearch.submit();
        }, 400);
    });
</script>

<?php include "../../includes/footer.php"; ?>
