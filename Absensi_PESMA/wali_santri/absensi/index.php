
<?php

$roleWajib = 'Wali Santri';

include "../../config/session.php";
include "../../config/koneksi.php";

// Filter
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$waktu   = $_GET['waktu'] ?? 'Subuh';
$lantai  = $_GET['lantai'] ?? '';
$mode    = $_GET['mode'] ?? '';

// Validasi waktu
$daftarWaktu = ['Subuh', 'Magrib', 'Isya'];

if (!in_array($waktu, $daftarWaktu, true)) {
    $waktu = 'Subuh';
}

// Validasi lantai
$daftarLantai = ['', '2', '3', 'semua'];

if (!in_array($lantai, $daftarLantai, true)) {
    $lantai = '';
}

// Validasi mode
if (!in_array($mode, ['', 'edit'], true)) {
    $mode = '';
}

// Validasi tanggal
$tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);

if (
    !$tanggalObj ||
    $tanggalObj->format('Y-m-d') !== $tanggal
) {
    $tanggal = date('Y-m-d');
}

include "../../includes/header.php";
include "../../includes/sidebar_walisantri.php";
include "../../includes/navbar.php";
?>

<style>
    .card-kamar {
        border: 1px solid #e9ecef !important;
        border-radius: 8px !important;
    }

    .header-kamar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1.25rem;
    }

    .table-clean th {
        background-color: #fff;
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }

    .table-clean td {
        vertical-align: middle;
        padding: 0.65rem 1rem;
    }

    .info-alert {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #495057;
        border-radius: 8px;
    }
</style>

<!-- Header -->
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Absensi Mahasiswi</h2>
    <p class="text-muted mb-0">
        Kelola kehadiran mahasiswi PESMA Al-Ma'soem.
    </p>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" id="formFilter">
            <div class="row align-items-center g-3">

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-semibold mb-1">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="form-control form-control-sm rounded-2"
                        value="<?= htmlspecialchars($tanggal); ?>"
                        onchange="this.form.submit()">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-semibold mb-1">
                        Waktu Salat
                    </label>

                    <select
                        name="waktu"
                        class="form-select form-select-sm rounded-2"
                        onchange="this.form.submit()">

                        <?php foreach ($daftarWaktu as $pilihanWaktu) { ?>
                            <option
                                value="<?= $pilihanWaktu; ?>"
                                <?= $waktu === $pilihanWaktu ? 'selected' : ''; ?>>
                                <?= $pilihanWaktu; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small fw-semibold mb-1">
                        Lantai
                    </label>

                    <select
                        name="lantai"
                        class="form-select form-select-sm rounded-2"
                        onchange="this.form.submit()">

                        <option value="" <?= $lantai === '' ? 'selected' : ''; ?>>
                            -- Pilih Lantai --
                        </option>

                        <option value="semua" <?= $lantai === 'semua' ? 'selected' : ''; ?>>
                            Semua Lantai
                        </option>

                        <option value="2" <?= $lantai === '2' ? 'selected' : ''; ?>>
                            Lantai 2
                        </option>

                        <option value="3" <?= $lantai === '3' ? 'selected' : ''; ?>>
                            Lantai 3
                        </option>
                    </select>
                </div>

            </div>
        </form>
    </div>
</div>

<?php

// Tampilkan absensi setelah lantai dipilih
if ($lantai !== '') {

    // Periksa apakah absensi tanggal dan waktu ini sudah ada
    $stmtAbsensi = mysqli_prepare(
        $conn,
        "SELECT id_absensi
         FROM absensi
         WHERE tanggal = ? AND waktu = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmtAbsensi,
        "ss",
        $tanggal,
        $waktu
    );

    mysqli_stmt_execute($stmtAbsensi);

    $hasilAbsensi = mysqli_stmt_get_result($stmtAbsensi);
    $dataAbsensi = mysqli_fetch_assoc($hasilAbsensi);

    mysqli_stmt_close($stmtAbsensi);

    $id_absensi = (int) ($dataAbsensi['id_absensi'] ?? 0);

    // Data terkunci jika sudah tersimpan dan belum masuk mode edit
    $is_locked = ($id_absensi > 0 && $mode !== 'edit');

    $label_lantai = ($lantai === 'semua')
        ? 'Semua Lantai'
        : 'Lantai ' . $lantai;
?>

<!-- Informasi absensi -->
<div class="p-3 info-alert mb-4">
    <span class="small">
        Menampilkan data untuk
        <strong><?= htmlspecialchars($label_lantai); ?></strong>
        &bull;
        <?= date('d M Y', strtotime($tanggal)); ?>
        (Salat <?= htmlspecialchars($waktu); ?>)
    </span>
</div>

<form
    action="<?= $id_absensi > 0 ? 'update.php' : 'simpan.php'; ?>"
    method="POST">

    <input type="hidden" name="tanggal"
           value="<?= htmlspecialchars($tanggal); ?>">

    <input type="hidden" name="waktu"
           value="<?= htmlspecialchars($waktu); ?>">

    <input type="hidden" name="lantai"
           value="<?= htmlspecialchars($lantai); ?>">

    <input type="hidden" name="id_absensi"
           value="<?= $id_absensi; ?>">

    <?php
    // Ambil kamar yang memiliki mahasiswi aktif
    if ($lantai === '2' || $lantai === '3') {

        $stmtKamar = mysqli_prepare(
            $conn,
            "SELECT DISTINCT k.id_kamar, k.nomor_kamar, k.lantai
             FROM kamar k
             INNER JOIN mahasiswi m ON m.id_kamar = k.id_kamar
             WHERE k.lantai = ? AND m.status = 'Aktif'
             ORDER BY k.lantai, k.nomor_kamar"
        );

        mysqli_stmt_bind_param($stmtKamar, "i", $lantai);

    } else {

        $stmtKamar = mysqli_prepare(
            $conn,
            "SELECT DISTINCT k.id_kamar, k.nomor_kamar, k.lantai
             FROM kamar k
             INNER JOIN mahasiswi m ON m.id_kamar = k.id_kamar
             WHERE m.status = 'Aktif'
             ORDER BY k.lantai, k.nomor_kamar"
        );
    }

    mysqli_stmt_execute($stmtKamar);
    $hasilKamar = mysqli_stmt_get_result($stmtKamar);

    $jumlahKamar = mysqli_num_rows($hasilKamar);

    if ($jumlahKamar === 0) {
        echo '<div class="alert alert-info">
            Belum ada kamar yang memiliki mahasiswi aktif pada lantai ini.
        </div>';
    }

    while ($k = mysqli_fetch_assoc($hasilKamar)) {
        $idKamar = (int) $k['id_kamar'];
    ?>

    <!-- Kartu kamar -->
    <div class="card card-kamar mb-4 border-0 shadow-sm">

        <div class="header-kamar">
            <h6 class="mb-0 fw-bold text-dark">
                Kamar <?= htmlspecialchars($k['nomor_kamar']); ?>
                <span class="text-muted fw-normal small">
                    (Lantai <?= (int) $k['lantai']; ?>)
                </span>
            </h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-clean align-middle mb-0">

                    <thead>
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Mahasiswi</th>
                            <th width="220" class="text-center">
                                Status Kehadiran
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $stmtMhs = mysqli_prepare(
                        $conn,
                        "SELECT
                            m.id_mahasiswi,
                            m.nama,
                            d.status AS status_absensi
                         FROM mahasiswi m
                         LEFT JOIN detail_absensi d
                            ON d.id_mahasiswi = m.id_mahasiswi
                            AND d.id_absensi = ?
                         WHERE m.id_kamar = ?
                            AND m.status = 'Aktif'
                         ORDER BY m.nama ASC"
                    );

                    mysqli_stmt_bind_param(
                        $stmtMhs,
                        "ii",
                        $id_absensi,
                        $idKamar
                    );

                    mysqli_stmt_execute($stmtMhs);
                    $hasilMhs = mysqli_stmt_get_result($stmtMhs);

                    $no = 1;

                    while ($m = mysqli_fetch_assoc($hasilMhs)) {
                        $idMahasiswi = (int) $m['id_mahasiswi'];

                        $statusTerpilih = $m['status_absensi'] ?? 'Hadir';

                        // Antisipasi data lama yang menggunakan Alpha
                        if ($statusTerpilih === 'Alpha') {
                            $statusTerpilih = 'Alpa';
                        }
                    ?>

                        <tr>
                            <td class="text-center text-muted small">
                                <?= $no++; ?>
                            </td>

                            <td class="fw-semibold text-dark">
                                <?= htmlspecialchars($m['nama']); ?>
                            </td>

                            <td class="text-center">
                                <input
                                    type="hidden"
                                    name="id_mahasiswi[]"
                                    value="<?= $idMahasiswi; ?>">

                                <select
                                    name="status[]"
                                    class="form-select form-select-sm text-center rounded-2"
                                    <?= $is_locked ? 'disabled' : ''; ?>>

                                    <?php
                                    $daftarStatus = [
                                        'Hadir',
                                        'Sakit',
                                        'Izin',
                                        'Alpa'
                                    ];

                                    foreach ($daftarStatus as $status) {
                                    ?>
                                        <option
                                            value="<?= $status; ?>"
                                            <?= $statusTerpilih === $status ? 'selected' : ''; ?>>
                                            <?= $status; ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </td>
                        </tr>

                    <?php
                    }

                    mysqli_stmt_close($stmtMhs);
                    ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <?php
    }

    mysqli_stmt_close($stmtKamar);
    ?>

    <!-- Tombol aksi -->
    <div class="card border-0 shadow-sm rounded-3 mb-5 bg-white">
        <div class="card-body p-3 d-flex flex-column flex-md-row
                    justify-content-between align-items-center gap-3">

            <div class="small text-muted">
                <?php if (!$id_absensi) { ?>
                    Data belum tersimpan. Periksa data sebelum menyimpan.

                <?php } elseif ($is_locked) { ?>
                    Mode pratinjau. Klik Edit Absensi untuk mengubah data.

                <?php } else { ?>
                    <span class="text-warning-emphasis">
                        Mode edit aktif. Simpan perubahan setelah selesai.
                    </span>
                <?php } ?>
            </div>

            <div class="d-flex flex-wrap gap-2">

                <?php if (!$id_absensi) { ?>

                    <button
                        type="submit"
                        class="btn btn-success px-4 fw-medium rounded-2">
                        Simpan Absensi
                    </button>

                <?php } elseif ($is_locked) { ?>

                    <a
                        href="index.php?tanggal=<?= urlencode($tanggal); ?>&waktu=<?= urlencode($waktu); ?>&lantai=<?= urlencode($lantai); ?>&mode=edit"
                        class="btn btn-warning text-dark px-4 fw-medium rounded-2">
                        Edit Absensi
                    </a>

                <?php } else { ?>

                    <a
                        href="index.php?tanggal=<?= urlencode($tanggal); ?>&waktu=<?= urlencode($waktu); ?>&lantai=<?= urlencode($lantai); ?>"
                        class="btn btn-secondary rounded-2">
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-warning text-dark px-4 fw-medium rounded-2">
                        Update Absensi
                    </button>

                <?php } ?>

            </div>
        </div>
    </div>

</form>

<?php
}
?>

<?php include "../../includes/footer.php"; ?>
