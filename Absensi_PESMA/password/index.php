
<?php
include "../config/session.php";
include "../config/koneksi.php";

// Data pengguna
$role = strtolower(trim($_SESSION['role'] ?? ''));
$nama_user = $_SESSION['nama'] ?? 'User';

// Sidebar dan dashboard sesuai role
switch ($role) {
    case 'super admin':
        $sidebar = "../includes/sidebar_spradmin.php";
        $dashboard = "../superadmin/dashboard.php";
        $nama_role = "Super Admin";
        break;

    case 'admin':
        $sidebar = "../includes/sidebar.php";
        $dashboard = "../admin/dashboard.php";
        $nama_role = "Admin";
        break;

    case 'wali santri':
        $sidebar = "../includes/sidebar_wali_santri.php";
        $dashboard = "../wali_santri/dashboard.php";
        $nama_role = "Wali Santri";
        break;

    default:
        header("Location: ../auth/logout.php");
        exit;
}

include "../includes/header.php";
include $sidebar;
include "../includes/navbar.php";
?>

<!-- Header halaman -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-4">
        <h2 class="fw-bold text-primary mb-1">
            Ganti Password
        </h2>

        <p class="text-muted mb-0">
            Ubah password akun <?= htmlspecialchars($nama_role); ?>
        </p>
    </div>
</div>

<!-- Notifikasi dari proses update -->
<?php if (isset($_GET['pesan'])): ?>
    <?php
    $pesan = $_GET['pesan'];

    $daftar_pesan = [
        'berhasil' => [
            'jenis' => 'success',
            'teks' => 'Password berhasil diperbarui.'
        ],
        'password_salah' => [
            'jenis' => 'danger',
            'teks' => 'Password lama yang dimasukkan salah.'
        ],
        'tidak_cocok' => [
            'jenis' => 'danger',
            'teks' => 'Konfirmasi password baru tidak cocok.'
        ],
        'gagal' => [
            'jenis' => 'danger',
            'teks' => 'Password gagal diperbarui.'
        ],
        'pendek' => [
            'jenis' => 'warning',
            'teks' => 'Password baru minimal 8 karakter.'
        ]
    ];
    ?>

    <?php if (isset($daftar_pesan[$pesan])): ?>
        <div class="alert alert-<?= $daftar_pesan[$pesan]['jenis']; ?> alert-dismissible fade show">
            <?= htmlspecialchars($daftar_pesan[$pesan]['teks']); ?>
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Form ganti password -->
<div class="card shadow-sm border-0">
    <div class="card-body">

        <form action="update.php" method="POST" autocomplete="off">

            <!-- Password lama -->
            <div class="mb-3">
                <label for="password_lama" class="form-label fw-semibold">
                    Password Lama
                </label>

                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password_lama"
                        name="password_lama"
                        required>

                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        onclick="togglePassword('password_lama', this)"
                        aria-label="Tampilkan password lama">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Password baru -->
            <div class="mb-3">
                <label for="password_baru" class="form-label fw-semibold">
                    Password Baru
                </label>

                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password_baru"
                        name="password_baru"
                        minlength="8"
                        required>

                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        onclick="togglePassword('password_baru', this)"
                        aria-label="Tampilkan password baru">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <small class="text-muted">Minimal 8 karakter.</small>
                <div id="strength" class="mt-2 fw-semibold"></div>
            </div>

            <!-- Konfirmasi password -->
            <div class="mb-4">
                <label for="konfirmasi" class="form-label fw-semibold">
                    Konfirmasi Password Baru
                </label>

                <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="konfirmasi"
                        name="konfirmasi"
                        minlength="8"
                        required>

                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        onclick="togglePassword('konfirmasi', this)"
                        aria-label="Tampilkan konfirmasi password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div id="match" class="mt-2 fw-semibold"></div>
            </div>

            <!-- Tombol -->
            <div class="text-end d-flex justify-content-end gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-key me-1"></i>
                    Simpan Password
                </button>

                <a href="<?= htmlspecialchars($dashboard); ?>"
                   class="btn btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

<script>
// Tampilkan atau sembunyikan password
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// Indikator kekuatan password
const passwordBaru = document.getElementById("password_baru");
const konfirmasi = document.getElementById("konfirmasi");

passwordBaru.addEventListener("input", function () {
    const value = this.value;
    const strength = document.getElementById("strength");

    if (value.length === 0) {
        strength.textContent = "";
    } else if (value.length < 8) {
        strength.textContent = "Password terlalu pendek";
        strength.className = "text-danger fw-semibold mt-2";
    } else if (
        /[A-Z]/.test(value) &&
        /[a-z]/.test(value) &&
        /[0-9]/.test(value)
    ) {
        strength.textContent = "Password kuat";
        strength.className = "text-success fw-semibold mt-2";
    } else {
        strength.textContent = "Password sedang";
        strength.className = "text-warning fw-semibold mt-2";
    }

    cekKonfirmasi();
});

// Periksa kesesuaian password
function cekKonfirmasi() {
    const match = document.getElementById("match");

    if (konfirmasi.value === "") {
        match.textContent = "";
        return;
    }

    if (passwordBaru.value === konfirmasi.value) {
        match.textContent = "Password cocok";
        match.className = "text-success fw-semibold mt-2";
    } else {
        match.textContent = "Password tidak cocok";
        match.className = "text-danger fw-semibold mt-2";
    }
}

konfirmasi.addEventListener("input", cekKonfirmasi);

// Cegah pengiriman jika konfirmasi tidak cocok
document.querySelector("form").addEventListener("submit", function (e) {
    if (passwordBaru.value !== konfirmasi.value) {
        e.preventDefault();
        alert("Konfirmasi password baru tidak cocok.");
        konfirmasi.focus();
    }
});
</script>

<?php include "../includes/footer.php"; ?>
