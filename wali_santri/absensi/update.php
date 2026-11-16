
<?php

$roleWajib = 'Wali Santri';

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

$tanggal = trim($_POST['tanggal']);
$waktu   = trim($_POST['waktu']);
$lantai  = isset($_POST['lantai']) ? trim($_POST['lantai']) : '';

$id_mahasiswi = $_POST['id_mahasiswi'];
$status       = $_POST['status'];

// URL untuk kembali ke halaman absensi
$redirectUrl = 'index.php?' . http_build_query([
    'tanggal' => $tanggal,
    'waktu'   => $waktu,
    'lantai'  => $lantai,
    'mode'    => 'edit'
]);

// Pastikan data berbentuk array
if (!is_array($id_mahasiswi) || !is_array($status)) {
    die('Data absensi tidak valid.');
}

// Pastikan jumlah data sama
if (
    count($id_mahasiswi) === 0 ||
    count($id_mahasiswi) !== count($status)
) {
    die('Data absensi tidak valid.');
}

/*
=================================================
VALIDASI TANGGAL
=================================================
*/

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    die('Format tanggal tidak valid.');
}

/*
=================================================
VALIDASI WAKTU
=================================================
*/

$waktuValid = ['Subuh', 'Magrib', 'Isya'];

if (!in_array($waktu, $waktuValid, true)) {
    die('Waktu absensi tidak valid.');
}

/*
=================================================
VALIDASI STATUS
=================================================
*/

$statusValid = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

foreach ($id_mahasiswi as $i => $id) {

    if (!is_numeric($id) || (int) $id <= 0) {
        die('ID mahasiswi tidak valid.');
    }

    if (
        !isset($status[$i]) ||
        !is_string($status[$i]) ||
        !in_array(trim($status[$i]), $statusValid, true)
    ) {
        die('Status absensi tidak valid pada data ke-' . ($i + 1));
    }
}

/*
=================================================
CARI ID ABSENSI
=================================================
*/

$stmt = $conn->prepare('
    SELECT id_absensi
    FROM absensi
    WHERE tanggal = ?
      AND waktu = ?
');

if (!$stmt) {
    die('Gagal mencari data absensi: ' . $conn->error);
}

$stmt->bind_param('ss', $tanggal, $waktu);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    echo "
    <script>
        alert('Data absensi tidak ditemukan.');
        window.location = " . json_encode($redirectUrl) . ";
    </script>
    ";

    exit;
}

$data = $result->fetch_assoc();
$id_absensi = (int) $data['id_absensi'];

$stmt->close();

/*
=================================================
UPDATE DETAIL ABSENSI
=================================================
*/

$stmtUpdate = $conn->prepare('
    UPDATE detail_absensi
    SET status = ?
    WHERE id_absensi = ?
      AND id_mahasiswi = ?
');

if (!$stmtUpdate) {
    die('Gagal menyiapkan update absensi: ' . $conn->error);
}

$totalData = count($id_mahasiswi);

for ($i = 0; $i < $totalData; $i++) {

    $idmhs = (int) $id_mahasiswi[$i];
    $sts   = trim($status[$i]);

    $stmtUpdate->bind_param(
        'sii',
        $sts,
        $id_absensi,
        $idmhs
    );

    if (!$stmtUpdate->execute()) {
        die(
            'Gagal memperbarui status mahasiswi ID ' .
            $idmhs . ': ' . $stmtUpdate->error
        );
    }
}

$stmtUpdate->close();

/*
=================================================
BERHASIL
=================================================
*/

echo "
<script>
    alert('Absensi berhasil diperbarui.');
    window.location = " . json_encode($redirectUrl) . ";
</script>
";

exit;

?>
