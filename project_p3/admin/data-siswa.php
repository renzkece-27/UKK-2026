<?php
session_start();
include '../db.php';

// ================== CEK LOGIN ==================
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// ================== TAMBAH SISWA ==================
if (isset($_POST['tambah'])) {
    $nis = (int)$_POST['nis'];

    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nis FROM siswa WHERE nis=$nis"));
    if ($cek) {
        echo '<script>alert("NIS sudah terdaftar!")</script>';
    } else {
        mysqli_query($conn, "INSERT INTO siswa (nis) VALUES ($nis)");
        echo '<script>alert("Siswa berhasil ditambahkan!")</script>';
    }
}

// ================== HAPUS SISWA ==================
if (isset($_GET['hapus'])) {
    $nis = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM siswa WHERE nis=$nis");
    echo "<script>window.location='data-siswa.php'</script>";
    exit;
}

// ================== AMBIL DATA ==================
$daftar = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nis ASC");

if (!$daftar) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Siswa</title>

    <!-- ✅ AUTO FIX CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- ✅ BACKUP kalau CSS di luar (htdocs/css) -->
    <link rel="stylesheet" href="http://localhost/css/style.css">

    <!-- BONUS STYLE -->
    <style>
        .btn {
            padding:8px 15px;
            background:#2ecc71;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }
        .btn-sm {
            padding:5px 10px;
            background:#e74c3c;
            color:white;
            border-radius:5px;
            text-decoration:none;
        }
        .input-control {
            padding:8px;
            width:200px;
            margin-right:5px;
        }

        /* ===== NAVBAR ===== */
        nav.navbar {
            background-color: #111;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            height: 60px;
        }
        nav.navbar h1 {
            margin: 0;
            font-size: 20px;
        }
        nav.navbar h1 a {
            color: white;
            text-decoration: none;
        }
        nav.navbar ul {
            list-style: none;
            display: flex;
            gap: 5px;
            margin: 0;
            padding: 0;
        }
        nav.navbar ul li a {
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 14px;
            transition: background 0.2s;
        }
        nav.navbar ul li a:hover,
        nav.navbar ul li a.active {
            background-color: #333;
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR (menggantikan <header> lama) ===== -->
<header>
    <div class="container">
        <h1><a href="dashboard.php">Aspirasi Sekolah</a></h1>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="data-aspirasi.php">Data Aspirasi</a></li>
            <li><a href="data-kategori.php">Kategori</a></li>
            <li><a href="data-siswa.php">Data Siswa</a></li>
            <li><a href="../keluar.php">Keluar</a></li>
        </ul>
    </div>
</header>

<div class="section">
    <div class="container">

        <h3>Data Siswa</h3>

        <!-- FORM TAMBAH -->
        <div class="box">
            <form method="POST">
                <input type="number" name="nis" class="input-control" placeholder="NIS Siswa" required>
                <input type="submit" name="tambah" class="btn" value="Tambah">
            </form>
        </div>

        <!-- TABEL -->
        <div class="box">
            <table class="table" border="1" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $no = 1;
                if (mysqli_num_rows($daftar) > 0) {
                    while ($row = mysqli_fetch_assoc($daftar)) {
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                    <td>
                        <a href="?hapus=<?php echo $row['nis']; ?>" 
                           class="btn-sm" 
                           onclick="return confirm('Hapus siswa ini?')">
                           Hapus
                        </a>
                    </td>
                </tr>
                <?php } } else { ?>
                <tr><td colspan="3">Belum ada siswa</td></tr>
                <?php } ?>
            </table>
        </div>

    </div>
</div>

</body>
</html>