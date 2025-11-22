<?php
// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Thêm menu bên trái
require_once __DIR__ . "/Class/product_class.php";
require_once __DIR__ . "/Class/category_class.php";
require_once __DIR__ . "/Class/brand_class.php";

$id = (int)($_GET['id'] ?? 0);
$pd = new Product();
$row = $pd->get_product($id);

// Kiểm tra sản phẩm có tồn tại không
if (!$row) { 
    die("❌ Lỗi: Không tìm thấy sản phẩm."); 
}

$cg = new Category();
$bd = new Brand();
$cates = $cg->show_category();
$brands = $bd->show_brand();

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lọc và chuẩn hóa dữ liệu
    $name = trim($_POST['name'] ?? '');
    $cid  = (int)($_POST['category_id'] ?? 0);
    $bid  = (int)($_POST['brand_id'] ?? 0);
    // Loại bỏ dấu phẩy/chấm nếu người dùng nhập và chuyển về int
    $price = (int)str_replace(['.', ','], '', $_POST['price'] ?? 0);
    $sale = (int)str_replace(['.', ','], '', $_POST['sale_price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $thumb = trim($_POST['thumb'] ?? ''); // Đường dẫn ảnh hiện tại

    if ($name && $cid && $bid) {
        $thumbVal = $thumb !== "" ? $thumb : null;
        
        // Cập nhật sản phẩm
        $result = $pd->update_product($id, $name, $cid, $bid, $price, $sale, $desc, $thumbVal);
        
        if ($result) {
            $msg = "✔ Đã lưu thay đổi sản phẩm thành công.";
            $msg_type = "success";
            // Lấy lại dữ liệu mới nhất sau khi cập nhật
            $row = $pd->get_product($id);
        } else {
            $msg = "❌ Lỗi khi cập nhật sản phẩm. Vui lòng kiểm tra Class/Database.";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Vui lòng nhập đủ thông tin bắt buộc (*).";
        $msg_type = "error";
    }
}
?>

<style>
    /* ================= LAYOUT CHÍNH ================= */
    .admin-content-right {
        flex: 1; 
        padding: 40px;
        display: flex;
        justify-content: center; 
        align-items: flex-start;
        position: relative;
    }

    /* ================= FORM CARD (GLASSMORPHISM) ================= */
    .form-container {
        width: 100%;
        max-width: 800px;
        padding: 40px;
        border-radius: 20px;
        
        /* Hiệu ứng kính */
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* ================= INPUT FIELDS ================= */
    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        margin-left: 5px;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.5);
        font-family: "Poppins", sans-serif;
        font-size: 15px;
        color: #333;
        transition: all 0.3s ease;
        outline: none;
        box-sizing: border-box; 
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(16, 172, 132, 0.15); 
        border-color: #10ac84;
    }

    /* Style cho Select box */
    select.form-control {
        appearance: none; 
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        cursor: pointer;
    }
    
    /* Textarea */
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    /* Group cho giá và khuyến mãi */
    .price-group {
        display: flex;
        gap: 20px;
    }
    .price-group .form-group {
        flex: 1;
    }
    
    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        /* Gradient xanh đậm */
        background: linear-gradient(135deg, #0077b6, #00b4d8); /* Màu xanh dương cho nút Update */
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        margin-top: 20px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 119, 182, 0.4);
        filter: brightness(1.1);
    }

    /* ================= ALERT MESSAGE ================= */
    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .alert-success {
        background: rgba(32, 191, 107, 0.15);
        border: 1px solid rgba(32, 191, 107, 0.3);
        color: #20bf6b;
    }

    .alert-error {
        background: rgba(252, 92, 101, 0.15);
        border: 1px solid rgba(252, 92, 101, 0.3);
        color: #fc5c65;
    }

    /* Trang trí background nhẹ */
    .blob-decor {
        position: absolute;
        width: 300px;
        height: 300px;
        background: linear-gradient(180deg, #a1c4fd 0%, #c2e9fb 100%);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        z-index: -1;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>

<div class="admin-content-right">
    
    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-pen-to-square"></i> Sửa sản phẩm ID: <?= htmlspecialchars($id) ?></h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="form-group">
                <label class="form-label">Tên sản phẩm (*)</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['product_name']) ?>" placeholder="Nhập tên sản phẩm" required autocomplete="off">
            </div>

            <div class="price-group">
                <div class="form-group">
                    <label class="form-label">Danh mục (*)</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php if ($cates) : 
                            $cates->data_seek(0); // Reset con trỏ
                            while ($c = $cates->fetch_assoc()) : ?>
                                <option value="<?= $c['category_id'] ?>" <?= $row['category_id'] == $c['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['category_name']) ?>
                                </option>
                            <?php endwhile; 
                        endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Loại sản phẩm (*)</label>
                    <select name="brand_id" class="form-control" required>
                        <option value="">-- Chọn loại sản phẩm --</option>
                        <?php if ($brands) : 
                            $brands->data_seek(0); // Reset con trỏ
                            while ($b = $brands->fetch_assoc()) : ?>
                                <option value="<?= $b['brand_id'] ?>" <?= $row['brand_id'] == $b['brand_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['brand_name']) ?>
                                </option>
                            <?php endwhile; 
                        endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="price-group">
                <div class="form-group">
                    <label class="form-label">Giá (Ví dụ: 100000)</label>
                    <input type="text" name="price" class="form-control" value="<?= (int)$row['product_price'] ?>" placeholder="Giá gốc" pattern="[0-9]*" title="Vui lòng nhập số">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Giá khuyến mãi (0 nếu không có)</label>
                    <input type="text" name="sale_price" class="form-control" value="<?= (int)$row['product_sale'] ?>" placeholder="Giá khuyến mãi" pattern="[0-9]*" title="Vui lòng nhập số">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả chi tiết</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Nhập mô tả sản phẩm chi tiết..."><?= htmlspecialchars($row['product_desc'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh (Đường dẫn lưu DB: uploads/ten_file.jpg)</label>
                <input type="text" name="thumb" class="form-control" value="<?= htmlspecialchars($row['product_img'] ?? '') ?>" placeholder="Đường dẫn ảnh">
                
                <?php if (!empty($row['product_img'])): ?>
                    <div style="margin-top: 15px; text-align: center;">
                        <p style="font-size: 14px; color: #555; margin-bottom: 5px;">Ảnh hiện tại:</p>
                        



                        <img src="<?= htmlspecialchars("../" . $row['product_img']) ?>" alt="Ảnh sản phẩm" style="max-width: 150px; height: auto; border-radius: 8px; border: 1px solid #ccc;">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
            </button>
            
        </form>
    </div>
</div>

</section>
</body>
</html>