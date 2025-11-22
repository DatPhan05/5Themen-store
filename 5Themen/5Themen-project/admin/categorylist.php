<?php
// Bổ sung các file cần thiết (Giả định nằm ngoài thư mục admin 1 cấp)
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
// Đường dẫn Class đã được điều chỉnh về chữ thường 'class'
require_once __DIR__ . "/class/category_class.php"; 

$cg = new Category();
// Kiểm tra dữ liệu trả về có phải là object result set không
$list = $cg->show_category();
?>

<style>
    /* ================= LAYOUT & CARD STYLE ================= */
    .admin-content-right {
        flex: 1; 
        padding: 40px;
        position: relative;
    }

    .list-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 30px;
        border-radius: 20px;
        
        /* Hiệu ứng kính */
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .list-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* ================= TABLE STYLE ================= */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
        font-size: 14px;
        overflow: hidden; /* Quan trọng để bo góc được */
    }

    /* Tiêu đề bảng */
    table th {
        background: linear-gradient(90deg, #4b7bec, #3867d6);
        color: white;
        padding: 15px 15px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap; /* Ngăn tiêu đề bị xuống dòng */
    }
    
    /* Bo góc cho hàng tiêu đề */
    table tr:first-child th:first-child { border-top-left-radius: 10px; }
    table tr:first-child th:last-child { border-top-right-radius: 10px; }


    /* Nội dung các ô */
    table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        background: rgba(255, 255, 255, 0.7);
        color: #333;
        font-weight: 500;
    }

    /* Hiệu ứng Hover cho hàng */
    table tbody tr {
        transition: background 0.3s ease;
    }
    table tbody tr:hover {
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    /* Dòng cuối cùng không có border bottom */
    table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Cột Danh mục cha */
    table td:nth-child(4) { 
        color: #777; 
        font-style: italic;
    }
    
    /* ================= BUTTONS STYLE ================= */
    .action-link {
        display: inline-flex; /* Dùng flex để căn giữa icon và text */
        align-items: center;
        gap: 5px;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 5px;
        font-size: 13px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .edit-btn {
        background: #ffeaa7; /* Vàng nhạt */
        color: #d63031;
    }
    .edit-btn:hover {
        background: #fed330;
    }

    .delete-btn {
        background: #ff7675; /* Đỏ nhạt */
        color: white;
    }
    .delete-btn:hover {
        background: #e17055;
    }
    
    /* Trang trí background nhẹ */
    .blob-decor-list {
        position: absolute;
        width: 350px;
        height: 350px;
        background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.3;
        z-index: -1;
        top: 10%;
        right: 5%;
        animation: float 10s infinite alternate;
    }
    
    @keyframes float { 
        0% { transform: translate(0, 0); } 
        100% { transform: translate(-20px, 20px); } 
    }
</style>

<div class="admin-content-right">
    
    <div class="blob-decor-list"></div>

    <div class="list-container">
        <h1 class="list-title"><i class="fa-solid fa-list-check"></i> Danh sách Danh mục</h1>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Danh mục cha</th>
                    <th>Tùy chỉnh</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php 
                        $i = 1; 
                        while ($r = $list->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['category_id'] ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td>
                                <?= ($r['parent_id'] && $r['parent_id'] != 0) ? $r['parent_id'] : "—" ?>
                            </td>
                            <td>
                                <a href="categoryedit.php?category_id=<?= $r['category_id'] ?>" class="action-link edit-btn">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a> 
                                <a onclick="return confirm('Bạn có chắc muốn xóa danh mục ID: <?= $r['category_id'] ?>? LƯU Ý: Nếu có danh mục con, chúng có thể bị lỗi.')" 
                                   href="categorydelete.php?category_id=<?= $r['category_id'] ?>" class="action-link delete-btn">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #777; font-style: italic;">Chưa có danh mục nào được thêm.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</section>
</body>
</html>