<?php

$roleWajib = 'Super Admin';

include "../config/session.php";
include "../config/koneksi.php";

include "../includes/header.php";
include "../includes/sidebar_spradmin.php";
include "../includes/navbar.php";

// =====================================
// TOTAL AKUN PENGGUNA
// =====================================
$qUser = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM users
");

$totalUser = mysqli_fetch_assoc($qUser)['total'] ?? 0;

// =====================================
// TOTAL ADMIN
// =====================================
$qAdmin = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'Admin'
");

$totalAdmin = mysqli_fetch_assoc($qAdmin)['total'] ?? 0;

// =====================================
// TOTAL WALI SANTRI
// =====================================
$qWali = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'Wali Santri'
");

$totalWali = mysqli_fetch_assoc($qWali)['total'] ?? 0;

// =====================================
// LIMA AKUN TERBARU
// =====================================
$qAkunTerbaru = mysqli_query($conn, "
    SELECT nama, username, role, created_at
    FROM users
    ORDER BY id_user DESC
    LIMIT 5
");

?>

<style>
    /* Jarak konten dashboard */
    .dashboard-superadmin {
        padding: 24px;
    }

    /* Card statistik */
    .dashboard-superadmin .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dashboard-superadmin .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .08) !important;
    }

    .dashboard-superadmin .stat-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .dashboard-superadmin .stat-number {
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 8px;
    }

    .dashboard-superadmin .stat-description {
        color: #6c757d;
        font-size: 12px;
    }

    /* Card tabel */
    .dashboard-superadmin .account-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .dashboard-superadmin .account-header {
        padding: 18px 22px;
        background: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    .dashboard-superadmin .account-table {
        width: 100%;
        table-layout: fixed;
        margin-bottom: 0;
    }

    .dashboard-superadmin .account-table th,
    .dashboard-superadmin .account-table td {
        vertical-align: middle;
        padding: 14px 12px;
        font-size: 13px;
    }

    .dashboard-superadmin .account-table th {
        font-weight: 600;
        color: #495057;
        white-space: normal;
    }

    .dashboard-superadmin .account-table .col-no {
        width: 7%;
        text-align: center;
    }

    .dashboard-superadmin .account-table .col-nama {
        width: 23%;
    }

    .dashboard-superadmin .account-table .col-username {
        width: 20%;
    }

    .dashboard-superadmin .account-table .col-role {
        width: 17%;
        text-align: center;
    }

    .dashboard-superadmin .account-table .col-tanggal {
        width: 33%;
    }

    .dashboard-superadmin .account-table .text-cell {
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .dashboard-superadmin .account-table .date-cell {
        white-space: nowrap;
        font-size: 12px;
    }

    .dashboard-superadmin .account-table .badge {
        white-space: normal;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .dashboard-superadmin {
            padding: 15px;
        }

        .dashboard-superadmin .stat-number {
            font-size: 27px;
        }

        .dashboard-superadmin .account-table {
            min-width: 700px;
            table-layout: fixed;
        }
    }
</style>

<div class="container-fluid dashboard-superadmin">

    <!-- =====================================
         JUDUL DASHBOARD
    ====================================== -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">
            Dashboard Super Admin
        </h2>

        <p class="text-muted mb-0">
            Selamat datang,
            <strong>
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Super Admin'); ?>
            </strong>
        </p>
    </div>

    <!-- =====================================
         CARD STATISTIK
    ====================================== -->
    <div class="row g-3 mb-4">

        <!-- TOTAL AKUN PENGGUNA -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 text-center">

                    <div class="stat-label">
                        Total Akun Pengguna
                    </div>

                    <div class="stat-number text-dark">
                        <?= (int) $totalUser; ?>
                    </div>

                    <div class="stat-description">
                        Seluruh akun yang terdaftar
                    </div>

                </div>
            </div>
        </div>

        <!-- TOTAL ADMIN -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 text-center">

                    <div class="stat-label">
                        Total Admin
                    </div>

                    <div class="stat-number text-primary">
                        <?= (int) $totalAdmin; ?>
                    </div>

                    <div class="stat-description">
                        Akun dengan role Admin
                    </div>

                </div>
            </div>
        </div>

        <!-- TOTAL WALI SANTRI -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4 text-center">

                    <div class="stat-label">
                        Total Wali Santri
                    </div>

                    <div class="stat-number" style="color: #198754;">
                        <?= (int) $totalWali; ?>
                    </div>

                    <div class="stat-description">
                        Akun dengan role Wali Santri
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- =====================================
         TABEL AKUN TERBARU
    ====================================== -->
    <div class="card account-card shadow-sm">

        <div class="account-header">
            <h5 class="fw-bold mb-1">
                Akun Terbaru
            </h5>

            <small class="text-muted">
                Lima akun dengan ID terbaru yang terdaftar di sistem.
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover account-table">

                    <colgroup>
                        <col style="width: 7%;">
                        <col style="width: 23%;">
                        <col style="width: 20%;">
                        <col style="width: 17%;">
                        <col style="width: 33%;">
                    </colgroup>

                    <thead class="table-light">
                        <tr>
                            <th class="col-no">No.</th>
                            <th class="col-nama">Nama Pengguna</th>
                            <th class="col-username">Username</th>
                            <th class="col-role">Role</th>
                            <th class="col-tanggal">Tanggal Dibuat</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($qAkunTerbaru && mysqli_num_rows($qAkunTerbaru) > 0): ?>

                            <?php $no = 1; ?>

                            <?php while ($akun = mysqli_fetch_assoc($qAkunTerbaru)): ?>

                                <?php
                                $warnaRole = 'secondary';

                                if ($akun['role'] === 'Admin') {
                                    $warnaRole = 'primary';
                                } elseif ($akun['role'] === 'Wali Santri') {
                                    $warnaRole = 'success';
                                } elseif ($akun['role'] === 'Super Admin') {
                                    $warnaRole = 'warning';
                                }
                                ?>

                                <tr>

                                    <td class="col-no">
                                        <?= $no++; ?>
                                    </td>

                                    <td class="col-nama text-cell">
                                        <?= htmlspecialchars($akun['nama']); ?>
                                    </td>

                                    <td class="col-username text-cell">
                                        <?= htmlspecialchars($akun['username']); ?>
                                    </td>

                                    <td class="col-role">
                                        <span class="badge text-bg-<?= $warnaRole; ?>">
                                            <?= htmlspecialchars($akun['role']); ?>
                                        </span>
                                    </td>

                                    <td class="col-tanggal">
                                        <span class="date-cell d-inline-block">
                                            <?php
                                            if (!empty($akun['created_at'])) {
                                                echo date(
                                                    'd-m-Y H:i',
                                                    strtotime($akun['created_at'])
                                                );
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-4">
                                    Belum ada data akun.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>

<?php
include "../includes/footer.php";
?>