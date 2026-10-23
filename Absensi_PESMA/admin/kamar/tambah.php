<?php

$roleWajib = 'Admin';

include "../../config/session.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";
include "../../includes/navbar.php";

?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4>Tambah Data Kamar</h4>
    </div>

    <div class="card-body">
        <form action="proses_tambah.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nomor Kamar</label>
                <input
                    type="text"
                    name="nomor_kamar"
                    class="form-control"
                    placeholder="Contoh : 201"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Lantai</label>
                <select name="lantai" class="form-select" required>
                    <option value="">-- Pilih Lantai --</option>
                    <option value="2">Lantai 2</option>
                    <option value="3">Lantai 3</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kapasitas</label>
                <input
                    type="number"
                    name="kapasitas"
                    class="form-control"
                    value="4"
                    required>
            </div>

            <button class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
</div>

<?php

include "../../includes/footer.php";

?>
