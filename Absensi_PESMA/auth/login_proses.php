<?php
session_start();

require_once "../config/koneksi.php";

// Pastikan form login dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Ambil input dari form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if ($username === '' || $password === '') {
    header("Location: login.php?pesan=gagal");
    exit;
}

// Cari pengguna berdasarkan username
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user, nama, username, password, role
     FROM users
     WHERE username = ?
     LIMIT 1"
);

if (!$stmt) {
    die("Terjadi kesalahan pada proses login.");
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Periksa username dan password
if (!$user || !password_verify($password, $user['password'])) {
    header("Location: login.php?pesan=gagal");
    exit;
}

// Pastikan role termasuk role yang diizinkan
$roleDiizinkan = [
    'Super Admin',
    'Admin',
    'Wali Santri'
];

if (!in_array($user['role'], $roleDiizinkan, true)) {
    header("Location: login.php?pesan=gagal");
    exit;
}

// Perbarui ID session setelah login berhasil
session_regenerate_id(true);

// Simpan informasi pengguna ke session
$_SESSION['login']   = true;
$_SESSION['id_user'] = $user['id_user'];
$_SESSION['nama']    = $user['nama'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']    = $user['role'];

// Arahkan ke dashboard

switch ($user['role']) {
    case 'Super Admin':
        header("Location: ../superadmin/dashboard.php");
        break;

    case 'Admin':
        header("Location: ../admin/dashboard.php");
        break;

    case 'Wali Santri':
        header("Location: ../wali_santri/dashboard.php");
        break;

    default:
        header("Location: login.php?pesan=gagal");
        exit;
}

exit;
