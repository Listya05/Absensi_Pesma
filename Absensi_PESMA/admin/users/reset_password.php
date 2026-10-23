<?php

include "../../config/session.php";
include "../../config/koneksi.php";

$id_user = (int) $_POST['id_user'];

$password   = $_POST['password'];
$konfirmasi = $_POST['konfirmasi'];

if (empty($password)) {

    echo "<script>
            alert('Password baru tidak boleh kosong!');
            history.back();
          </script>";
    exit;
}

if ($password !== $konfirmasi) {

    echo "<script>
            alert('Konfirmasi password tidak sama!');
            history.back();
          </script>";
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    UPDATE users
    SET password = ?
    WHERE id_user = ?
");

$stmt->bind_param("si", $passwordHash, $id_user);

if ($stmt->execute()) {

    echo "<script>
            alert('Password berhasil direset.');
            window.location='index.php';
          </script>";

} else {

    echo "<script>
            alert('Reset password gagal.');
            history.back();
          </script>";

}