<?php
session_start();
include 'db.php';

$pesan = '';

// ================== PROSES LOGIN ==================
if (isset($_POST['submit'])) {

    $role = $_POST['role'] ?? '';

    // ====== LOGIN ADMIN ======
    if ($role == 'admin') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $cek = mysqli_query($conn, "
            SELECT * FROM admin 
            WHERE username = '$username' 
            AND password = '" . md5($password) . "'
        ");

        if ($cek && mysqli_num_rows($cek) > 0) {
            $data = mysqli_fetch_assoc($cek);

            $_SESSION['login']    = true;
            $_SESSION['role']     = 'admin';
            $_SESSION['username'] = $data['username'];

            header('Location: admin/dashboard.php');
            exit;
        } else {
            $pesan = 'Username atau Password salah!';
        }

    // ====== LOGIN SISWA ======
    } else {

        $nis = (int)$_POST['nis'];

        if ($nis <= 0) {
            $pesan = 'NIS tidak boleh kosong!';
        } else {

            $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = $nis");

            if ($cek && mysqli_num_rows($cek) > 0) {
                $data = mysqli_fetch_assoc($cek);

                $_SESSION['login'] = true;
                $_SESSION['role']  = 'siswa';
                $_SESSION['nis']   = $data['nis'];

                header('Location: siswa/dashboard.php');
                exit;

            } else {
                // AUTO DAFTAR
                mysqli_query($conn, "INSERT INTO siswa (nis) VALUES ($nis)");

                $_SESSION['login'] = true;
                $_SESSION['role']  = 'siswa';
                $_SESSION['nis']   = $nis;

                header('Location: siswa/dashboard.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Aspirasi Sekolah</title>

    <!-- ✅ AUTO FIX CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="http://localhost/css/style.css">

    <!-- BONUS STYLE -->
    <style>
        body#bg-login {
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            background:linear-gradient(135deg,#6a11cb,#2575fc);
        }

        .box-login {
            background:white;
            padding:25px;
            border-radius:12px;
            width:320px;
            box-shadow:0 5px 15px rgba(0,0,0,0.2);
            text-align:center;
        }

        .input-control {
            width:100%;
            padding:10px;
            margin:8px 0;
            border:1px solid #ccc;
            border-radius:5px;
        }

        .btn {
            background:#2575fc;
            color:white;
            border:none;
            padding:10px;
            border-radius:5px;
            cursor:pointer;
        }

        .pilih-role button {
            padding:8px 15px;
            margin:5px;
            border:none;
            cursor:pointer;
            border-radius:5px;
            background:#ccc;
        }

        .pilih-role .aktif {
            background:#2575fc;
            color:white;
        }

        .alert {
            background:#e74c3c;
            color:white;
            padding:8px;
            margin-bottom:10px;
            border-radius:5px;
        }

        .header-login {
            position:absolute;
            top:20px;
            text-align:center;
            width:100%;
            color:white;
        }
    </style>
</head>

<body id="bg-login">

<div class="header-login">
    <h1>RUMAH ASPIRASI SEKOLAH</h1>
</div>

<div class="box-login">
    <h2>Aspirasi Sekolah</h2>
    <p>Pilih login sebagai:</p>

    <?php if ($pesan != '') { ?>
        <div class="alert"><?php echo htmlspecialchars($pesan); ?></div>
    <?php } ?>

    <div class="pilih-role">
        <button id="btn-admin" class="aktif" onclick="pilihRole('admin'); return false;">Admin</button>
        <button id="btn-siswa" onclick="pilihRole('siswa'); return false;">Siswa</button>
    </div>

    <!-- FORM ADMIN -->
    <div id="form-admin">
        <form method="post">
            <input type="hidden" name="role" value="admin">
            <input type="text" name="username" class="input-control" placeholder="Username" required>
            <input type="password" name="password" class="input-control" placeholder="Password" required>
            <input type="submit" name="submit" class="btn" value="Login Admin" style="width:100%">
        </form>
    </div>

    <!-- FORM SISWA -->
    <div id="form-siswa" style="display:none">
        <form method="post">
            <input type="hidden" name="role" value="siswa">
            <input type="number" name="nis" class="input-control" placeholder="Masukkan NIS" required>
            <input type="submit" name="submit" class="btn" value="Masuk" style="width:100%">
        </form>
    </div>
</div>

<script>
function pilihRole(role) {
    if (role === 'admin') {
        document.getElementById('form-admin').style.display = 'block';
        document.getElementById('form-siswa').style.display = 'none';
        document.getElementById('btn-admin').classList.add('aktif');
        document.getElementById('btn-siswa').classList.remove('aktif');
    } else {
        document.getElementById('form-admin').style.display = 'none';
        document.getElementById('form-siswa').style.display = 'block';
        document.getElementById('btn-admin').classList.remove('aktif');
        document.getElementById('btn-siswa').classList.add('aktif');
    }
}

// BALIK KE SISWA KALAU ERROR
<?php if ($pesan != '' && isset($_POST['role']) && $_POST['role'] == 'siswa') { ?>
pilihRole('siswa');
<?php } ?>
</script>

</body>
</html>