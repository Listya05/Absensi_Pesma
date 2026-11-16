<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../config/koneksi.php";

// Cek apakah parameter id ada
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Validasi id
$id = (int) $_GET['id'];

/*=========================================
CEK APAKAH KAMAR MASIH DIGUNAKAN
=========================================*/

$stmt = $conn->prepare("
    SELECT id_mahasiswi
    FROM mahasiswi
    WHERE id_kamar = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$cek = $stmt->get_result();

if ($cek->num_rows > 0) {
?>
    <script>
        alert("Kamar tidak dapat dihapus karena masih ditempati mahasiswi.");
        window.location = "index.php";
    </script>
<?php
    exit;
}

/*=========================================
HAPUS DATA KAMAR
=========================================*/

$stmt = $conn->prepare("
    DELETE FROM kamar
    WHERE id_kamar = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
?>
    <script>
        alert("Data berhasil dihapus.");
        window.location = "index.php";
    </script>
<?php
} else {
?>
    <script>
        alert("Data gagal dihapus.");
        window.location = "index.php";
    </script>
<?php
}
?>