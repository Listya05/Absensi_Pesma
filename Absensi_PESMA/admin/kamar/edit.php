
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Validasi ID kamar
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data kamar
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_kamar, nomor_kamar, lantai, kapasitas
     FROM kamar
     WHERE id_kamar = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

// Jika kamar tidak ditemukan
if (!$d) {
    echo "<script>
        alert('Data kamar tidak ditemukan!');
        window.location='index.php';
    </script>";
    exit;
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";
?>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-primary text-white rounded-top-4 py-3">
        <h4 class="mb-0 fs-5 fw-bold">
            <i class="fa-solid fa-door-open me-2"></i>
            Edit Data Kamar
        </h4>
    </div>

    <div class="card-body p-4">
        <form action="update.php" method="POST">

            <input
                type="hidden"
                name="id_kamar"
                value="<?= (int) $d['id_kamar']; ?>">

            <!-- Nomor Kamar -->
            <div class="mb-3">
                <label for="nomor_kamar" class="form-label fw-semibold">
                    Nomor Kamar
                </label>

                <input
                    type="text"
                    id="nomor_kamar"
                    name="nomor_kamar"
                    class="form-control"
                    value="<?= htmlspecialchars($d['nomor_kamar']); ?>"
                    placeholder="Contoh: 201"
                    required>
            </div>

            <!-- Lantai -->
            <div class="mb-3">
                <label for="lantai" class="form-label fw-semibold">
                    Lantai
                </label>

                <select
                    name="lantai"
                    id="lantai"
                    class="form-select"
                    required>

                    <option
                        value="2"
                        <?= ((int) $d['lantai'] === 2) ? 'selected' : ''; ?>>
                        Lantai 2
                    </option>

                    <option
                        value="3"
                        <?= ((int) $d['lantai'] === 3) ? 'selected' : ''; ?>>
                        Lantai 3
                    </option>

                </select>
            </div>

            <!-- Kapasitas -->
            <div class="mb-4">
                <label for="kapasitas" class="form-label fw-semibold">
                    Kapasitas Kamar
                </label>

                <input
                    type="number"
                    id="kapasitas"
                    name="kapasitas"
                    class="form-control"
                    value="<?= (int) $d['kapasitas']; ?>"
                    min="1"
                    required>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex flex-wrap gap-2">
                <button
                    type="submit"
                    class="btn btn-success px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Simpan Perubahan
                </button>

                <a
                    href="index.php"
                    class="btn btn-secondary px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

        </form>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
