<?php

$roleWajib = 'admin';

include "../config/session.php";
include "../config/koneksi.php";

include "../includes/header.php";
include "../includes/sidebar.php";
include "../includes/navbar.php";

// Ambil ID User dan Role dari Session
$id_user = $_SESSION['id_user'] ?? '';
$role    = strtolower($_SESSION['role'] ?? 'mahasiswi');

// Inisialisasi variabel profil
$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$username  = $_SESSION['username'] ?? '-';
$data_detail = null;

if ($role === 'mahasiswi') {
    // Ambil Data Detail Mahasiswi (NIM, Prodi, Kamar, No HP, Alamat)
    $query_mhs = "
        SELECT 
            mahasiswi.*, 
            prodi.nama_prodi, 
            kamar.nomor_kamar, 
            kamar.lantai,
            users.username
        FROM mahasiswi
        LEFT JOIN prodi ON prodi.id_prodi = mahasiswi.id_prodi
        LEFT JOIN kamar ON kamar.id_kamar = mahasiswi.id_kamar
        LEFT JOIN users ON users.id_user = mahasiswi.id_user
        WHERE mahasiswi.id_user = '$id_user' OR users.id_user = '$id_user'
    ";
    $sql_mhs = mysqli_query($conn, $query_mhs);
    if ($sql_mhs && mysqli_num_rows($sql_mhs) > 0) {
        $data_detail = mysqli_fetch_assoc($sql_mhs);
        $nama_user   = $data_detail['nama'];
        $username    = $data_detail['username'] ?? $username;
    }
} else {
    // Ambil Data Admin
    $query_admin = "SELECT * FROM users WHERE id_user = '$id_user'";
    $sql_admin   = mysqli_query($conn, $query_admin);
    if ($sql_admin && mysqli_num_rows($sql_admin) > 0) {
        $data_detail = mysqli_fetch_assoc($sql_admin);
        $nama_user   = $data_detail['nama'];
        $username    = $data_detail['username'];
    }
}

// Generasi Inisial Nama untuk Avatar
$words = explode(" ", $nama_user);
$initials = "";
if (count($words) >= 2) {
    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
} else {
    $initials = strtoupper(substr($nama_user, 0, 2));
}
?>

<!-- Header Section -->
<div class="card-body mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-primary mb-1">Profil Saya</h2>
            <p class="text-muted mb-0">Detail informasi akun dan identitas pengguna sistem</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Card Kiri: Foto/Avatar & Ringkasan -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 text-center p-4 h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <!-- Avatar Circle -->
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm mb-3" style="width: 90px; height: 90px; font-size: 2rem;">
                    <?= $initials; ?>
                </div>
                
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($nama_user); ?></h5>
                <span class="badge bg-primary text-uppercase px-3 py-2 mb-3">
                    <?= htmlspecialchars($role); ?>
                </span>

                <?php if ($role === 'mahasiswi' && $data_detail): ?>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fa-solid fa-graduation-cap me-1 text-primary"></i> <?= htmlspecialchars($data_detail['nama_prodi'] ?? '-'); ?>
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fa-solid fa-door-closed me-1 text-primary"></i> Kamar <?= htmlspecialchars($data_detail['nomor_kamar'] ?? '-'); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card Kanan: Detail Biodata -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2"></i>Informasi Akun</h6>
                <a href="../password/" class="btn btn-light btn-sm fw-semibold text-primary">
                    <i class="fa-solid fa-key me-1"></i> Ganti Password
                </a>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nama Lengkap</label>
                        <div class="fw-bold text-dark p-2 bg-light rounded border-start border-3 border-primary">
                            <?= htmlspecialchars($nama_user); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Username Login</label>
                        <div class="fw-bold text-dark p-2 bg-light rounded border-start border-3 border-primary">
                            <code><?= htmlspecialchars($username); ?></code>
                        </div>
                    </div>

                    <?php if ($role === 'mahasiswi' && $data_detail): ?>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase d-block mb-1">NIM (Nomor Induk Mahasiswa)</label>
                            <div class="fw-bold text-dark p-2 bg-light rounded">
                                <?= htmlspecialchars($data_detail['nim'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Program Studi</label>
                            <div class="fw-bold text-dark p-2 bg-light rounded">
                                <?= htmlspecialchars($data_detail['nama_prodi'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nomor Kamar Asrama</label>
                            <div class="fw-bold text-dark p-2 bg-light rounded">
                                Kamar <?= htmlspecialchars($data_detail['nomor_kamar'] ?? '-'); ?> (Lantai <?= htmlspecialchars($data_detail['lantai'] ?? '-'); ?>)
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Nomor HP / WA</label>
                            <div class="fw-bold text-dark p-2 bg-light rounded">
                                <?= htmlspecialchars($data_detail['no_hp'] ?? '-'); ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Alamat Domisili / Asal</label>
                            <div class="fw-bold text-dark p-2 bg-light rounded">
                                <?= htmlspecialchars($data_detail['alamat'] ?? '-'); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase d-block mb-1">Hak Akses / Role</label>
                        <div class="fw-bold text-dark p-2 bg-light rounded">
                            <span class="badge bg-success"><?= strtoupper(htmlspecialchars($role)); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../includes/footer.php";
?>