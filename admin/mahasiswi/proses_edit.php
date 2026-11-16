<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Proses hanya melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Mengambil data dari formulir
$id       = filter_input(INPUT_POST, 'id_mahasiswi', FILTER_VALIDATE_INT);
$nama     = trim($_POST['nama'] ?? '');
$prodi    = trim($_POST['prodi'] ?? '');
$angkatan = trim($_POST['angkatan'] ?? '');
$no_hp    = trim($_POST['no_hp'] ?? '');
$id_kamar = filter_input(INPUT_POST, 'id_kamar', FILTER_VALIDATE_INT);
$status   = $_POST['status'] ?? 'Aktif';

// Validasi input
if (!$id || $id < 1 || $nama === '' || $prodi === '' || !$id_kamar || $id_kamar < 1) {
    echo "<script>
        alert('Nama, program studi, dan kamar wajib diisi!');
        window.location='index.php';
    </script>";
    exit;
}

// Validasi status
if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
    echo "<script>
        alert('Status mahasiswi tidak valid!');
        window.location='edit.php?id={$id}';
    </script>";
    exit;
}

// Validasi angkatan
if ($angkatan !== '' && !ctype_digit($angkatan)) {
    echo "<script>
        alert('Angkatan harus berupa angka!');
        window.location='edit.php?id={$id}';
    </script>";
    exit;
}

try {
    // Memulai transaksi
    mysqli_begin_transaction($conn);

    // Memeriksa dan mengunci data mahasiswi
    $stmtCek = mysqli_prepare(
        $conn,
        "SELECT id_mahasiswi, id_kamar, status
         FROM mahasiswi
         WHERE id_mahasiswi = ?
         FOR UPDATE"
    );

    if (!$stmtCek) {
        throw new Exception("Gagal memeriksa data mahasiswi.");
    }

    mysqli_stmt_bind_param($stmtCek, "i", $id);
    mysqli_stmt_execute($stmtCek);

    $hasilCek = mysqli_stmt_get_result($stmtCek);
    $dataMahasiswi = mysqli_fetch_assoc($hasilCek);

    mysqli_stmt_close($stmtCek);

    if (!$dataMahasiswi) {
        throw new Exception("Data mahasiswi tidak ditemukan.");
    }

    // Memeriksa dan mengunci data kamar tujuan
    $stmtKamar = mysqli_prepare(
        $conn,
        "SELECT id_kamar, kapasitas
         FROM kamar
         WHERE id_kamar = ?
         FOR UPDATE"
    );

    if (!$stmtKamar) {
        throw new Exception("Gagal memeriksa data kamar.");
    }

    mysqli_stmt_bind_param($stmtKamar, "i", $id_kamar);
    mysqli_stmt_execute($stmtKamar);

    $hasilKamar = mysqli_stmt_get_result($stmtKamar);
    $dataKamar = mysqli_fetch_assoc($hasilKamar);

    mysqli_stmt_close($stmtKamar);

    if (!$dataKamar) {
        throw new Exception("Kamar yang dipilih tidak ditemukan.");
    }

    $kapasitas = (int) $dataKamar['kapasitas'];

    if ($kapasitas < 1) {
        throw new Exception("Kapasitas kamar tidak valid.");
    }

    // Menghitung penghuni aktif di kamar tujuan,
    // tidak termasuk mahasiswi yang sedang diedit
    $stmtJumlah = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS jumlah_penghuni
         FROM mahasiswi
         WHERE id_kamar = ?
           AND status = 'Aktif'
           AND id_mahasiswi != ?"
    );

    if (!$stmtJumlah) {
        throw new Exception("Gagal menghitung penghuni kamar.");
    }

    mysqli_stmt_bind_param($stmtJumlah, "ii", $id_kamar, $id);
    mysqli_stmt_execute($stmtJumlah);

    $hasilJumlah = mysqli_stmt_get_result($stmtJumlah);
    $dataJumlah = mysqli_fetch_assoc($hasilJumlah);

    mysqli_stmt_close($stmtJumlah);

    $jumlahPenghuni = (int) $dataJumlah['jumlah_penghuni'];

    // Jika status yang baru Aktif, pastikan kamar masih tersedia
    if ($status === 'Aktif' && $jumlahPenghuni >= $kapasitas) {
        throw new Exception(
            "Kamar sudah penuh! Kapasitas kamar adalah " .
            $kapasitas . " orang."
        );
    }

    // Memperbarui data mahasiswi
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE mahasiswi SET
            nama = ?,
            prodi = ?,
            angkatan = NULLIF(?, ''),
            no_hp = ?,
            id_kamar = ?,
            status = ?
         WHERE id_mahasiswi = ?"
    );

    if (!$stmt) {
        throw new Exception("Gagal menyiapkan perubahan data.");
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssisi",
        $nama,
        $prodi,
        $angkatan,
        $no_hp,
        $id_kamar,
        $status,
        $id
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal memperbarui data mahasiswi.");
    }

    mysqli_stmt_close($stmt);

    // Menyimpan transaksi
    mysqli_commit($conn);

    echo "<script>
        alert('Data mahasiswi berhasil diperbarui!');
        window.location='index.php';
    </script>";
    exit;

} catch (Throwable $e) {

    // Membatalkan transaksi jika terjadi kesalahan
    mysqli_rollback($conn);

    $pesan = json_encode(
        $e->getMessage(),
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        alert('Gagal memperbarui data: ' + {$pesan});
        window.location='edit.php?id={$id}';
    </script>";
    exit;
}
?>