
<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Pastikan ID tersedia
$id_mahasiswi = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id_mahasiswi) {
    header("Location: index.php");
    exit;
}

try {
    // Pastikan data mahasiswi tersedia
    $stmtCek = mysqli_prepare(
        $conn,
        "SELECT id_mahasiswi
         FROM mahasiswi
         WHERE id_mahasiswi = ?"
    );

    mysqli_stmt_bind_param(
        $stmtCek,
        "i",
        $id_mahasiswi
    );

    mysqli_stmt_execute($stmtCek);

    $hasil = mysqli_stmt_get_result($stmtCek);
    $dataAda = mysqli_num_rows($hasil) > 0;

    mysqli_stmt_close($stmtCek);

    if (!$dataAda) {
        echo "<script>
            alert('Data mahasiswi tidak ditemukan!');
            window.location='index.php';
        </script>";
        exit;
    }

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    // Hapus detail absensi milik mahasiswi
    $stmtAbsensi = mysqli_prepare(
        $conn,
        "DELETE FROM detail_absensi
         WHERE id_mahasiswi = ?"
    );

    mysqli_stmt_bind_param(
        $stmtAbsensi,
        "i",
        $id_mahasiswi
    );

    if (!mysqli_stmt_execute($stmtAbsensi)) {
        throw new Exception("Gagal menghapus detail absensi.");
    }

    mysqli_stmt_close($stmtAbsensi);

    // Hapus data mahasiswi
    $stmtHapus = mysqli_prepare(
        $conn,
        "DELETE FROM mahasiswi
         WHERE id_mahasiswi = ?"
    );

    mysqli_stmt_bind_param(
        $stmtHapus,
        "i",
        $id_mahasiswi
    );

    if (!mysqli_stmt_execute($stmtHapus)) {
        throw new Exception("Gagal menghapus data mahasiswi.");
    }

    mysqli_stmt_close($stmtHapus);

    // Simpan perubahan
    mysqli_commit($conn);

    echo "<script>
        alert('Data mahasiswi berhasil dihapus.');
        window.location='index.php';
    </script>";
    exit;

} catch (Throwable $e) {

    // Batalkan perubahan jika terjadi kegagalan
    if (mysqli_connect_errno() === 0) {
        mysqli_rollback($conn);
    }

    $pesan = json_encode(
        $e->getMessage(),
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        alert('Data gagal dihapus: ' + {$pesan});
        window.location='index.php';
    </script>";
    exit;
}
?>
