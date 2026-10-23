
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Pastikan ID mahasiswi tersedia
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data mahasiswi beserta nomor kamar
$stmt = mysqli_prepare(
    $conn,
    "SELECT mahasiswi.*, kamar.nomor_kamar
     FROM mahasiswi
     LEFT JOIN kamar ON kamar.id_kamar = mahasiswi.id_kamar
     WHERE mahasiswi.id_mahasiswi = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

// Jika data tidak ditemukan
if (!$d) {
    echo "<script>
        alert('Data mahasiswi tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";
?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-warning text-dark py-3 rounded-top-4">
        <h4 class="mb-0 fw-bold fs-5">
            <i class="fa-solid fa-user-pen me-2"></i>
            Edit Data Mahasiswi
        </h4>
    </div>

    <div class="card-body p-4">
        <form action="proses_edit.php" method="POST">

            <input
                type="hidden"
                name="id_mahasiswi"
                value="<?= (int) $d['id_mahasiswi']; ?>">

            <!-- Nama Mahasiswi -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nama Mahasiswi
                </label>
                <input
                    type="text"
                    name="nama"
                    class="form-control"
                    value="<?= htmlspecialchars($d['nama'] ?? ''); ?>"
                    placeholder="Masukkan nama lengkap"
                    required>
            </div>

            <!-- Program Studi -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Program Studi
                </label>
                <input
                    type="text"
                    name="prodi"
                    class="form-control"
                    value="<?= htmlspecialchars($d['prodi'] ?? ''); ?>"
                    placeholder="Masukkan program studi"
                    required>
            </div>

            <!-- Angkatan -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Angkatan
                </label>
                <input
                    type="number"
                    name="angkatan"
                    class="form-control"
                    value="<?= htmlspecialchars($d['angkatan'] ?? ''); ?>"
                    placeholder="Contoh: 2024">
            </div>

            <!-- Nomor HP -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nomor HP
                </label>
                <input
                    type="text"
                    name="no_hp"
                    class="form-control"
                    value="<?= htmlspecialchars($d['no_hp'] ?? ''); ?>"
                    placeholder="Contoh: 081234567890">
            </div>

            <!-- Nomor Kamar -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nomor Kamar
                </label>
                <select name="id_kamar" class="form-select" required>
                    <option value="">-- Pilih Kamar --</option>

                    <?php
                    $kamar = mysqli_query(
                        $conn,
                        "SELECT id_kamar, nomor_kamar
                         FROM kamar
                         ORDER BY nomor_kamar ASC"
                    );

                    while ($k = mysqli_fetch_assoc($kamar)) {
                        $selected = (
                            $d['id_kamar'] == $k['id_kamar']
                        ) ? 'selected' : '';
                    ?>
                        <option
                            value="<?= (int) $k['id_kamar']; ?>"
                            <?= $selected; ?>>
                            <?= htmlspecialchars($k['nomor_kamar']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Status
                </label>
                <select name="status" class="form-select" required>
                    <option
                        value="Aktif"
                        <?= ($d['status'] === 'Aktif') ? 'selected' : ''; ?>>
                        Aktif
                    </option>

                    <option
                        value="Nonaktif"
                        <?= ($d['status'] === 'Nonaktif') ? 'selected' : ''; ?>>
                        Nonaktif
                    </option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex flex-wrap gap-2">
                <button
                    type="submit"
                    class="btn btn-warning px-4 text-dark fw-medium">
                    <i class="fa-solid fa-pen-to-square me-1"></i>
                    Simpan Perubahan
                </button>

                <a href="index.php" class="btn btn-secondary px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
