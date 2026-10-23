<?php
session_start();

if (
    !isset($_SESSION['login']) ||
    $_SESSION['login'] !== true ||
    ($_SESSION['role'] ?? '') !== 'Super Admin'
) {
    header("Location: /asrama_PESMA/auth/login.php?pesan=akses");
    exit;
}

require_once "../../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$idUser = (int) ($_POST['id_user'] ?? 0);

if ($idUser <= 0) {
    header("Location: index.php?pesan=gagal");
    exit;
}

if ($idUser === (int) ($_SESSION['id_user'] ?? 0)) {
    header("Location: index.php?pesan=dilarang");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM users
     WHERE id_user = ?
       AND role <> 'Super Admin'"
);

mysqli_stmt_bind_param($stmt, "i", $idUser);
$berhasil = mysqli_stmt_execute($stmt);
$kodeError = mysqli_stmt_errno($stmt);
$jumlahDihapus = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($berhasil && $jumlahDihapus > 0) {
    header("Location: index.php?pesan=hapus");
    exit;
}

if ($kodeError === 1451) {
    header("Location: index.php?pesan=terkait");
    exit;
}

if ($berhasil) {
    header("Location: index.php?pesan=dilarang");
    exit;
}

header("Location: index.php?pesan=gagal");
exit;
?>
