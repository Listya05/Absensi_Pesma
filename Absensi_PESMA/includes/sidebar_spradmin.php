<?php
$halamanAktif = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <!-- Header Sidebar dengan Logo -->
    <div class="sidebar-brand text-center p-3">

        <img
            src="/absensi_PESMA/assets/img/logo.png"
            alt="Logo Al-Masoem"
            class="img-fluid mb-2"
            style="max-height: 55px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.2));"
        >

        <h6
            class="fw-bold text-white mb-0"
            style="letter-spacing: 0.5px; font-size: 15px;"
        >
            PESMA AL-MASOEM
        </h6>

        <small class="text-white-50" style="font-size: 11px;">
            Pesantren Mahasiswa
        </small>

    </div>

    <hr class="mx-3 my-2 text-white-50">

    <!-- Menu Utama -->

    <a
        href="/absensi_PESMA/superadmin/dashboard.php"
        class="<?= $halamanAktif === 'dashboard.php' ? 'active' : ''; ?>"
    >
        <i class="fa-solid fa-house"></i>
        Dashboard
    </a>

    <a
        href="/absensi_PESMA/superadmin/akun/index.php"
        class="<?= $halamanAktif === 'index.php' ? 'active' : ''; ?>"
    >
        <i class="fa-solid fa-users-gear"></i>
        Manajemen Akun
    </a>

    <!-- Menu Keluar -->
    <hr class="mx-3 my-2 text-white-50">

    <a
        href="/absensi_PESMA/auth/logout.php"
        onclick="return confirm('Apakah kamu yakin ingin logout?');"
    >
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </a>

</div>

<div id="overlay" class="overlay"></div>
