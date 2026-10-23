
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
if ($nama === '' || $prodi === '' || !$id_kamar) {
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
    // Memastikan kamar yang dipilih tersedia
    $stmtKamar = mysqli_prepare(
        $conn,
        "SELECT id_kamar FROM kamar WHERE id_kamar = ?"
    );

    if (!$stmtKamar) {
        throw new Exception("Gagal memeriksa data kamar.");
    }

    mysqli_stmt_bind_param($stmtKamar, "i", $id_kamar);
    mysqli_stmt_execute($stmtKamar);

    $hasilKamar = mysqli_stmt_get_result($stmtKamar);
    $kamarAda = mysqli_num_rows($hasilKamar) > 0;

    mysqli_stmt_close($stmtKamar);

    if (!$kamarAda) {
        throw new Exception("Kamar yang dipilih tidak ditemukan.");
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

    // s = string, i = integer
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
        throw new Exception(mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    // Berhasil menyimpan data
    echo "<script>
        alert('Data mahasiswi berhasil ditambahkan!');
        window.location='index.php';
    </script>";
    exit;

} catch (Throwable $e) {
    // Menampilkan pesan jika penyimpanan gagal
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
