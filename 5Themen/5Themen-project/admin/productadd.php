<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/product_class.php";
require_once __DIR__ . "/Class/category_class.php";
require_once __DIR__ . "/Class/brand_class.php";

$cg = new Category();
$bd = new Brand();
$cates  = $cg->show_category();
$brands = $bd->show_brand();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['product_name'] ?? '');
    $cid   = (int)($_POST['category_id'] ?? 0);
    $bid   = (int)($_POST['brand_id'] ?? 0);
    $price = (int)($_POST['product_price'] ?? 0);
    $sale  = (int)($_POST['product_sale'] ?? 0);
    $desc  = trim($_POST['product_desc'] ?? '');

    // File ảnh gốc
    $thumb = $_FILES['product_img']['name'] ?? '';

    if ($name && $cid && $bid && $thumb) {

        /* ===============================
           1. Tạo thư mục upload nếu chưa có
        =============================== */
        $upload_dir = __DIR__ . "/uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        /* ===============================
           2. Chuyển tên file về dạng an toàn
        =============================== */

        // Bỏ dấu tiếng Việt
        $safeName = iconv('UTF-8','ASCII//TRANSLIT//IGNORE', $thumb);

        // Chuyển về chữ thường
        $safeName = strtolower($safeName);

        // Thay khoảng trắng và ký tự lạ thành dấu "-"
        $safeName = preg_replace('/[^a-z0-9\.]+/', '-', $safeName);

        // Loại bỏ nhiều dấu "---" thành "-"
        $safeName = preg_replace('/-+/', '-', $safeName);

        // Lấy phần mở rộng
        $ext = pathinfo($thumb, PATHINFO_EXTENSION);

        // Nếu file trùng tên → thêm số phía sau
        $fileBase = pathinfo($safeName, PATHINFO_FILENAME);
        $newFileName = $safeName;
        $i = 1;

        while (file_exists($upload_dir . $newFileName)) {
            $newFileName = $fileBase . "-" . $i . "." . $ext;
            $i++;
        }

        /* ===============================
           3. Đường dẫn thực & đường dẫn lưu DB
        =============================== */
        $real_path = $upload_dir . $newFileName;
        $save_path = "admin/uploads/" . $newFileName;

        /* ===============================
           4. Upload file
        =============================== */
        move_uploaded_file($_FILES['product_img']['tmp_name'], $real_path);

        /* ===============================
           5. Lưu database
        =============================== */
        (new Product())->insert_product($name, $cid, $bid, $price, $sale, $desc, $save_path);

        $msg = "Đã thêm sản phẩm thành công!";
    } else {
        $msg = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-product_add">
        <h1>Thêm sản phẩm</h1>

        <?php if ($msg): ?>
            <p style="color:red; font-weight:bold;"><?= $msg ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">

            <label>Tên sản phẩm:</label><br>
            <input type="text" name="product_name" placeholder="Nhập tên sản phẩm"><br>

            <label>Danh mục:</label><br>
            <select name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php while ($c = $cates->fetch_assoc()): ?>
                    <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endwhile; ?>
            </select><br>

            <label>Loại sản phẩm:</label><br>
            <select name="brand_id" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <?php while ($b = $brands->fetch_assoc()): ?>
                    <option value="<?= $b['brand_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
                <?php endwhile; ?>
            </select><br>

            <label>Giá:</label><br>
            <input type="text" name="product_price"><br>

            <label>Khuyến mãi:</label><br>
            <input type="text" name="product_sale"><br>

            <label>Mô tả:</label><br>
            <textarea name="product_desc" rows="5"></textarea><br>

            <label>Ảnh sản phẩm:</label><br>
            <input type="file" name="product_img" accept="image/*" required><br><br>

            <button type="submit">Thêm</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
