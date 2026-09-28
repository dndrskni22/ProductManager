<?php
session_start();
require_once '../config/db.php';

$errors = [];
$name = $category = $price = $stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normalisasi Input
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Donut');
    $price    = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock    = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // Validasi Server-Side
    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama menu minimal harus 3 karakter.';
    } else {
        // Cek Keunikan Nama
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name");
        $stmt->execute(['name' => $name]);
        if ($stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama menu sudah ada, gunakan nama unik lainnya.';
        }
    }

    if ($price === false || $price <= 0) {
        $errors['price'] = 'Harga harus angka positif dan lebih dari 0.';
    }

    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'Stok tidak boleh bernilai negatif.';
    }

    // Validasi & Upload Gambar (Bonus Feature)
    $imageName = 'default.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp   = $_FILES['image']['tmp_name'];
        $fileName  = $_FILES['image']['name'];
        $fileSize  = $_FILES['image']['size'];
        $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';
        } elseif ($fileSize > 2 * 1024 * 1024) { // Max 2MB
            $errors['image'] = 'Ukuran gambar maksimal adalah 2MB.';
        } else {
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($fileTmp, 'uploads/' . $imageName);
        }
    }

    // Jika Tidak Ada Error -> Eksekusi PRG (Post-Redirect-Get)
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image) VALUES (:name, :category, :price, :stock, :image)");
        $stmt->execute([
            'name'     => $name,
            'category' => $category,
            'price'    => $price,
            'stock'    => $stock,
            'image'    => $imageName
        ]);

        header("Location: index.php?status=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Menu Donut / Minuman</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <h2>Tambah Menu Baru</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="create.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nama Menu</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required minlength="3">
        </div>

        <div class="form-group">
            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="Donut" <?= $category === 'Donut' ? 'selected' : '' ?>>Donut</option>
                <option value="Minuman" <?= $category === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                <option value="Paket Combo" <?= $category === 'Paket Combo' ? 'selected' : '' ?>>Paket Combo</option>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" step="1" value="<?= htmlspecialchars($price) ?>" required min="0">
        </div>

        <div class="form-group">
            <label for="stock">Stok awal</label>
            <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($stock) ?>" required min="0">
        </div>

        <div class="form-group">
            <label for="image">Foto Menu (PNG/JPG/WEBP, Max 2MB)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit">Simpan Menu</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>