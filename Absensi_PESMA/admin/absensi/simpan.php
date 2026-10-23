<?php

$roleWajib = 'Admin';

include '../../config/session.php';
include '../../config/koneksi.php';

/*
=================================================
VALIDASI FORM
=================================================
*/

if (
    !isset($_POST['tanggal']) ||
    !isset($_POST['waktu']) ||
    !isset($_POST['id_mahasiswi']) ||
    !isset($_POST['status'])
) {
    header('Location: index.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$tanggal = trim($_POST['tanggal']);
$waktu   = trim($_POST['waktu']);
$lantai  = isset($_POST['lantai']) ? trim($_POST['lantai']) : '';

$id_mahasiswi = $_POST['id_mahasiswi'];
$status       = $_POST['status'];

// Buat URL redirect untuk membawa parameter filter kembali
$redirectUrl = "index.php?tanggal=" . urlencode($tanggal) . "&waktu=" . urlencode($waktu) . "&lantai=" . urlencode($lantai);

// Pastikan array
if (!is_array($id_mahasiswi) || !is_array($status)) {
    header("Location: $redirectUrl");
    exit;
}

// Pastikan jumlah data sama
if (count($id_mahasiswi) !== count($status)) {
    die('Data absensi tidak valid.');
}

/*
=================================================
CEK APAKAH ABSENSI SUDAH ADA
=================================================
*/

$stmt = $conn->prepare('
    SELECT id_absensi
    FROM absensi
    WHERE tanggal = ? AND waktu = ?
');

$stmt->bind_param('ss', $tanggal, $waktu);
$stmt->execute();

$cek = $stmt->get_result();

if ($cek->num_rows > 0) {
    echo "
    <script>
        alert('Absensi pada tanggal dan waktu tersebut sudah diinput!');
        window.location='$redirectUrl';
    </script>
    ";
    exit;
}

/*
=================================================
SIMPAN KE TABEL ABSENSI
=================================================
*/

$stmt = $conn->prepare('
    INSERT INTO absensi
    (
        tanggal,
        waktu,
        id_user
    )
    VALUES
    (
        ?, ?, ?
    )
');

$stmt->bind_param('ssi', $tanggal, $waktu, $id_user);

if (!$stmt->execute()) {
    die('Gagal menyimpan absensi.');
}

/*
=================================================
AMBIL ID ABSENSI
=================================================
*/

$id_absensi = $conn->insert_id;

/*
=================================================
SIMPAN DETAIL ABSENSI
=================================================
*/

$stmtDetail = $conn->prepare('
    INSERT INTO detail_absensi
    (
        id_absensi,
        id_mahasiswi,
        status
    )
    VALUES
    (
        ?, ?, ?
    )
');

$totalData = count($id_mahasiswi);

for ($i = 0; $i < $totalData; $i++) {

    $idmhs = (int) $id_mahasiswi[$i];
    $sts   = trim($status[$i]);

    // Validasi status
    $statusValid = ['Hadir', 'Sakit', 'Izin', 'Alpha'];

    if (!in_array($sts, $statusValid, true)) {
        continue;
    }

    $stmtDetail->bind_param('iis', $id_absensi, $idmhs, $sts);
    $stmtDetail->execute();
}

/*
=================================================
BERHASIL
=================================================
*/

echo "
<script>
    alert('Absensi berhasil disimpan.');
    window.location='$redirectUrl';
</script>
";
?>