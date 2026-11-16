<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Pastikan data dikirim melalui formulir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Mengambil data dari formulir
$nama     = trim($_POST['nama'] ?? '');
$prodi    = trim($_POST['prodi'] ?? '');
$angkatan = trim($_POST['angkatan'] ?? '');
$no_hp    = trim($_POST['no_hp'] ?? '');
$id_kamar = filter_input(INPUT_POST, 'id_kamar', FILTER_VALIDATE_INT);
$status   = $_POST['status'] ?? 'Aktif';

// Validasi data wajib
if ($nama === '' || $prodi === '' || !$id_kamar || $id_kamar < 1) {
    echo "<script>
        alert('Nama, program studi, dan kamar wajib diisi!');
        window.location='tambah.php';
    </script>";
    exit;
}

// Validasi status
if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
    echo "<script>
        alert('Status mahasiswi tidak valid!');
        window.location='tambah.php';
    </script>";
    exit;
}

// Validasi angkatan harus berupa angka jika diisi
if ($angkatan !== '' && !ctype_digit($angkatan)) {
    echo "<script>
        alert('Angkatan harus berupa angka!');
        window.location='tambah.php';
    </script>";
    exit;
}

try {
    // Memulai transaksi database
    mysqli_begin_transaction($conn);

    // Mengunci data kamar agar kapasitas dapat diperiksa
    // sebelum data mahasiswi ditambahkan
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

    // Memastikan kamar tersedia
    if (!$dataKamar) {
        throw new Exception("Kamar yang dipilih tidak ditemukan.");
    }

    $kapasitas = (int) $dataKamar['kapasitas'];

    if ($kapasitas < 1) {
        throw new Exception("Kapasitas kamar tidak valid.");
    }

    // Menghitung jumlah mahasiswi aktif di kamar
    $stmtJumlah = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS jumlah_penghuni
         FROM mahasiswi
         WHERE id_kamar = ?
           AND status = 'Aktif'"
    );

    if (!$stmtJumlah) {
        throw new Exception("Gagal menghitung penghuni kamar.");
    }

    mysqli_stmt_bind_param($stmtJumlah, "i", $id_kamar);
    mysqli_stmt_execute($stmtJumlah);

    $hasilJumlah = mysqli_stmt_get_result($stmtJumlah);
    $dataJumlah = mysqli_fetch_assoc($hasilJumlah);

    mysqli_stmt_close($stmtJumlah);

    $jumlahPenghuni = (int) $dataJumlah['jumlah_penghuni'];

    // Memeriksa kapasitas jika mahasiswi yang ditambahkan berstatus Aktif
    if ($status === 'Aktif' && $jumlahPenghuni >= $kapasitas) {
        throw new Exception(
            "Kamar sudah penuh! Kapasitas kamar adalah " .
            $kapasitas . " orang."
        );
    }

    // Menyimpan data mahasiswi
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO mahasiswi
            (nama, prodi, angkatan, no_hp, id_kamar, status)
         VALUES (?, ?, NULLIF(?, ''), ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception("Gagal menyiapkan penyimpanan data.");
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssis",
        $nama,
        $prodi,
        $angkatan,
        $no_hp,
        $id_kamar,
        $status
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menyimpan data mahasiswi.");
    }

    mysqli_stmt_close($stmt);

    // Menyimpan transaksi
    mysqli_commit($conn);

    // Berhasil menyimpan data
    echo "<script>
        alert('Data mahasiswi berhasil ditambahkan!');
        window.location='index.php';
    </script>";
    exit;

} catch (Throwable $e) {

    // Membatalkan transaksi jika terjadi kegagalan
    mysqli_rollback($conn);

    // Menyiapkan pesan notifikasi
    $pesan = json_encode(
        $e->getMessage(),
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        alert('Gagal menambah data mahasiswi: ' + {$pesan});
        window.location='tambah.php';
    </script>";
    exit;
}
?>