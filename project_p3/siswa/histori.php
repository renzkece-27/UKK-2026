<?php
session_start();
include '../db.php';

// ================== CEK LOGIN ==================
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header('Location: ../login.php');
    exit;
}

$nis = $_SESSION['nis'];

// ================== AMBIL DATA (LEFT JOIN FIX + AMAN) ==================
$data = mysqli_query($conn, "
    SELECT 
        a.id_aspirasi, 
        a.status, 
        a.tgl_diajukan, 
        a.tgl_selesai, 
        a.feedback,
        i.lokasi, 
        i.ket,
        k.ket_kategori
    FROM aspirasi a
    LEFT JOIN input_aspirasi i 
        ON a.id_pelaporan = i.id_pelaporan
    LEFT JOIN kategori k 
        ON i.id_kategori = k.id_kategori
    WHERE i.nis = '$nis'
    ORDER BY a.id_aspirasi DESC
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
    <title>Histori Aspirasi | Siswa</title>

    <!-- ✅ AUTO FIX CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="http://localhost/css/style.css">

    <!-- BONUS STYLE -->
    <style>
        .card-aspirasi {
            background:white;
            padding:15px;
            border-radius:10px;
            margin-bottom:15px;
            box-shadow:0 2px 5px rgba(0,0,0,0.1);
        }
        .badge {
            padding:4px 10px;
            border-radius:5px;
            color:white;
            font-size:12px;
        }
        .badge-menunggu { background:#ffc107; }
        .badge-proses { background:#007bff; }
        .badge-selesai { background:#28a745; }

        .feedback {
            margin-top:10px;
            padding:10px;
            background:#f1f1f1;
            border-left:4px solid #28a745;
            border-radius:5px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1><a href="dashboard.php">Aspirasi Sekolah</a></h1>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="form-aspirasi.php">Buat Pengaduan</a></li>
            <li><a href="histori.php">Histori</a></li>
            <li><a href="../keluar.php">Keluar</a></li>
        </ul>
    </div>
</header>

<div class="section">
    <div class="container">
        <h3>Histori Aspirasi Saya</h3>

        <p>NIS: <strong><?php echo htmlspecialchars($nis); ?></strong></p>
        <br>

        <?php
        if (mysqli_num_rows($data) > 0) {
            while ($row = mysqli_fetch_assoc($data)) {

                // ================== BADGE ==================
                $badge = 'badge-menunggu';
                if ($row['status'] == 'Proses')  $badge = 'badge-proses';
                if ($row['status'] == 'Selesai') $badge = 'badge-selesai';

                // ================== PROGRESS ==================
                $status = strtolower(trim($row['status']));
                $persen = 20;
                $warna  = '#ffc107';

                if ($status == 'proses') {
                    $persen = 60;
                    $warna  = '#007bff';
                } elseif ($status == 'selesai') {
                    $persen = 100;
                    $warna  = '#28a745';
                }
        ?>

        <div class="card-aspirasi">
            <h4>
                #<?php echo $row['id_aspirasi']; ?> - 
                <?php echo htmlspecialchars($row['ket_kategori'] ?? '-'); ?>

                <span class="badge <?php echo $badge; ?>" style="margin-left:8px;">
                    <?php echo htmlspecialchars($row['status']); ?>
                </span>
            </h4>

            <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($row['lokasi'] ?? '-'); ?></p>
            <p><strong>Keterangan:</strong> <?php echo htmlspecialchars($row['ket'] ?? '-'); ?></p>

            <p style="color:#999; font-size:12px;">
                Diajukan: 
                <?php echo $row['tgl_diajukan'] ? date('d-m-Y H:i', strtotime($row['tgl_diajukan'])) : '-'; ?>
                <?php 
                if ($row['tgl_selesai']) {
                    echo ' | Selesai: ' . date('d-m-Y H:i', strtotime($row['tgl_selesai']));
                }
                ?>
            </p>

            <!-- PROGRESS BAR -->
            <div style="margin-top:8px;">
                <small style="color:#666;">Progres:</small>
                <div style="background:#eee; border-radius:10px; height:8px; margin-top:4px;">
                    <div style="width:<?php echo $persen; ?>%; background:<?php echo $warna; ?>; height:8px; border-radius:10px;"></div>
                </div>
                <small style="color:#999;">
                    <?php echo $persen; ?>% - <?php echo htmlspecialchars($row['status']); ?>
                </small>
            </div>

            <!-- FEEDBACK -->
            <?php if (!empty($row['feedback'])) { ?>
            <div class="feedback">
                <strong>Balasan Admin:</strong> 
                <?php echo htmlspecialchars($row['feedback']); ?>
            </div>
            <?php } ?>

        </div>

        <?php 
            } 
        } else { 
        ?>

        <div class="box">
            <p>Belum ada aspirasi. <a href="form-aspirasi.php">Buat sekarang</a></p>
        </div>

        <?php } ?>

    </div>
</div>

<footer>
    <div class="container">
        <small>Copyright &copy; 2026 - Aspirasi Sekolah.</small>
    </div>
</footer>

</body>
</html>