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

$aksi = $_POST['aksi'] ?? '';
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';
$idUser = (int) ($_POST['id_user'] ?? 0);

if (
    $nama === '' ||
    $username === '' ||
    !in_array($role, ['Admin', 'Wali Santri'], true) ||
    strlen($nama) > 100 ||
    strlen($username) > 50
) {
    header("Location: index.php?pesan=gagal");
    exit;
}

if ($aksi === 'tambah') {
    if (strlen($password) < 8) {
        header("Location: index.php?pesan=gagal");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (nama, username, password, role)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt, "ssss", $nama, $username, $hash, $role
    );

} elseif ($aksi === 'edit' && $idUser > 0) {
    // Lindungi akun yang sedang digunakan
    if ($idUser === (int) ($_SESSION['id_user'] ?? 0)) {
        header("Location: index.php?pesan=dilarang");
        exit;
    }

    if ($password !== '' && strlen($password) < 8) {
        header("Location: index.php?mode=edit&id=$idUser&pesan=gagal");
        exit;
    }

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET nama = ?, username = ?, role = ?, password = ?
             WHERE id_user = ? AND role <> 'Super Admin'"
        );

        mysqli_stmt_bind_param(
            $stmt, "ssssi", $nama, $username, $role, $hash, $idUser
        );
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET nama = ?, username = ?, role = ?
             WHERE id_user = ? AND role <> 'Super Admin'"
        );

        mysqli_stmt_bind_param(
            $stmt, "sssi", $nama, $username, $role, $idUser
        );
    }
} else {
    header("Location: index.php?pesan=gagal");
    exit;
}

if (mysqli_stmt_execute($stmt)) {
    $jumlahBerubah = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    // INSERT menghasilkan satu baris baru.
    // UPDATE dengan nilai yang sama juga bisa menghasilkan 0.
    if ($aksi === 'edit' && $jumlahBerubah === 0) {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id_user FROM users
             WHERE id_user = ? AND role <> 'Super Admin'"
        );
        mysqli_stmt_bind_param($cek, "i", $idUser);
        mysqli_stmt_execute($cek);
        $ada = mysqli_stmt_get_result($cek);
        $ditemukan = mysqli_num_rows($ada) > 0;
        mysqli_stmt_close($cek);

        if (!$ditemukan) {
            header("Location: index.php?pesan=dilarang");
            exit;
        }
    }

    header("Location: index.php?pesan=" . ($aksi === 'tambah' ? 'tambah' : 'edit'));
    exit;
}

$kodeError = mysqli_stmt_errno($stmt);
mysqli_stmt_close($stmt);

if ($kodeError === 1062) {
    header("Location: index.php?pesan=username");
    exit;
}

header("Location: index.php?pesan=gagal");
exit;
?>
