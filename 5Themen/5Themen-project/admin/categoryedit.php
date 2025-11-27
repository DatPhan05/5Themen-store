<?php
include "../include/session.php";
include "../include/database.php";
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/category_class.php";

$cg  = new Category();
$id  = (int)($_GET['category_id'] ?? 0);
$row = $id ? $cg->get_category($id) : null;

if (!$row) {
    die('<div style="text-align: center; margin-top: 100px; font-size: 18px; color: #dc3545;">
            ❌ Lỗi: Không tìm thấy danh mục ID: ' . htmlspecialchars($id) . '
            <br><a href="categorylist.php" style="color: #007bff; text-decoration: none; margin-top:10px; display:inline-block;">
            ⬅ Quay lại danh sách
            </a>
        </div>');
}

$msg      = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name'] ?? '');

    if ($name !== "") {
        $result = $cg->update_category($id, $name, null);
        if ($result) {
            $msg      = "✔ Đã lưu thay đổi danh mục thành công.";
            $msg_type = "success";
            $row      = $cg->get_category($id);
        } else {
            $msg      = "❌ Lỗi: Không thể cập nhật danh mục. Vui lòng thử lại.";
            $msg_type = "error";
        }
    } else {
        $msg      = "⚠️ Vui lòng nhập tên danh mục.";
        $msg_type = "error";
    }
}
?>

<style>
    .admin-content-right {
        margin-left: 230px;
        flex: 1;
        padding: 40px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
    }
    .form-container {
        width: 100%;
        max-width: 500px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .form-title {
        font-size: 26px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 30px;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .form-group { margin-bottom: 25px; }
    .form-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
        color: #555;
    }
    .form-control {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.5);
        font-size: 16px;
        color: #333;
        outline: none;
        transition: 0.3s ease;
    }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        border-color: #ff7f50;
        box-shadow: 0 0 0 4px rgba(255, 127, 80, 0.15);
    }
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #ff7f50, #ff6b81);
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 127, 80, 0.3);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 127, 80, 0.4);
        filter: brightness(1.1);
    }
    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
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
    .blob-decor {
        position: absolute;
        width: 300px;
        height: 300px;
        background: linear-gradient(180deg, #ffafc0, #ffcba4);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        z-index: -1;
        top: 50%;
        right: 10%;
        transform: translate(50%, -50%);
    }
</style>

<div class="admin-content-right">
    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-tags"></i> Sửa Danh Mục</h1>

        <?php if (!empty($msg)): ?>
            <div class="alert <?= $msg_type === 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= $msg_type === 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label class="form-label">Tên danh mục</label>
                <input 
                    type="text"
                    name="category_name"
                    class="form-control"
                    value="<?= htmlspecialchars($row['category_name']) ?>"
                    placeholder="Nhập tên danh mục mới"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
            </button>
        </form>
    </div>
</div>

</body>
</html>
