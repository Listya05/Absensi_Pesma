<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

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
    header("Location: index.php");
    exit;
}

$tanggal = trim($_POST['tanggal']);
$waktu   = trim($_POST['waktu']);

$id_mahasiswi = $_POST['id_mahasiswi'];
$status       = $_POST['status'];

if (!is_array($id_mahasiswi) || !is_array($status)) {
    header("Location: index.php");
    exit;
}

if (count($id_mahasiswi) != count($status)) {
    header("Location: index.php");
    exit;
}

// Validasi tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    header("Location: index.php");
    exit;
}

// Validasi waktu
$waktuValid = ['Subuh', 'Magrib', 'Isya'];

if (!in_array($waktu, $waktuValid, true)) {
    header("Location: index.php");
    exit;
}

/*
=================================================
CARI ID ABSENSI
=================================================
*/

$stmt = $conn->prepare("
    SELECT id_absensi
    FROM absensi
    WHERE tanggal = ?
      AND waktu = ?
");

$stmt->bind_param("ss", $tanggal, $waktu);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    echo "
    <script>
        alert('Data absensi tidak ditemukan.');
        window.location='index.php';
    </script>
    ";

    exit;
}

$data = $result->fetch_assoc();
$id_absensi = (int)$data['id_absensi'];

/*
=================================================
UPDATE DETAIL ABSENSI
=================================================
*/

$stmtUpdate = $conn->prepare("
    UPDATE detail_absensi
    SET status = ?
    WHERE id_absensi = ?
      AND id_mahasiswi = ?
");

$statusValid = ['Hadir', 'Sakit', 'Izin', 'Alpha'];

for ($i = 0; $i < count($id_mahasiswi); $i++) {

    $idmhs = (int)$id_mahasiswi[$i];
    $sts   = trim($status[$i]);

    if (!in_array($sts, $statusValid, true)) {
        continue;
    }

    $stmtUpdate->bind_param("sii", $sts, $id_absensi, $idmhs);
    $stmtUpdate->execute();
}

/*
=================================================
BERHASIL
=================================================
*/

echo "
<script>
    alert('Absensi berhasil diperbarui.');
    window.location='index.php?tanggal={$tanggal}&waktu={$waktu}&mode=edit';
</script>
";