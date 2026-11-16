<?php

$roleWajib = 'admin';

session_start();

include "../../config/koneksi.php";

$id          = $_POST['id_kamar'];
$nomor_kamar = $_POST['nomor_kamar'];
$lantai      = $_POST['lantai'];
$kapasitas   = $_POST['kapasitas'];

$cek = mysqli_query($conn, "
    SELECT *
    FROM kamar
    WHERE nomor_kamar='$nomor_kamar'
    AND id_kamar != '$id'
");

if (mysqli_num_rows($cek) > 0) {

?>

<script>
alert("Nomor kamar sudah digunakan.");
window.location = "edit.php?id=<?= $id ?>";
</script>

<?php

exit;

}

mysqli_query($conn, "
    UPDATE kamar
    SET
    nomor_kamar='$nomor_kamar',
    lantai='$lantai',
    kapasitas='$kapasitas'
    WHERE id_kamar='$id'
");

?>

<script>
alert("Data berhasil diperbarui.");
window.location = "index.php";
</script>