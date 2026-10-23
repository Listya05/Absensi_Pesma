
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
if (!$id || $nama === '' || $prodi === '' || !$id_kamar) {
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
        window.location='index.php';
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
    // Pastikan data mahasiswi tersedia
    $stmtCek = mysqli_prepare(
        $conn,
        "SELECT id_mahasiswi FROM mahasiswi
         WHERE id_mahasiswi = ?"
    );

    mysqli_stmt_bind_param($stmtCek, "i", $id);
    mysqli_stmt_execute($stmtCek);

    $hasilCek = mysqli_stmt_get_result($stmtCek);
    $dataAda = mysqli_num_rows($hasilCek) > 0;

    mysqli_stmt_close($stmtCek);

    if (!$dataAda) {
        throw new Exception("Data mahasiswi tidak ditemukan.");
    }

    // Pastikan kamar tersedia
    $stmtKamar = mysqli_prepare(
        $conn,
        "SELECT id_kamar FROM kamar WHERE id_kamar = ?"
    );

    mysqli_stmt_bind_param($stmtKamar, "i", $id_kamar);
    mysqli_stmt_execute($stmtKamar);

    $hasilKamar = mysqli_stmt_get_result($stmtKamar);
    $kamarAda = mysqli_num_rows($hasilKamar) > 0;

    mysqli_stmt_close($stmtKamar);

    if (!$kamarAda) {
        throw new Exception("Kamar yang dipilih tidak ditemukan.");
    }

    // Update data mahasiswi
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
        throw new Exception(mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    echo "<script>
        alert('Data mahasiswi berhasil diperbarui!');
        window.location='index.php';
    </script>";
    exit;

} catch (Throwable $e) {
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
