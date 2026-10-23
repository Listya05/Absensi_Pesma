
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";

// Mengambil data kamar
$kamar = mysqli_query($conn, "
    SELECT id_kamar, nomor_kamar
    FROM kamar
    ORDER BY nomor_kamar ASC
");

?>

<!-- Form Tambah Mahasiswi -->
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-header bg-primary text-white py-3 rounded-top-4">
        <h4 class="mb-0 fw-bold fs-5">
            <i class="fa-solid fa-user-plus me-2"></i>
            Tambah Mahasiswi
        </h4>
    </div>

    <div class="card-body p-4">

        <form action="proses_tambah.php" method="POST">

            <!-- Nama Mahasiswi -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nama Mahasiswi
                </label>

                <input
                    type="text"
                    name="nama"
                    class="form-control"
                    placeholder="Masukkan nama lengkap..."
                    maxlength="100"
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
                    placeholder="Masukkan program studi..."
                    maxlength="100"
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
                    placeholder="Contoh: 2024"
                    min="2000"
                    max="2100">
            </div>

            <!-- Nomor HP -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    No HP
                </label>

                <input
                    type="tel"
                    name="no_hp"
                    class="form-control"
                    placeholder="Contoh: 081234567890"
                    maxlength="20">
            </div>

            <!-- Nomor Kamar -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nomor Kamar
                </label>

                <select
                    name="id_kamar"
                    class="form-select"
                    required>

                    <option value="">-- Pilih Kamar --</option>

                    <?php if ($kamar && mysqli_num_rows($kamar) > 0) { ?>

                        <?php while ($k = mysqli_fetch_assoc($kamar)) { ?>

                            <option value="<?= (int) $k['id_kamar']; ?>">
                                <?= htmlspecialchars($k['nomor_kamar']); ?>
                            </option>

                        <?php } ?>

                    <?php } ?>

                </select>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Status
                </label>

                <select name="status" class="form-select" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Tombol -->
            <div class="d-flex gap-2 flex-wrap">

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Simpan
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
