<?php
// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Thêm menu bên trái
require_once __DIR__ . "/class/category_content_class.php";
require_once __DIR__ . "/class/category_class.php";

$cg = new Category();
$ct = new CategoryContent();

// Lấy danh sách danh mục
$categories = $cg->show_category();

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lọc và chuẩn hóa dữ liệu
    $catId = (int)($_POST['category_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // CKEditor đã xử lý nội dung

    // Kiểm tra dữ liệu bắt buộc
    if ($catId > 0 && !empty($title)) {
        if ($ct->insert($catId, $title, $content)) {
            $msg = "✔ Thêm nội dung thành công!";
            $msg_type = "success";
        } else {
            $msg = "❌ Lỗi khi thêm nội dung vào Database, vui lòng kiểm tra kết nối/Class.";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Vui lòng chọn Danh mục và nhập Tiêu đề.";
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

    .form-control, select.form-control {
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

    .form-control:focus, select.form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(16, 172, 132, 0.15); 
        border-color: #10ac84;
    }

    select.form-control {
        appearance: none; 
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        cursor: pointer;
    }
    
    /* ================= CKEditor CUSTOM STYLES (SỬA LỖI) ================= */
    /* Định dạng cho container của CKEditor */
    .cke_chrome {
        border-radius: 12px !important; /* Áp dụng bo góc cho toàn bộ CKEditor */
        border: 1px solid rgba(255, 255, 255, 0.8) !important; /* Thêm border nhẹ */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* Thêm box shadow nhẹ */
        overflow: hidden; /* Quan trọng để bo góc hoạt động tốt */
    }
    
    /* Làm cho thanh công cụ Glassmorphism hơn (nếu có thể) */
    .cke_top {
        /* Màu nền toolbar có thể làm trong suốt một chút nếu muốn Glassmorphism */
        background: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.9) !important;
    }
    
    /* Loại bỏ border không cần thiết */
    .cke_bottom {
        border-top: 1px solid #ccc !important; 
        border-radius: 0 0 12px 12px !important; /* Bo góc dưới */
    }

    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        /* Gradient xanh đậm */
        background: linear-gradient(135deg, #10ac84, #00d2d3); 
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 172, 132, 0.3);
        margin-top: 25px; 
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 172, 132, 0.4);
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
        <h1 class="form-title"><i class="fa-solid fa-file-circle-plus"></i> Thêm nội dung chi tiết cho Danh mục</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="form-group">
                <label class="form-label">Chọn danh mục (*)</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php if ($categories && $categories->num_rows > 0): 
                        $categories->data_seek(0);
                        while ($r = $categories->fetch_assoc()): ?>
                            <option value="<?= $r['category_id'] ?>">
                                <?= htmlspecialchars($r['category_name']) ?>
                            </option>
                        <?php endwhile; 
                    endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tiêu đề (*)</label>
                <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề nội dung (Ví dụ: Giới thiệu về Áo Nam)" required autocomplete="off">
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung chi tiết</label>
                <textarea name="content" id="editor" rows="10" placeholder="Nhập nội dung chi tiết..."></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-upload"></i> Thêm nội dung
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
            height: 350, 
            // Cấu hình thêm: Đảm bảo class 'cke_chrome' là nơi áp dụng styling Glassmorphism 
            // Cần CSS bên trên để định kiểu cho .cke_chrome
        });
    });
</script>

</body>
</html>