<?php
// Bổ sung các file cần thiết (Giả định nằm ngoài thư mục admin 1 cấp)
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/product_class.php";
require_once __DIR__ . "/class/category_class.php";
require_once __DIR__ . "/class/brand_class.php";

$cg = new Category();
$bd = new Brand();

// Lấy danh sách để điền vào Select Box
$cates = $cg->show_category();
$brands = $bd->show_brand();

$msg = "";
$msg_type = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lọc và chuyển đổi dữ liệu POST
    $name = trim($_POST['product_name'] ?? '');
    $cid = (int)($_POST['category_id'] ?? 0);
    $bid = (int)($_POST['brand_id'] ?? 0);
    $price = (int)str_replace(['.', ','], '', $_POST['product_price'] ?? 0);
    $sale = (int)str_replace(['.', ','], '', $_POST['product_sale'] ?? 0);
    $desc = trim($_POST['product_desc'] ?? '');

    // File ảnh gốc
    $thumb = $_FILES['product_img']['name'] ?? '';

    // Kiểm tra các trường bắt buộc
    if ($name && $cid && $bid && $price >= 0 && !empty($thumb)) {

        /* ===============================
            1. Thiết lập thư mục upload
            - Thư mục THẬT:  /admin/uploads/
            - Đường dẫn lưu DB: admin/uploads/ten-file.jpg
        =============================== */
        // Thư mục thật trên ổ đĩa
        $upload_dir_full = __DIR__ . "/uploads/"; // admin/uploads/

        if (!is_dir($upload_dir_full)) {
            mkdir($upload_dir_full, 0777, true); 
        }

        /* ===============================
            2. Xử lý tên file an toàn
        =============================== */
        $ext      = pathinfo($thumb, PATHINFO_EXTENSION);
        $fileBase = pathinfo($thumb, PATHINFO_FILENAME); 

        $safeBaseName = iconv('UTF-8','ASCII//TRANSLIT//IGNORE', $fileBase);
        $safeBaseName = strtolower($safeBaseName);
        $safeBaseName = preg_replace('/[^a-z0-9]+/', '-', $safeBaseName); 
        $safeBaseName = preg_replace('/-+/', '-', $safeBaseName); 
        $safeBaseName = trim($safeBaseName, '-');

        $newFileName = $safeBaseName . "." . $ext;

        // Xử lý trùng tên
        $i = 1;
        $tempFileName = $newFileName;
        while (file_exists($upload_dir_full . $tempFileName)) {
            $tempFileName = $safeBaseName . "-" . $i . "." . $ext;
            $i++;
        }
        $newFileName = $tempFileName;

        /* ===============================
            3. Đường dẫn thực & đường dẫn lưu DB
        =============================== */
        $real_path = $upload_dir_full . $newFileName;           // D:\...\admin\uploads\ao-thun-1.jpg
        $save_path = "admin/uploads/" . $newFileName;           // LƯU VÀO DB: admin/uploads/ao-thun-1.jpg

        /* ===============================
            4. Upload file
        =============================== */
        if (move_uploaded_file($_FILES['product_img']['tmp_name'], $real_path)) {

            /* ===============================
                5. Lưu database
            =============================== */
            (new Product())->insert_product($name, $cid, $bid, $price, $sale, $desc, $save_path);

            $msg      = "✨ Đã thêm sản phẩm thành công!";
            $msg_type = "success";

        } else {
            $msg      = "❌ Lỗi khi upload file! Vui lòng kiểm tra quyền ghi thư mục 'admin/uploads'.";
            $msg_type = "error";
        }

    } else {
        $msg      = "⚠️ Vui lòng điền đầy đủ Tên sản phẩm, Danh mục, Loại, Giá và chọn Ảnh!";
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
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        animation: slideIn 0.5s ease-out;
    }

    /* (Các style khác giữ nguyên) */
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

    .form-group { margin-bottom: 25px; position: relative; }
    .form-label {
        display: block; font-size: 14px; font-weight: 600; color: #555;
        margin-bottom: 8px; margin-left: 5px;
    }
    .form-control {
        width: 100%; padding: 14px 18px; border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.8); background: rgba(255, 255, 255, 0.5);
        font-family: "Poppins", sans-serif; font-size: 15px; color: #333;
        transition: all 0.3s ease; outline: none; box-sizing: border-box;
    }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 0 0 4px rgba(16, 172, 132, 0.15); 
        border-color: #10ac84;
    }
    select.form-control {
        appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 15px center; background-size: 16px; cursor: pointer;
    }
    textarea.form-control { min-height: 120px; resize: vertical; }
    .price-group { display: flex; gap: 20px; }
    .price-group .form-group { flex: 1; }

    /* ================= CUSTOM FILE INPUT STYLE (QUAN TRỌNG) ================= */
    /* Ẩn input file mặc định */
    .file-input-hidden {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    /* Style cho label (nút Chọn tệp) */
    .file-input-label {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.5);
        font-family: "Poppins", sans-serif;
        font-size: 15px;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-input-label:hover {
        background: rgba(255, 255, 255, 0.7);
        box-shadow: 0 0 0 2px rgba(16, 172, 132, 0.1);
    }
    
    /* Style cho nút "Chọn Tệp" nhỏ bên trong label */
    .file-select-button {
        background-color: #10ac84;
        color: white;
        padding: 5px 15px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        margin-right: 15px;
        white-space: nowrap;
        box-shadow: 0 2px 5px rgba(16, 172, 132, 0.3);
    }
    
    /* Style cho văn bản hiển thị tên file */
    .file-name-display {
        color: #777; /* Màu xám cho tên file chưa chọn */
        font-style: italic;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ================= BUTTON ================= */
    .btn-submit {
        width: 100%; padding: 15px; border: none; border-radius: 12px;
        background: linear-gradient(135deg, #10ac84, #00d2d3); 
        color: white; font-size: 16px; font-weight: 600; cursor: pointer;
        transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 172, 132, 0.3);
        margin-top: 10px;
    }
    .btn-submit:hover {
        transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 172, 132, 0.4); filter: brightness(1.1);
    }
    
    /* (Các style alert và blob-decor giữ nguyên) */
    .alert { padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: rgba(32, 191, 107, 0.15); border: 1px solid rgba(32, 191, 107, 0.3); color: #20bf6b; }
    .alert-error { background: rgba(252, 92, 101, 0.15); border: 1px solid rgba(252, 92, 101, 0.3); color: #fc5c65; }
    .blob-decor { position: absolute; width: 300px; height: 300px; background: linear-gradient(180deg, #a1c4fd 0%, #c2e9fb 100%); border-radius: 50%; filter: blur(80px); opacity: 0.4; z-index: -1; top: 50%; left: 50%; transform: translate(-50%, -50%); }
</style>

<div class="admin-content-right">
    
    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-square-plus"></i> Thêm sản phẩm mới</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= ($msg_type == 'success') ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= ($msg_type == 'success') ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label class="form-label">Tên sản phẩm (*)</label>
                <input type="text" name="product_name" class="form-control" placeholder="Nhập tên sản phẩm" required autocomplete="off">
            </div>

            <div class="price-group">
                <div class="form-group">
                    <label class="form-label">Danh mục (*)</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php if ($cates && $cates->num_rows > 0): 
                            $cates->data_seek(0);
                            while ($c = $cates->fetch_assoc()): ?>
                                <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                            <?php endwhile; 
                        endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Loại sản phẩm (*)</label>
                    <select name="brand_id" class="form-control" required>
                        <option value="">-- Chọn loại sản phẩm --</option>
                        <?php if ($brands && $brands->num_rows > 0): 
                            $brands->data_seek(0);
                            while ($b = $brands->fetch_assoc()): ?>
                                <option value="<?= $b['brand_id'] ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
                            <?php endwhile; 
                        endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="price-group">
                <div class="form-group">
                    <label class="form-label">Giá (Ví dụ: 100000) (*)</label>
                    <input type="text" name="product_price" class="form-control" placeholder="Giá gốc" required pattern="[0-9]*" title="Vui lòng nhập số">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Khuyến mãi (0 nếu không có)</label>
                    <input type="text" name="product_sale" class="form-control" placeholder="Giá khuyến mãi" pattern="[0-9]*" title="Vui lòng nhập số">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả chi tiết</label>
                <textarea name="product_desc" class="form-control" rows="5" placeholder="Nhập mô tả sản phẩm chi tiết..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh sản phẩm (*)</label>
                
                <input type="file" name="product_img" id="product_img_input" class="file-input-hidden" accept="image/*" required>
                
                <label for="product_img_input" class="file-input-label">
                    <span class="file-select-button">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Chọn tệp
                    </span>
                    <span id="file_name_display" class="file-name-display">
                        Chưa có tệp nào được chọn
                    </span>
                </label>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-square-plus"></i> Thêm sản phẩm
            </button>
            
        </form>
    </div>
</div>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('product_img_input');
        const fileNameDisplay = document.getElementById('file_name_display');

        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files.length > 0) {
                // Hiển thị tên file đầu tiên được chọn
                fileNameDisplay.textContent = fileInput.files[0].name;
                // Đổi màu chữ cho dễ nhìn
                fileNameDisplay.style.color = '#333'; 
                fileNameDisplay.style.fontStyle = 'normal';
            } else {
                fileNameDisplay.textContent = 'Chưa có tệp nào được chọn';
                fileNameDisplay.style.color = '#777'; 
                fileNameDisplay.style.fontStyle = 'italic';
            }
        });
    });
</script>

</body>
</html>