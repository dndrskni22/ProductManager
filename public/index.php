<?php
session_start();
require_once '../config/db.php';

// Generate Token CSRF jika belum ada
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

// Feature Search / Filter (Bonus)
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q OR category LIKE :q ORDER BY id DESC");
    $stmt->execute(['q' => "%$q%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SweetGlaze Donuts & Brews</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1 class="brand-title">🍩 SweetGlaze & Brews ☕</h1>
    <p class="brand-subtitle">Artisanal Donuts • Specialty Coffee • Sweet Moments</p>
</header>

<div class="container">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'created'): ?>
            <div class="alert alert-success">Produk berhasil ditambahkan!</div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success">Produk berhasil diperbarui!</div>
        <?php elseif ($_GET['status'] === 'deleted'): ?>
            <div class="alert alert-success">Produk berhasil dihapus!</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="top-bar">
        <a href="create.php" class="btn">+ Tambah Menu Baru</a>
        
        <!-- Form Search (GET Method) -->
        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="q" placeholder="Cari donut/minuman..." value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Cari</button>
            <?php if($q !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="products-grid">
        <?php if (empty($products)): ?>
            <p>Tidak ada menu yang ditemukan.</p>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <div class="card">
                    <?php 
                        $imgPath = 'uploads/' . $p['image'];
                        $imgSrc = (file_exists($imgPath) && !empty($p['image'])) ? $imgPath : 'https://via.placeholder.com/300x200?text=Donut+%26+Drink';
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="card-body">
                        <span class="badge"><?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                        <!-- Keamanan Output (XSS Prevention) -->
                        <h3 class="card-title"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <div class="stock">Stok Tersedia: <?= (int)$p['stock'] ?> pcs</div>
                    </div>
                    <div class="card-actions">
                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="flex:1; text-align:center;">Edit</a>
                        
                        <!-- Form Delete dengan POST + CSRF Protection -->
                        <form method="POST" action="delete.php" style="flex:1;" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                            <button type="submit" class="btn btn-danger" style="width:100%;">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>