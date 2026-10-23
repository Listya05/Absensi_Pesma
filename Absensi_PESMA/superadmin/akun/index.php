<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validasi akses Super Admin
if (
    !isset($_SESSION['login']) ||
    $_SESSION['login'] !== true ||
    ($_SESSION['role'] ?? '') !== 'Super Admin'
) {
    header("Location: /absensi_PESMA/auth/login.php?pesan=akses");
    exit;
}

require_once "../../config/koneksi.php";

// Data form
$pesan = $_GET['pesan'] ?? '';
// UBAH DARI 'tambah' MENJADI 'list'
$mode = $_GET['mode'] ?? 'list'; 
$idEdit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$dataEdit = [
    'id_user' => 0,
    'nama' => '',
    'username' => '',
    'role' => 'Admin'
];

// Ambil akun yang akan diedit
if ($mode === 'edit' && $idEdit > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_user, nama, username, role
         FROM users
         WHERE id_user = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $idEdit);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($stmt);

    if ($data) {
        $dataEdit = $data;
    } else {
        header("Location: index.php?pesan=tidak_ditemukan");
        exit;
    }
}

// Ambil daftar akun (Hanya jika mode = list)
$daftarAkun = [];
if ($mode === 'list') {
    $sql = "
        SELECT id_user, nama, username, role, created_at
        FROM users
        ORDER BY
            CASE role
                WHEN 'Super Admin' THEN 1
                WHEN 'Admin' THEN 2
                WHEN 'Wali Santri' THEN 3
                ELSE 4
            END,
            nama ASC
    ";

    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $daftarAkun[] = $row;
        }
    }
}

// Fungsi bantuan
function e($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function warnaRole($role)
{
    if ($role === 'Super Admin') return 'bg-warning-subtle text-dark';
    if ($role === 'Admin') return 'bg-primary-subtle text-primary';
    if ($role === 'Wali Santri') return 'bg-success-subtle text-success';
    return 'bg-secondary-subtle text-secondary';
}

function tanggalAkun($tanggal)
{
    if (empty($tanggal)) return '-';

    $waktu = strtotime($tanggal);
    return $waktu ? date('d-m-Y H:i', $waktu) : '-';
}

$notifikasi = [
    'tambah' => ['success', 'Akun berhasil ditambahkan.'],
    'edit' => ['success', 'Data akun berhasil diperbarui.'],
    'hapus' => ['success', 'Akun berhasil dihapus.'],
    'gagal' => ['danger', 'Proses gagal. Periksa kembali data akun.'],
    'username' => ['warning', 'Username sudah digunakan akun lain.'],
    'tidak_ditemukan' => ['warning', 'Data akun tidak ditemukan.'],
    'dilarang' => ['warning', 'Akun tersebut tidak dapat diubah atau dihapus.'],
    'terkait' => ['warning', 'Akun tidak dapat dihapus karena masih memiliki data terkait.']
];

$isEdit = $mode === 'edit';

// Gunakan komponen layout bersama
include '../../includes/header.php';
include '../../includes/sidebar_spradmin.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid py-3">

    <!-- Judul halaman -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Manajemen Akun</h2>
            <p class="text-muted mb-0">
                Kelola akun Admin dan Wali Santri PESMA Al-Ma'soem.
            </p>
        </div>

        <!-- Tombol dinamis tergantung mode -->
        <?php if ($mode === 'list'): ?>
            <a href="index.php?mode=tambah" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Tambah Akun
            </a>
        <?php else: ?>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <!-- Notifikasi -->
    <?php if (isset($notifikasi[$pesan])): ?>
        <div class="alert alert-<?= e($notifikasi[$pesan][0]); ?> alert-dismissible fade show">
            <?= e($notifikasi[$pesan][1]); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form tambah/edit (HANYA MUNCUL JIKA MODE TAMBAH ATAU EDIT) -->
    <?php if ($mode === 'tambah' || $mode === 'edit'): ?>
    <div class="card mb-4">
        <div class="card-body">

            <h5 class="fw-bold mb-1">
                <?= $isEdit ? 'Edit Akun Pengguna' : 'Tambah Akun Baru'; ?>
            </h5>

            <p class="text-muted small mb-4">
                <?= $isEdit
                    ? 'Perbarui informasi akun pengguna.'
                    : 'Masukkan informasi akun yang akan didaftarkan.'; ?>
            </p>

            <form action="proses.php" method="POST">
                <input type="hidden" name="aksi" value="<?= $isEdit ? 'edit' : 'tambah'; ?>">
                <input type="hidden" name="id_user" value="<?= (int) $dataEdit['id_user']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control" maxlength="100"
                               value="<?= e($dataEdit['nama']); ?>" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="col-md-6">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" id="username" name="username" class="form-control" maxlength="50"
                               value="<?= e($dataEdit['username']); ?>" placeholder="Masukkan username" required>
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-semibold">Role Pengguna</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="Admin" <?= $dataEdit['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="Wali Santri" <?= $dataEdit['role'] === 'Wali Santri' ? 'selected' : ''; ?>>Wali Santri</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">
                            Password <?= $isEdit ? '(Opsional)' : ''; ?>
                        </label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8"
                               autocomplete="new-password"
                               placeholder="<?= $isEdit ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter'; ?>"
                               <?= $isEdit ? '' : 'required'; ?>>
                        <small class="text-muted">
                            <?= $isEdit ? 'Kosongkan jika tetap menggunakan password lama.' : 'Password minimal 8 karakter.'; ?>
                        </small>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Akun'; ?>
                        </button>

                        <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daftar akun (HANYA MUNCUL JIKA MODE LIST) -->
    <?php if ($mode === 'list'): ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Akun Pengguna</h5>
                    <p class="text-muted small mb-0">Daftar pengguna yang terdaftar dalam sistem.</p>
                </div>
                <span class="badge bg-light text-dark border">
                    Total: <?= count($daftarAkun); ?> akun
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daftarAkun)): ?>
                            <?php foreach ($daftarAkun as $i => $row): ?>
                                <?php
                                $idUser = (int) $row['id_user'];
                                $akunSendiri = $idUser === (int) ($_SESSION['id_user'] ?? 0);
                                ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1; ?></td>
                                    <td><?= e($row['nama']); ?></td>
                                    <td><?= e($row['username']); ?></td>
                                    <td>
                                        <span class="badge <?= warnaRole($row['role']); ?>">
                                            <?= e($row['role']); ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap"><?= e(tanggalAkun($row['created_at'])); ?></td>
                                    <td>
                                        <?php if (!$akunSendiri): ?>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="index.php?mode=edit&id=<?= $idUser; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="hapus.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                                    <input type="hidden" name="id_user" value="<?= $idUser; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center">
                                                <span class="text-muted small">Akun Anda</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada akun pengguna.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include '../../includes/footer.php'; ?>