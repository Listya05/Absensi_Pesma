<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==================================
// CEK APAKAH PENGGUNA SUDAH LOGIN
// ==================================
if (
    !isset($_SESSION['login']) ||
    $_SESSION['login'] !== true
) {
    header("Location: ../auth/login.php");
    exit;
}

// ==================================
// CEK ROLE JIKA HALAMAN MEMBUTUHKAN
// ROLE TERTENTU
// ==================================
if (isset($roleWajib)) {

    if (
        !isset($_SESSION['role']) ||
        $_SESSION['role'] !== $roleWajib
    ) {
        header("Location: ../dashboard/index.php");
        exit;
    }
}
?>
