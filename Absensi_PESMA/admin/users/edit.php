<?php

$roleWajib = 'admin';

include "../config/session.php";
include "../config/koneksi.php";

/*=========================================
AMBIL DATA
=========================================*/

$id_user  = intval($_POST['id_user']);
$nama     = trim(mysqli_real_escape_string($conn, $_POST['nama']));
$username = trim(mysqli_real_escape_string($conn, $_POST['username']));
$password = trim($_POST['password']);
$role     = trim(mysqli_real_escape_string($conn, $_POST['role']));

/*=========================================
VALIDASI
=========================================*/

if (
    empty($id_user) ||
    empty($nama) ||
    empty($username) ||
    empty($role)
) {

    echo "
    <script>
        alert('Data belum lengkap.');
        window.location='index.php';
    </script>
    ";

    exit;
}

/*=========================================
CEK USER
=========================================*/

$query = mysqli_query($conn,"
    SELECT *
    FROM users
    WHERE id_user='$id_user'
");

if(mysqli_num_rows($query)==0){

    echo "
    <script>
        alert('User tidak ditemukan.');
        window.location='index.php';
    </script>
    ";

    exit;
}

/*=========================================
USERNAME DUPLIKAT
=========================================*/

$cek = mysqli_query($conn,"
    SELECT id_user
    FROM users
    WHERE username='$username'
    AND id_user!='$id_user'
");

if(mysqli_num_rows($cek)>0){

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>

    Swal.fire({

        icon:'warning',
        title:'Username sudah digunakan',
        confirmButtonColor:'#005BAC'

    }).then(()=>{

        window.location='index.php';

    });

    </script>
    ";

    exit;
}

/*=========================================
PASSWORD DIUBAH?
=========================================*/

if(!empty($password)){

    if(strlen($password)<8){

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

        <script>

        Swal.fire({

            icon:'warning',
            title:'Password minimal 8 karakter',
            confirmButtonColor:'#005BAC'

        }).then(()=>{

            window.location='index.php';

        });

        </script>
        ";

        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $update = mysqli_query($conn,"
        UPDATE users
        SET
            nama='$nama',
            username='$username',
            password='$passwordHash',
            role='$role'
        WHERE id_user='$id_user'
    ");

}else{

    $update = mysqli_query($conn,"
        UPDATE users
        SET
            nama='$nama',
            username='$username',
            role='$role'
        WHERE id_user='$id_user'
    ");

}

/*=========================================
HASIL
=========================================*/

if($update){

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>

    Swal.fire({

        icon:'success',
        title:'Berhasil',
        text:'Data pengguna berhasil diperbarui.',
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