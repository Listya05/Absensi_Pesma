<?php

$roleWajib = 'admin';

include "../config/session.php";
include "../config/koneksi.php";

// =========================================
// Ambil Data
// =========================================

$nama     = trim(mysqli_real_escape_string($conn, $_POST['nama']));
$username = trim(mysqli_real_escape_string($conn, $_POST['username']));
$password = $_POST['password'];
$role     = trim(mysqli_real_escape_string($conn, $_POST['role']));

// =========================================
// Validasi
// =========================================

if (
    empty($nama) ||
    empty($username) ||
    empty($password) ||
    empty($role)
) {

    echo "
    <script>
        alert('Semua data wajib diisi.');
        window.location='index.php';
    </script>
    ";

    exit;
}

// =========================================
// Password minimal
// =========================================

if (strlen($password) < 8) {

    echo "
    <script>
        alert('Password minimal 8 karakter.');
        window.location='index.php';
    </script>
    ";

    exit;
}

// =========================================
// Username sudah digunakan?
// =========================================

$cek = mysqli_query($conn,"
    SELECT id_user
    FROM users
    WHERE username='$username'
");

if(mysqli_num_rows($cek)>0){

    echo "
    <script>
        alert('Username sudah digunakan.');
        window.location='index.php';
    </script>
    ";

    exit;

}

// =========================================
// Hash Password
// =========================================

$passwordHash = password_hash($password,PASSWORD_DEFAULT);

// =========================================
// Simpan
// =========================================

$simpan = mysqli_query($conn,"
    INSERT INTO users
    (
        username,
        nama,
        password,
        role
    )
    VALUES
    (
        '$username',
        '$nama',
        '$passwordHash',
        '$role'
    )
");

if($simpan){

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>

    Swal.fire({

        icon:'success',
        title:'Berhasil',
        text:'Pengguna berhasil ditambahkan.',
        confirmButtonColor:'#005BAC'

    }).then(()=>{

        window.location='index.php';

    });

    </script>
    ";

}else{

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>

    Swal.fire({

        icon:'error',
        title:'Gagal',
        text:'".mysqli_error($conn)."',
        confirmButtonColor:'#005BAC'

    }).then(()=>{

        window.location='index.php';

    });

    </script>
    ";

}

?>