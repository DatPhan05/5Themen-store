<?php
// Tên file: admin/postadd.php

// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Menu bên trái
require_once __DIR__ . "/Class/post_class.php";

$postModel = new Post();
$msg = "";
$msg_type = "";

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $slug_raw = trim($_POST['slug'] ?? '');
    $slug     = !empty($slug_raw) ? slugify($slug_raw) : slugify($title); 
    
    $summary  = trim($_POST['summary'] ?? '');
    $content  = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? 'Tin tức';

    $thumbnail = null;
    $uploadError = false;

    if (isset($_FILES['thumbnail']) && !empty($_FILES['thumbnail']['name'])) {
        $uploadDir = "../uploads/"; 
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileMimeType = $_FILES['thumbnail']['type'];

        if (in_array($fileMimeType, $allowedTypes)) {
            $fileName   = time() . "_" . basename($_FILES['thumbnail']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
                $thumbnail = 'uploads/' . $fileName; 
            } else {
                $uploadError = true;
                $msg = "❌ Lỗi: Không thể di chuyển file ảnh. Kiểm tra quyền ghi thư mục.";
                $msg_type = "error";
            }
        } else {
            $uploadError = true;
            $msg = "❌ Lỗi: Định dạng file ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, GIF).";
            $msg_type = "error";
        }
    }

    if (!$uploadError && $title !== "" && $content !== "") {
        $result = $postModel->insert_post($title, $slug, $thumbnail, $summary, $content, $category);
        if ($result) {
            $msg = "✨ Đã thêm bài viết mới thành công! <a href='posts.php' style='color: #fff;'>Xem danh sách</a>";
            $msg_type = "success";
        } else {
            $msg = "❌ Lỗi: Không thể thêm bài viết vào Database. Vui lòng kiểm tra lại Class.";
            $msg_type = "error";
        }
    } elseif (!$uploadError) {
        $msg = "⚠️ Vui lòng nhập đầy đủ tiêu đề và nội dung.";
        $msg_type = "warning";
    }
}
?>

<style>
    /* ================= LAYOUT CHÍNH (ĐÃ CHỈNH SỬA) ================= */
    .admin-content-right {
        flex: 1; 
        padding: 40px;
        position: relative;
        /* CĂN GIỮA NỘI DUNG */
        display: flex;
        justify-content: center; /* Căn giữa ngang */
align-items: flex-start; /* Giữ form ở trên (Nếu muốn căn giữa dọc, dùng center) */
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* ================= FORM CARD (GLASSMORPHISM) ================= */
    .admin-form {
        width: 100%;
        max-width: 1000px;
        padding: 40px;
        border-radius: 20px;
        
        /* Hiệu ứng kính */
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
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

    .form-input {
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

    .form-input:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(230, 126, 34, 0.15); 
        border-color: #e67e22;
    }
    
    textarea.form-input {
        min-height: 100px;
        resize: vertical;
    }
    
    select.form-input {
        appearance: none; 
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        cursor: pointer;
    }
    
    /* ================= CKEditor CUSTOM STYLES ================= */
    .cke_chrome {
        border-radius: 12px !important; 
        border: 1px solid rgba(255, 255, 255, 0.8) !important; 
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
        overflow: hidden; 
    }
    
    .cke_top {
        background: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.9) !important;
    }

    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #f39c12, #e67e22); 
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
        margin-top: 15px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(230, 126, 34, 0.4);
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
    
    .alert-warning {
        background: rgba(255, 165, 0, 0.15);
        border: 1px solid rgba(255, 165, 0, 0.3);
        color: #ffa500;
    }

    /* Trang trí background nhẹ */
    .blob-decor {
        position: absolute;
        width: 300px;
        height: 300px;
        background: linear-gradient(180deg, #f39c12 0%, #ffcba4 100%);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.3;
        z-index: -1;
        top: 80%;
        right: 10%;
        transform: translate(50%, -50%);
    }
</style>

<div class="admin-content-right">
    <div class="blob-decor"></div>

    <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
        
        <h2 class="form-title"><i class="fa-solid fa-file-circle-plus"></i> THÊM BÀI VIẾT MỚI</h2>

        <?php if ($msg): ?>
            <div class="alert <?= 'alert-' . $msg_type; ?>">
                <i class="fa-solid <?= ($msg_type == 'success') ? 'fa-circle-check' : (($msg_type == 'warning') ? 'fa-triangle-exclamation' : 'fa-circle-xmark'); ?>"></i>
                <?= $msg; ?>
            </div>
        <?php endif; ?>


        <div class="form-group">
            <label class="form-label">Tiêu đề bài viết (*)</label>
            <input type="text" name="title" class="form-input" required 
                   placeholder="Nhập tiêu đề bài viết"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Slug (URL – có thể bỏ trống, sẽ tự tạo)</label>
            <input type="text" name="slug" class="form-input" 
                   placeholder="vd: top-10-shop-quan-au-nam"
                   value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Chuyên mục</label>
            <select name="category" class="form-input">
                <?php 
                    $selected_category = $_POST['category'] ?? 'Tin tức';
$options = ["Tin tức", "Tin thời trang", "Kinh nghiệm hay"];
                    foreach ($options as $option):
                ?>
                    <option value="<?= htmlspecialchars($option) ?>" 
                            <?= ($selected_category == $option) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($option) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Ảnh thumbnail (tùy chọn)</label>
            <input type="file" name="thumbnail" class="form-input" accept="image/*">
        </div>

        <div class="form-group">
            <label class="form-label">Tóm tắt ngắn</label>
            <textarea name="summary" rows="3" class="form-input" 
                      placeholder="Tóm tắt nội dung chính, hiển thị ở trang chủ/danh sách."><?= htmlspecialchars($_POST['summary'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Nội dung chi tiết (*)</label>
            <textarea name="content" id="editor" class="form-input"><?= $_POST['content'] ?? '' ?></textarea>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> Lưu bài viết
        </button>

    </form>
</div>
</section>

<script src="../ckeditor/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        CKEDITOR.replace("editor", {
            toolbar: 'Full', 
            height: 500, 
        });
    });
</script>
</body>
</html>
