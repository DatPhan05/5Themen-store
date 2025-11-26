<?php
// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Thêm menu bên trái
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$id = (int)($_GET['brand_id'] ?? 0);

$brand = new Brand();
$row  = $brand->get_brand($id);

// Xử lý khi không tìm thấy loại sản phẩm
if (!$row) {
    die('<div style="text-align: center; margin-top: 50px; font-size: 18px; color: #dc3545;">
        ❌ Lỗi: Không tìm thấy loại sản phẩm ID: ' . htmlspecialchars($id) . '
        <br><a href="brandlist.php" style="color: #007bff; text-decoration: none;">Quay lại danh sách</a>
    </div>');
}

$cg = new category();
$cates = $cg->show_category();

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');

    if ($cid && $name !== "") {
        $result = $brand->update_brand($id, $cid, $name);
        
        if ($result) {
            $msg = "✔ Đã lưu thay đổi loại sản phẩm thành công.";
            $msg_type = "success";
            // Lấy lại dữ liệu mới nhất
            $row = $brand->get_brand($id);
        } else {
            $msg = "❌ Lỗi: Không thể cập nhật loại sản phẩm. Vui lòng kiểm tra Class/Database.";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Vui lòng chọn danh mục và nhập tên loại.";
        $msg_type = "error";
    }
}
?>

<style>
    /* ================= LAYOUT CHÍNH ================= */
    .admin-content-right {
        margin-left: 230px;
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
        max-width: 500px;
        padding: 40px;
        border-radius: 20px;
        
        /* Hiệu ứng kính */
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .form-title {
        font-size: 26px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* ================= INPUT FIELDS & SELECT ================= */
    .form-group {
        margin-bottom: 25px;
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
        font-size: 16px;
        color: #333;
        transition: all 0.3s ease;
        outline: none;
        box-sizing: border-box; 
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(78, 205, 196, 0.15); /* Màu xanh ngọc cho focus */
        border-color: #4ecdc4;
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

    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        /* Gradient màu teal/xanh ngọc cho nút Sửa Brand */
        background: linear-gradient(135deg, #4ecdc4, #1abc9c); 
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78, 205, 196, 0.4);
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
        background: linear-gradient(180deg, #b2fefd 0%, #a6c0fe 100%);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        z-index: -1;
        top: 20%;
        right: 15%;
        transform: translate(50%, -50%);
    }
</style>

<div class="admin-content-right">
    
    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-list-ul"></i> SỬA LOẠI SẢN PHẨM</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="form-group">
                <label class="form-label" for="category_id">Chọn danh mục (*)</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php if ($cates) : 
                        $cates->data_seek(0); // Đảm bảo con trỏ ở đầu
                        while ($c = $cates->fetch_assoc()) : ?>
                            <option 
                                value="<?= $c['category_id'] ?>" 
                                <?= ($row['category_id'] == $c['category_id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($c['category_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="brand_name">Tên loại (*)</label>
                <input 
                    type="text" 
                    name="brand_name" 
                    id="brand_name" 
                    class="form-control"
                    value="<?= htmlspecialchars($row['brand_name']) ?>" 
                    placeholder="Nhập tên loại sản phẩm mới"
                    required
                >
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