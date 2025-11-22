<?php
// Tên file: admin/posts.php (Đã đổi tên file cho rõ ràng hơn)

// Bổ sung các file cần thiết
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php"; // Menu bên trái
require_once __DIR__ . "/Class/post_class.php";

$postModel = new Post();
// Giả định get_all_posts() trả về kết quả hoặc null
$list = $postModel->get_all_posts();

// Hàm tiện ích hiển thị trạng thái
function display_status_badge($status) {
    if ($status == 1) {
        return '<span class="badge badge-success">✔ Hiển thị</span>';
    } else {
        return '<span class="badge badge-secondary">❌ Ẩn</span>';
    }
}
?>

<style>
    /* ================= LAYOUT CHÍNH (Kế thừa từ slider.php) ================= */
    .admin-content-right {
        flex: 1; 
        padding: 40px;
        position: relative;
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* ================= NÚT THÊM BÀI VIẾT ================= */
    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        /* Màu xanh lá cho nút Thêm */
        background: linear-gradient(135deg, #1abc9c, #16a085); 
        color: white;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(22, 160, 133, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(22, 160, 133, 0.4);
    }

    /* ================= BẢNG GLASSMORPHISM ================= */
    .table-container {
        margin-top: 20px;
        overflow-x: auto; 
    }

    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 800px;
        border-radius: 15px;
        overflow: hidden; /* Quan trọng để border-radius hoạt động */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.2); /* Nền mờ nhẹ */
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    
    .admin-table thead th {
        background: rgba(255, 255, 255, 0.5); /* Header mờ hơn */
        font-weight: 700;
        color: #333;
        text-transform: uppercase;
        font-size: 13px;
        padding: 15px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        position: sticky;
        top: 0;
    }

    .admin-table tbody td {
        padding: 12px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        color: #555;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .admin-table tbody tr:hover td {
background: rgba(255, 255, 255, 0.4);
    }

    /* ================= THẺ TRẠNG THÁI (BADGE) ================= */
    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
    }
    .badge-success {
        background-color: rgba(39, 174, 96, 0.15); /* Xanh lá nhạt */
        color: #27ae60;
    }
    .badge-secondary {
        background-color: rgba(149, 165, 166, 0.15); /* Xám nhạt */
        color: #95a5a6;
    }

    /* ================= NÚT THAO TÁC ================= */
    .btn-sm {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        margin-right: 5px;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #3498db; /* Xanh dương */
        color: white;
    }
    .btn-edit:hover {
        background-color: #2980b9;
    }

    .btn-delete {
        background-color: #e74c3c; /* Đỏ */
        color: white;
    }
    .btn-delete:hover {
        background-color: #c0392b;
    }
    
    /* Trang trí background nhẹ */
    .blob-decor-list {
        position: absolute;
        width: 400px;
        height: 400px;
        background: linear-gradient(180deg, #ffafc0 0%, #ffcba4 100%);
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.3;
        z-index: -1;
        top: 60%;
        right: 0%;
        transform: translate(50%, -50%);
    }
</style>

<div class="admin-content-right">
    <div class="blob-decor-list"></div>

    <h2 class="form-title"><i class="fa-solid fa-newspaper"></i> DANH SÁCH BÀI VIẾT</h2>

    <div style="margin-bottom: 25px;">
        <a href="postadd.php" class="btn-submit">
            <i class="fa-solid fa-plus"></i> Thêm bài viết mới
        </a>
    </div>

    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Chuyên mục</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th>Tùy chỉnh</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php while ($row = $list->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['post_id']); ?></td>
                            <td><?= htmlspecialchars($row['title']); ?></td>
                            <td><?= htmlspecialchars($row['category']); ?></td> 
                            <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?= display_status_badge($row['status']); ?>
                            </td>
<td>
                                <a href="postedit.php?id=<?= $row['post_id']; ?>" class="btn-sm btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="postdelete.php?id=<?= $row['post_id']; ?>" 
                                   class="btn-sm btn-delete"
                                   onclick="return confirm('Xóa bài viết ID #<?= $row['post_id']; ?>?');">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 20px;">Chưa có bài viết nào được tạo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</section>
</body>
</html>