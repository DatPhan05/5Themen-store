<?php
require_once "../include/session.php";
require_once "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/brand_class.php";
require_once __DIR__ . "/class/category_class.php";

$cg     = new Category();
$brands = new Brand();

$msg      = "";
$msg_type = "";

/* Lấy danh sách danh mục */
$categories_result = $cg->show_category();

/* Xử lý thêm brand */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $name = trim($_POST['brand_name']);

    if ($cid > 0 && $name !== "") {
        $brands->insert_brand($cid, $name);
        $msg      = "✨ Đã thêm loại sản phẩm thành công!";
        $msg_type = "success";
    } else {
        $msg      = "⚠️ Vui lòng chọn danh mục và nhập tên loại sản phẩm.";
        $msg_type = "error";
    }
}
?>

<style>
    /* Layout */
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
        background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        z-index: -1;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Form container */
    .form-container {
        width: 100%;
        max-width: 600px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-align: center;
        text-transform: uppercase;
    }

    /* Input fields */
    .form-group { margin-bottom: 25px; }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.5);
        font-size: 15px;
        color: #333;
        outline: none;
        transition: 0.3s;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.95);
        border-color: #fc5c65;
        box-shadow: 0 0 0 4px rgba(252, 92, 101, 0.15);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        cursor: pointer;
    }

    /* Button */
    .btn-submit {
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #fa8231, #fc5c65);
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(252, 92, 101, 0.3);
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(252, 92, 101, 0.4);
        filter: brightness(1.1);
    }

    /* Alerts */
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
</style>

<div class="admin-content-right">

    <div class="blob-decor"></div>

    <div class="form-container">
        <h1 class="form-title">
            <i class="fa-solid fa-tag"></i> Thêm loại sản phẩm
        </h1>

        <?php if (!empty($msg)) : ?>
            <div class="alert <?= $msg_type == 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= $msg_type == 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">

            <div class="form-group">
                <label class="form-label">Chọn danh mục</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>

                    <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                        <?php
                        $categories_result->data_seek(0);
                        while ($c = $categories_result->fetch_assoc()):
                            if ($c['parent_id']):
                        ?>
                            <option value="<?= $c['category_id'] ?>">
                                <?= htmlspecialchars($c['category_name']) ?>
                            </option>
                        <?php endif; endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tên loại sản phẩm</label>
                <input type="text" name="brand_name" class="form-control"
                       placeholder="Nhập tên loại sản phẩm" required autocomplete="off">
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-plus-circle"></i> Thêm loại sản phẩm
            </button>
        </form>
    </div>
</div>

</section>
</body>
</html>
