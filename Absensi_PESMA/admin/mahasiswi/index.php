
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";

$keyword = isset($_GET['keyword'])
    ? mysqli_real_escape_string($conn, trim($_GET['keyword']))
    : "";

?>

<!-- Header -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Data Mahasiswi</h2>
    <p class="text-muted mb-0">
        Daftar Mahasiswi PESMA Al-Ma'soem
    </p>
</div>

<!-- Pencarian dan Tombol Tambah -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <form method="GET" id="formSearch">
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">

                <div class="flex-grow-1" style="max-width: 350px;">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="Cari nama, prodi, kamar, atau status..."
                        value="<?= htmlspecialchars($keyword); ?>"
                    >
                </div>

                <a href="tambah.php"
                   class="btn btn-primary text-nowrap rounded-3 px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i>
                    Tambah Mahasiswi
                </a>

            </div>
        </form>

    </div>
</div>

<!-- Tabel Data Mahasiswi -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-primary text-center">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th width="90">Angkatan</th>
                        <th>No HP</th>
                        <th width="90">Kamar</th>
                        <th width="100">Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php

                    $no = 1;

                    // Mengambil data langsung dari tabel mahasiswi
                    $query_sql = "
                        SELECT
                            mahasiswi.*,
                            kamar.nomor_kamar
                        FROM mahasiswi
                        LEFT JOIN kamar
                            ON kamar.id_kamar = mahasiswi.id_kamar
                    ";

                    // Pencarian data
                    if ($keyword !== "") {
                        $query_sql .= "
                            WHERE
                                mahasiswi.nama LIKE '%$keyword%'
                                OR mahasiswi.prodi LIKE '%$keyword%'
                                OR kamar.nomor_kamar LIKE '%$keyword%'
                                OR mahasiswi.status LIKE '%$keyword%'
                        ";
                    }

                    // Mahasiswi aktif ditampilkan terlebih dahulu
                    $query_sql .= "
                        ORDER BY
                            CASE
                                WHEN mahasiswi.status = 'Aktif' THEN 1
                                ELSE 2
                            END,
                            mahasiswi.nama ASC
                    ";

                    $query = mysqli_query($conn, $query_sql);

                    if (!$query) {
                    ?>

                        <tr>
                            <td colspan="8" class="text-center text-danger py-4">
                                Gagal mengambil data mahasiswi.
                            </td>
                        </tr>

                    <?php
                    } elseif (mysqli_num_rows($query) > 0) {

                        while ($d = mysqli_fetch_assoc($query)) {
                    ?>

                        <tr class="<?= ($d['status'] == 'Nonaktif')
                            ? 'table-secondary opacity-75'
                            : ''; ?>">

                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($d['nama'] ?? '-'); ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['prodi'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($d['angkatan'] ?? '-'); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($d['no_hp'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($d['nomor_kamar'] ?? '-'); ?>
                            </td>

                            <td class="text-center">
                                <?php if ($d['status'] == 'Aktif') { ?>

                                    <span class="badge bg-success">
                                        Aktif
                                    </span>

                                <?php } else { ?>

                                    <span class="badge bg-secondary">
                                        Nonaktif
                                    </span>

                                <?php } ?>
                            </td>

                            <td class="text-center text-nowrap">

                                <a
                                    href="edit.php?id=<?= (int) $d['id_mahasiswi']; ?>"
                                    class="btn btn-warning btn-sm mb-1">
                                    Edit
                                </a>

                                <a
                                    href="hapus.php?id=<?= (int) $d['id_mahasiswi']; ?>"
                                    class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus mahasiswi ini? Pastikan riwayat absensinya tidak diperlukan lagi.');">
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php
                        }

                    } else {
                    ?>

                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Data mahasiswi tidak ditemukan.
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
