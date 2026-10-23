<?php

session_start();

include "../../config/koneksi.php";

$nomor_kamar = $_POST['nomor_kamar'];
$lantai      = $_POST['lantai'];
$kapasitas   = $_POST['kapasitas'];

// Cek nomor kamar
$cek = mysqli_query($conn, "SELECT * FROM kamar WHERE nomor_kamar='$nomor_kamar'");

if (mysqli_num_rows($cek) > 0) {

    $_SESSION['error'] = "Nomor kamar sudah ada.";

    header("Location: tambah.php");
    exit;

}

$query = mysqli_query($conn, "
    INSERT INTO kamar
    (
        nomor_kamar,
        lantai,
        kapasitas
    )
    VALUES
    (
        '$nomor_kamar',
        '$lantai',
        '$kapasitas'
    )
");

if ($query) {
    $_SESSION['success'] = "Data kamar berhasil ditambahkan.";
} else {
    $_SESSION['error'] = "Data kamar gagal ditambahkan.";
}

header("Location: index.php");
exit;

?>
