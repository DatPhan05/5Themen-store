<?php
// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Thêm menu bên trái
require_once __DIR__ . "/class/category_content_class.php";

$ct = new CategoryContent();

// Lấy ID và kiểm tra hợp lệ
$id = (int)($_GET['id'] ?? 0); 
$data = null;

if ($id > 0) {
    // Giả định hàm getById trả về result set, sau đó fetch_assoc
    $result = $ct->getById($id);
    $data = $result ? $result->fetch_assoc() : null;
}

// Xử lý lỗi: Không tìm thấy nội dung
if (!$data) {
    die('<div style="text-align: center; margin-top: 50px; font-size: 18px; color: #dc3545;">
        ❌ Lỗi: Không tìm thấy Nội dung chi tiết có ID: ' . htmlspecialchars($id) . '
        <br><a href="category_content_list.php" style="color: #007bff; text-decoration: none;">Quay lại danh sách</a>
    </div>');
}

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lọc và chuẩn hóa dữ liệu
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // CKEditor đã xử lý nội dung

    if (!empty($title)) {
        if ($ct->update($id, $title, $content)) {
            $msg = "✔ Cập nhật thành công!";
            $msg_type = "success";
            
            // Lấy lại dữ liệu mới nhất sau khi cập nhật thành công
            $result = $ct->getById($id);
            $data = $result ? $result->fetch_assoc() : null;
        } else {
            $msg = "❌ Lỗi: Không thể cập nhật vào Database, vui lòng kiểm tra Class.";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Tiêu đề không được để trống.";
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
        max-width: 900px;
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

    /* ================= INPUT FIELDS & SELECT ================= */
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
    
    /* ================= CKEditor CUSTOM STYLES (Glassmorphism) ================= */
    /* Định dạng cho container của CKEditor */
    .cke_chrome {
        border-radius: 12px !important; 
        border: 1px solid rgba(255, 255, 255, 0.8) !important; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
        overflow: hidden; 
    }
    
    .cke_top {
        /* Màu nền toolbar */
        background: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.9) !important;
    }
    
    .cke_bottom {
        border-top: 1px solid #ccc !important; 
        border-radius: 0 0 12px 12px !important; 
    }

    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        /* Gradient xanh dương cho nút Update */
        background: linear-gradient(135deg, #0077b6, #00b4d8); 
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        margin-top: 25px; 
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
        top: 20%;
        left: 20%;
        transform: translate(-50%, -50%);
    }
</style>

<div class="admin-content-right">
    
    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-file-pen"></i> SỬA NỘI DUNG CHI TIẾT</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="form-group">
                <label class="form-label">Tiêu đề</label>
                <input 
                    type="text" 
                    name="title" 
                    class="form-control"
                    value="<?= htmlspecialchars($data['title'] ?? '') ?>" 
                    placeholder="Nhập tiêu đề nội dung"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung chi tiết</label>
                <textarea name="content" id="editor"><?= $data['content'] ?? '' ?></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Cập nhật
            </button>
            
        </form>
    </div>
</div>

</section>

<script src="../ckeditor/ckeditor.js"></script>
<script>
    // Khởi tạo CKEditor sau khi DOM đã load
    document.addEventListener('DOMContentLoaded', function() {
        CKEDITOR.replace("editor", {
            toolbar: 'Full', 
            height: 400, 
            // Cấu hình thêm: Đảm bảo class 'cke_chrome' là nơi áp dụng styling Glassmorphism 
            // Cần CSS bên trên để định kiểu cho .cke_chrome
        });
    });
</script>

</body>
</html>