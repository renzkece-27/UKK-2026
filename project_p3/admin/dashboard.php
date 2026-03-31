<?php
session_start();
include '../db.php';

// ================== CEK LOGIN ==================
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// ================== HITUNG DATA (OPTIMASI 1 QUERY) ==================
$count = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) as semua,
        SUM(status='Menunggu') as tunggu,
        SUM(status='Proses') as proses,
        SUM(status='Selesai') as selesai
    FROM aspirasi
"));

$semua   = $count['semua'] ?? 0;
$tunggu  = $count['tunggu'] ?? 0;
$proses  = $count['proses'] ?? 0;
$selesai = $count['selesai'] ?? 0;

// ================== AMBIL DATA (LEFT JOIN FIX) ==================
$data = mysqli_query($conn, "
    SELECT 
        a.id_aspirasi, 
        a.status, 
        a.tgl_diajukan,
        i.nis, 
        i.lokasi,
        k.ket_kategori
    FROM aspirasi a
    LEFT JOIN input_aspirasi i 
        ON a.id_pelaporan = i.id_pelaporan
    LEFT JOIN kategori k 
        ON i.id_kategori = k.id_kategori
    ORDER BY a.id_aspirasi DESC
    LIMIT 10
");

// CEK ERROR QUERY
if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Aspirasi Sekolah</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .col-stat {
            display:inline-block;
            width:22%;
            padding:15px;
            margin:16px;
            border-radius:100px;
            text-align:center;
            color:white;
        }
        .col-stat:nth-child(1){ background:#3498db; }
        .col-stat:nth-child(2){ background:#f39c12; }
        .col-stat:nth-child(3){ background:#9b59b6; }
        .col-stat:nth-child(4){ background:#2ecc71; }

        .badge {
            padding:5px 10px;
            border-radius:5px;
            color:white;
        }
        .badge-menunggu { background color: #eb2323;; }
        .badge-proses { background color: #eeff00;
        .badge-selesai { background color: #00ff22; }

        .btn-sm {
            padding:5px 10px;
            background:#3498db;
            color:white;
            border-radius:5px;
            text-decoration:none;
        }
    </style>
</head>
<body>

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
        <h3>Dashboard Admin</h3>

        <div class="box">
            <h4>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
        </div>

        <!-- Statistik -->
        <div style="margin-top:10px;">
            <div class="col-stat">
                <p>Total</p>
                <h2><?php echo $semua; ?></h2>
            </div>
            <div class="col-stat">
                <p>Menunggu</p>
                <h2><?php echo $tunggu; ?></h2>
            </div>
            <div class="col-stat">
                <p>Proses</p>
                <h2><?php echo $proses; ?></h2>
            </div>
            <div class="col-stat">
                <p>Selesai</p>
                <h2><?php echo $selesai; ?></h2>
            </div>
        </div>

        <!-- Tabel -->
        <div class="box">
            <table class="table" border="1" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>NIS</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($data) > 0) {
                        while ($row = mysqli_fetch_assoc($data)) {

                            $badge = 'badge-menunggu';
                            if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                            if ($row['status'] == 'Selesai') $badge = 'badge-selesai';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php 
                            echo $row['tgl_diajukan'] 
                            ? date('d-m-Y H:i', strtotime($row['tgl_diajukan'])) 
                            : '-'; 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['ket_kategori'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['lokasi'] ?? '-'); ?></td>
                        <td>
                            <span class="badge <?php echo $badge; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="detail-aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>" class="btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else { 
                    ?>
                    <tr><td colspan="7">Belum ada aspirasi</td></tr>
                    <?php } ?>
                </tbody>
            </table>

            <p style="margin-top:10px;">
                <a href="data-aspirasi.php">Lihat semua &rarr;</a>
            </p>
        </div>

    </div>
</div>

<footer>
    <div class="container">
        <small>Copyright &copy; 2026 - Aspirasi Sekolah.</small>
    </div>
</footer>

</body>
</html>