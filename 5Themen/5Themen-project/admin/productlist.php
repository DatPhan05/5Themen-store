<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/product_class.php";

$pd   = new Product();
$list = $pd->get_all_products();
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_list">
        <h1>Danh sách sản phẩm</h1>

        <table>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Loại</th>
                <th>Giá</th>
                <th>Giá KM</th>
                <th>Ảnh</th>
                <th>Tùy chỉnh</th>
            </tr>

            <?php if ($list && $list->num_rows > 0) : ?>
                <?php $i = 1; while ($r = $list->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($r['product_name']) ?></td>
                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                        <td><?= htmlspecialchars($r['brand_name']) ?></td>
                        <td><?= number_format($r['product_price']) ?> đ</td>
                        <td><?= number_format($r['product_sale']) ?> đ</td>
                        <td>
                            <?php if ($r['product_img']) : ?>
                                <img src="../<?= htmlspecialchars($r['product_img']) ?>" width="60" alt="Ảnh sản phẩm">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="productedit.php?id=<?= $r['product_id'] ?>">Sửa</a> |
                            <a onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" 
                               href="productdelete.php?id=<?= $r['product_id'] ?>">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align:center">Chưa có sản phẩm nào.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</section>
</body>
</html>
