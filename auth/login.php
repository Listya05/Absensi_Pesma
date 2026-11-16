
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan sesuai role
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    switch ($_SESSION['role'] ?? '') {
        case 'Super Admin':
            header("Location: ../superadmin/dashboard.php");
            exit;

        case 'Admin':
            header("Location: ../admin/dashboard.php");
            exit;

        case 'Wali Santri':
            header("Location: ../wali_santri/dashboard.php");
            exit;

        default:
            // Jika role tidak dikenali, hapus sesi
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - SIM Absensi PESMA</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f7fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
            padding: 32px;
        }

        .logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        .login-title {
            color: #17324d;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.4;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 13px;
        }

        .form-label {
            color: #344054;
            font-size: 14px;
        }

        .form-control {
            min-height: 44px;
            border-radius: 8px;
            border-color: #d7deea;
        }

        .form-control:focus {
            border-color: #005BAC;
            box-shadow: 0 0 0 .2rem rgba(0, 91, 172, .12);
        }

        .input-group .form-control {
            border-radius: 8px 0 0 8px;
        }

        .input-group .btn {
            border-radius: 0 8px 8px 0;
        }

        .btn-login {
            background: #005BAC;
            color: #fff;
            border: none;
            border-radius: 8px;
            min-height: 46px;
            font-weight: 600;
            transition: .2s;
        }

        .btn-login:hover {
            background: #00448a;
            color: #fff;
        }

        .login-footer {
            font-size: 12px;
            color: #98a2b3;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 24px 20px;
            }

            .login-title {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <main class="login-card">

        <img
            src="../assets/img/logo.png"
            class="logo"
            alt="Logo PESMA Al-Ma'soem">

        <h1 class="text-center mt-4 login-title">
            Sistem Informasi Absensi Mahasiswi
        </h1>

        <p class="text-center login-subtitle mb-4">
            Pesantren Mahasiswa Al-Ma'soem
        </p>

        <?php if (isset($_GET['pesan'])): ?>

            <?php if ($_GET['pesan'] === 'gagal'): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    Username atau password salah. Silakan periksa kembali.
                </div>

            <?php elseif ($_GET['pesan'] === 'logout'): ?>
                <div class="alert alert-success py-2 small" role="alert">
                    Kamu berhasil keluar dari sistem.
                </div>

            <?php elseif ($_GET['pesan'] === 'akses'): ?>
                <div class="alert alert-warning py-2 small" role="alert">
                    Kamu tidak memiliki akses ke halaman tersebut.
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <form action="login_proses.php" method="POST">

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control"
                    required
                    maxlength="50"
                    autocomplete="username"
                    placeholder="Masukkan username"
                    autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">
                    Password
                </label>

                <div class="input-group">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password">

                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        id="togglePassword"
                        aria-label="Tampilkan password"
                        title="Tampilkan atau sembunyikan password">

                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Login
            </button>

        </form>

        <div class="text-center mt-4 login-footer">
            &copy; <?php echo date('Y'); ?> PESMA Al-Ma'soem
        </div>

    </main>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");
        const icon = togglePassword.querySelector("i");

        togglePassword.addEventListener("click", function () {
            const sedangTampil = password.type === "text";

            password.type = sedangTampil ? "password" : "text";

            icon.classList.toggle("fa-eye", sedangTampil);
            icon.classList.toggle("fa-eye-slash", !sedangTampil);

            togglePassword.setAttribute(
                "aria-label",
                sedangTampil ? "Tampilkan password" : "Sembunyikan password"
            );
        });
    </script>

</body>
</html>
