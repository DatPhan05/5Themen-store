<?php
require_once "../include/session.php";
require_once "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/brand_class.php";
require_once __DIR__ . "/class/category_class.php";

$id = (int)($_GET['brand_id'] ?? 0);

$brand = new Brand();
$row   = $brand->get_brand($id);

if (!$row) {
    die('
        <div style="text-align:center;margin-top:50px;font-size:18px;color:#dc3545;">
            ❌ Không tìm thấy loại sản phẩm ID: ' . htmlspecialchars($id) . '
            <br><a href="brandlist.php" style="color:#007bff;text-decoration:none;">Quay lại danh sách</a>
        </div>
    ');
}

$cg    = new Category();
$cates = $cg->show_category();

$msg      = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');

    if ($cid && $name !== "") {
        $result = $brand->update_brand($id, $cid, $name);

        if ($result) {
            $msg      = "✔ Đã lưu thay đổi loại sản phẩm thành công.";
            $msg_type = "success";
            $row      = $brand->get_brand($id);
        } else {
            $msg      = "❌ Không thể cập nhật loại sản phẩm. Vui lòng thử lại.";
            $msg_type = "error";
        }
    } else {
        $msg      = "⚠️ Vui lòng chọn danh mục và nhập tên loại.";
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

    .form-container {
        width: 100%;
        max-width: 500px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px rgba(31,38,135,0.15);
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }

    .form-title {
        font-size: 26px;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        color: #333;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .form-group { margin-bottom: 25px; }
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.8);
        background: rgba(255,255,255,0.5);
        font-size: 16px;
        color: #333;
        transition: 0.3s;
        outline: none;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.95);
        border-color: #4ecdc4;
        box-shadow: 0 0 0 4px rgba(78,205,196,0.15);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,<svg fill='none' stroke='%23333' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><polyline points='6 9 12 15 18 9'></polyline></svg>");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        cursor: pointer;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #4ecdc4, #1abc9c);
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(78,205,196,0.3);
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78,205,196,0.4);
        filter: brightness(1.1);
    }

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
        background: rgba(32,191,107,0.15);
        border: 1px solid rgba(32,191,107,0.3);
        color: #20bf6b;
    }

    .alert-error {
        background: rgba(252,92,101,0.15);
        border: 1px solid rgba(252,92,101,0.3);
        color: #fc5c65;
    }
</style>

<div class="admin-content-right">

    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title"><i class="fa-solid fa-list-ul"></i> Sửa loại sản phẩm</h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= $msg_type == 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= $msg_type == 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label class="form-label">Chọn danh mục</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>

                    <?php
                    if ($cates) :
                        $cates->data_seek(0);
                        while ($c = $cates->fetch_assoc()) :
                    ?>
                        <option value="<?= $c['category_id'] ?>"
                            <?= ($row['category_id'] == $c['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['category_name']) ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tên loại sản phẩm</label>
                <input 
                    type="text"
                    name="brand_name"
                    class="form-control"
                    value="<?= htmlspecialchars($row['brand_name']) ?>"
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
