<?php

$roleWajib = 'admin';

include "../../config/session.php";
include "../../config/koneksi.php";

include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";

/*=========================================
PENCARIAN
=========================================*/

$keyword = isset($_GET['keyword'])
    ? mysqli_real_escape_string($conn, $_GET['keyword'])
    : "";

$query_users = mysqli_query($conn,"
    SELECT *
    FROM users
    WHERE
        username LIKE '%$keyword%'
        OR nama LIKE '%$keyword%'
        OR role LIKE '%$keyword%'
    ORDER BY role ASC,nama ASC
");

?>

<!-- Header -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Data Pengguna</h2>
    <p class="text-muted mb-0">
        Daftar Akun & Hak Akses Pengguna PESMA Al-Ma'soem
    </p>
</div>

<!-- Search -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" id="formSearch">
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <div class="flex-grow-1" style="max-width:350px">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="Cari username, nama, atau role..."
                        value="<?= htmlspecialchars($keyword) ?>">
                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahUser">
                    <i class="fa-solid fa-plus me-1"></i>
                    Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th width="60">No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th width="170">Role</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no=1;
                if(mysqli_num_rows($query_users)>0){
                    while($u=mysqli_fetch_assoc($query_users)){
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <code><?= htmlspecialchars($u['username']) ?></code>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($u['nama']) ?></strong>
                        </td>
                        <td class="text-center">
                            <?php
                            if($u['role']=="admin"){
                                echo '<span class="badge bg-primary">Administrator</span>';
                            }else{
                                echo '<span class="badge bg-info text-dark">Mahasiswi</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit<?= $u['id_user'] ?>">
                                Edit
                            </button>

                            <button
                                class="btn btn-info btn-sm text-white"
                                data-bs-toggle="modal"
                                data-bs-target="#modalReset<?= $u['id_user'] ?>">
                                Reset Password
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $u['id_user'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="edit.php" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit Pengguna</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
                                        <div class="mb-3">
                                            <label>Nama Lengkap</label>
                                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($u['nama']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Role</label>
                                            <select class="form-select" name="role">
                                                <option value="admin" <?= $u['role']=="admin" ? "selected" : "" ?>>Administrator</option>
                                                <option value="mahasiswi" <?= $u['role']=="mahasiswi" ? "selected" : "" ?>>Mahasiswi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                                        <button class="btn btn-primary" type="submit">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Reset Password -->
                    <div class="modal fade" id="modalReset<?= $u['id_user'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="reset_password.php" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Reset Password</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
                                        <div class="mb-3">
                                            <label>Password Baru</label>
                                            <input type="password" class="form-control" name="password" placeholder="Masukkan password baru" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Konfirmasi Password</label>
                                            <input type="password" class="form-control" name="konfirmasi" placeholder="Ulangi password baru" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                                        <button class="btn btn-primary" type="submit">Reset Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php
                    }
                }else{
                ?>
                    <tr>
                        <td colspan="5" class="text-center">Data pengguna tidak ditemukan.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="tambah.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Pengguna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select class="form-select" name="role">
                            <option value="admin">Administrator</option>
                            <option value="mahasiswi" selected>Mahasiswi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const keyword = document.querySelector('input[name="keyword"]');
let timer;

keyword.addEventListener("keyup", function(){
    clearTimeout(timer);
    timer = setTimeout(function(){
        document.getElementById("formSearch").submit();
    }, 300);
});
</script>

<?php
include "../../includes/footer.php";
?>