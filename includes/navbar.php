<?php
// Role dan data pengguna yang sedang login
$role = strtolower(trim($_SESSION['role'] ?? ''));
$nama_user = $_SESSION['nama'] ?? 'User';

// Link ganti password sesuai role
switch ($role) {
    case 'super admin':
        $link_password = '/absensi_PESMA/password/index.php';
        break;

    case 'admin':
        $link_password = '/absensi_PESMA/password/index.php';
        break;

    case 'wali santri':
        $link_password = '/absensi_PESMA/password/index.php';
        break;

    default:
        $link_password = '/absensi_PESMA/password/index.php';
        break;
}

// Link logout
$link_logout = '/absensi_PESMA/auth/logout.php';

// Inisial nama pengguna
$inisial = strtoupper(substr($nama_user, 0, 2));
?>

<div class="main">

    <nav class="navbar navbar-expand-lg navbar-custom bg-white border-bottom py-2">
        <div class="container-fluid d-flex flex-row flex-nowrap align-items-center">

            <!-- Tombol menu mobile -->
            <button
                type="button"
                id="menu-toggle"
                class="btn btn-outline-primary menu-toggle me-3 d-lg-none flex-shrink-0">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- Profil pengguna -->
            <div class="ms-auto flex-shrink-0">
                <div class="dropdown">

                    <a
                        href="#"
                        class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        id="dropdownUser"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <!-- Avatar -->
                        <div
                            class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2"
                            style="width:36px;height:36px;font-size:.85rem;">
                            <?= htmlspecialchars($inisial); ?>
                        </div>

                        <!-- Nama dan role -->
                        <div class="text-start d-none d-md-block me-1">
                            <span class="fw-bold d-block text-dark lh-1 mb-1"
                                  style="font-size:.85rem;">
                                <?= htmlspecialchars($nama_user); ?>
                            </span>

                            <small class="text-muted text-uppercase fw-semibold d-block"
                                   style="font-size:.65rem;letter-spacing:.5px;">
                                <?= htmlspecialchars($_SESSION['role'] ?? 'User'); ?>
                            </small>
                        </div>
                    </a>

                    <!-- Dropdown -->
                    <ul
                        class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2"
                        aria-labelledby="dropdownUser"
                        style="min-width:190px;">

                        <!-- Nama pengguna -->
                        <li>
                            <div class="dropdown-item py-2 fw-medium text-dark pe-none">
                                <i class="fa-regular fa-user me-2 text-secondary"></i>
                                <?= htmlspecialchars($nama_user); ?>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>

                        <!-- Ganti password -->
                        <li>
                            <a class="dropdown-item py-2 fw-medium text-dark"
                               href="<?= htmlspecialchars($link_password); ?>">
                                <i class="fa-solid fa-key me-2 text-primary"></i>
                                Ganti Password
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>

                        <!-- Logout -->
                        <li>
                            <a
                                class="dropdown-item py-2 text-danger fw-medium"
                                href="<?= htmlspecialchars($link_logout); ?>"
                                onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>
                                Logout
                            </a>
                        </li>

                    </ul>
                </div>
            </div>

        </div>
    </nav>

    <div class="container-fluid mt-4">