<?php
require_once "../include/session.php";
require_once "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/category_class.php";

$cg         = new Category();
$msg        = "";
$msg_type   = "";
$old_name   = "";
$old_parent = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name       = trim($_POST['category_name'] ?? '');
    $old_name   = $name;

    $parent_id  = ($_POST['parent_id'] !== '') ? (int)$_POST['parent_id'] : 0;
    $old_parent = $parent_id;

    if ($name !== "") {
        $cg->insert_category($name, $parent_id);
        $msg        = "✨ Đã thêm danh mục thành công!";
        $msg_type   = "success";
        $old_name   = "";
        $old_parent = 0;
    } else {
        $msg      = "⚠️ Vui lòng nhập tên danh mục!";
        $msg_type = "error";
    }
}

$parents = $cg->get_parent_categories();
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
        max-width: 600px;
        padding: 40px;
        border-radius: 20px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 8px 32px rgba(31,38,135,0.15);
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        color: #333;
        margin-bottom: 30px;
        text-transform: uppercase;
    }

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
        background: rgba(255,255,255,0.5);
        border: 1px solid rgba(255,255,255,0.8);
        font-size: 15px;
        color: #333;
        transition: 0.3s;
        outline: none;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.95);
        border-color: #4b7bec;
        box-shadow: 0 0 0 4px rgba(75,123,236,0.1);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,<svg fill='none' stroke='%23333' stroke-width='2' viewBox='0 0 24 24'><polyline points='6 9 12 15 18 9'></polyline></svg>");
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
        background: linear-gradient(135deg, #4b7bec, #3867d6);
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(75,123,236,0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(75,123,236,0.4);
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

    .blob-decor {
        position: absolute;
        width: 300px;
        height: 300px;
        background: linear-gradient(180deg, #a18cd1, #fbc2eb);
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

        <h1 class="form-title"><i class="fa-solid fa-folder-plus"></i> Thêm danh mục</h1>

        <?php if (!empty($msg)): ?>
            <div class="alert <?= $msg_type === 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= $msg_type === 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label class="form-label">Tên danh mục</label>
                <input
                    type="text"
                    name="category_name"
                    class="form-control"
                    placeholder="Ví dụ: Áo, Quần, Giày..."
                    required
                    autocomplete="off"
                    value="<?= htmlspecialchars($old_name) ?>"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Danh mục cha (không bắt buộc)</label>
                <select name="parent_id" class="form-control">
                    <option value="0">-- Không có danh mục cha --</option>

                    <?php if ($parents): ?>
                        <?php while ($p = $parents->fetch_assoc()): ?>
                            <option value="<?= $p['category_id'] ?>"
                                <?= ($old_parent == $p['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['category_name']) ?>
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
