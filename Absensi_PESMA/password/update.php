<?php

session_start();

include "../config/session.php";
include "../config/koneksi.php";

// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi    = $_POST['konfirmasi'];

// ==============================
// Validasi Password Baru
// ==============================

if ($password_baru != $konfirmasi) {

    echo "
    <script>
        alert('Konfirmasi password tidak sesuai.');
        history.back();
    </script>
    ";

    exit;
}

if (strlen($password_baru) < 8) {

    echo "
    <script>
        alert('Password minimal 8 karakter.');
        history.back();
    </script>
    ";

    exit;
}

// ==============================
// Ambil Password Lama
// ==============================

$query = mysqli_query($conn, "
    SELECT password
    FROM users
    WHERE id_user='$id_user'
");

if (!$query || mysqli_num_rows($query) == 0) {

    echo "
    <script>
        alert('User tidak ditemukan.');
        history.back();
    </script>
    ";

    exit;
}

$user = mysqli_fetch_assoc($query);

// ==============================
// Cek Password Lama
// ==============================

if (!password_verify($password_lama, $user['password'])) {

    echo "
    <script>
        alert('Password lama salah.');
        history.back();
    </script>
    ";

    exit;
}

// ==============================
// Hash Password Baru
// ==============================

$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// ==============================
// Update Password
// ==============================

$update = mysqli_query($conn, "
    UPDATE users
    SET password='$password_hash'
    WHERE id_user='$id_user'
");

if ($update) {

    echo "
    <script>
        alert('Password berhasil diubah.');

    ";

    // Redirect sesuai role
    if ($_SESSION['role'] == "Admin") {

        echo "
        window.location='../admin/dashboard.php';
        ";

    } else {

        echo "
        window.location='../mahasiswi/dashboard.php';
        ";

    }

    echo "
    </script>
    ";

} else {

    echo "
    <script>
        alert('Gagal mengubah password.');
        history.back();
    </script>
    ";

}

?>