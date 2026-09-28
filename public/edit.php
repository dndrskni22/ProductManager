<?php
session_start();
require_once '../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Read One Data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name     = $product['name'];
$category = $product['category'];
$price    = $product['price'];
$stock    = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Donut');
    $price    = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock    = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama menu minimal harus 3 karakter.';
    } else {
        // Cek Keunikan nama selain milik ID ini sendiri
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name AND id != :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama menu sudah digunakan oleh produk lain.';
        }
    }

    if ($price === false || $price <= 0) {
        $errors['price'] = 'Harga harus bernilai positif.';
    }

    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'Stok tidak boleh bernilai negatif.';
    }

    $imageName = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Format gambar tidak sesuai.';
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $errors['image'] = 'Ukuran gambar maksimal 2MB.';
        } else {
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($fileTmp, 'uploads/' . $imageName);
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock = :stock, image = :image WHERE id = :id");
        $stmt->execute([
            'name'     => $name,
            'category' => $category,
            'price'    => $price,
            'stock'    => $stock,
            'image'    => $imageName,
            'id'       => $id
        ]);

        header("Location: index.php?status=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Donut/Minuman</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container" style="max-width: 600px;">
    <h2>Edit Menu #<?= (int)$product['id'] ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data">
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
            <label for="stock">Stok</label>
            <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($stock) ?>" required min="0">
        </div>

        <div class="form-group">
            <label for="image">Ganti Foto Menu (Opsional)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit">Update Menu</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>