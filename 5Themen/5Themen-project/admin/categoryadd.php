<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Giả sử file này chứa Sidebar menu
require_once __DIR__ . "/Class/category_class.php";

$cg = new Category();
$msg = "";
$msg_type = ""; // Để phân biệt màu sắc thông báo (success/error)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    if ($name !== "") {
        $cg->insert_category($name, $parent_id);
        $msg = "✨ Đã thêm danh mục thành công!";
        $msg_type = "success";
    } else {
        $msg = "⚠️ Vui lòng nhập tên danh mục!";
        $msg_type = "error";
    }
}

$parents = $cg->get_parent_categories();
?>

<style>
    /* ================= LAYOUT CHÍNH ================= */
    /* Đảm bảo admin-content chứa cả sidebar và content-right */
    .admin-content {
        display: flex;
        min-height: 100vh;
        padding-top: 20px; /* Tránh header che */
    }

    /* Phần nội dung bên phải */
    .admin-content-right {
        flex: 1; /* Chiếm phần còn lại */
        padding: 40px;
        display: flex;
        justify-content: center; /* Căn giữa form */
        align-items: flex-start;
        position: relative;
    }

    /* ================= FORM CARD (GLASSMORPHISM) ================= */
    .form-container {
        width: 100%;
        max-width: 600px; /* Giới hạn chiều rộng cho đẹp */
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
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(75, 123, 236, 0.1);
        border-color: #4b7bec;
    }

    /* Style cho Select box */
    select.form-control {
        appearance: none; /* Xóa mũi tên mặc định */
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
        background: linear-gradient(135deg, #4b7bec, #3867d6);
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(75, 123, 236, 0.3);
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(75, 123, 236, 0.4);
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
        background: linear-gradient(180deg, #a18cd1 0%, #fbc2eb 100%);
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
        <h1 class="form-title"><i class="fa-solid fa-folder-plus"></i> Thêm Danh Mục</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="form-group">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="category_name" class="form-control" placeholder="Ví dụ: Điện thoại, Laptop..." required autocomplete="off">
            </div>

            <div class="form-group">
                <label class="form-label">Danh mục (Không bắt buộc)</label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Chọn danh mục --</option>
                    <?php if ($parents): ?>
                        <?php while ($p = $parents->fetch_assoc()): ?>
                            <option value="<?= $p['category_id'] ?>">
                                <?= $p['category_name'] ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Lưu danh mục
            </button>
            
        </form>
    </div>

</div>

</section> 
</body>
</html>