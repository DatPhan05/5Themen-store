<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/product_class.php";
require_once __DIR__ . "/Class/category_class.php";
require_once __DIR__ . "/Class/brand_class.php";

$id = (int)($_GET['id'] ?? 0);
$pd = new Product();
$row = $pd->get_product($id);
if(!$row){ die("Không tìm thấy sản phẩm"); }

$cg = new category(); $bd = new Brand();
$cates = $cg->show_category(); $brands = $bd->show_brand();

$msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $cid = (int)($_POST['category_id'] ?? 0);
    $bid = (int)($_POST['brand_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $sale = (int)($_POST['sale_price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $thumb = trim($_POST['thumb'] ?? '');
    if($name && $cid && $bid){
        $thumbVal = $thumb!=="" ? $thumb : null;
        $pd->update_product($id,$name,$cid,$bid,$price,$sale,$desc,$thumbVal);
        $msg="Đã lưu thay đổi.";
        $row = $pd->get_product($id);
    } else { $msg="Vui lòng nhập đủ thông tin bắt buộc."; }
}
?>
<div class="admin-content-right">
  <div class="admin-content-right-product_add">
    <h1>Sửa Sản Phẩm</h1>
    <?php if($msg){ echo "<p>$msg</p>"; }?>
    <form action="" method="POST">
      <label>Nhập tên sản phẩm *</label>
      <input name="name" type="text" value="<?= htmlspecialchars($row['ten_san_pham']) ?>">
      <label>Chọn danh mục *</label>
      <select name="category_id">
        <option value="">--Chọn--</option>
        <?php if($cates){ while($c=$cates->fetch_assoc()){ $sel = ($row['id_danh_muc']==$c['category_id'])?'selected':''; ?>
          <option value="<?= $c['category_id'] ?>" <?= $sel ?>><?= htmlspecialchars($c['category_name']) ?></option>
        <?php }} ?>
      </select>
      <label>Chọn loại sản phẩm *</label>
      <select name="brand_id">
        <option value="">--Chọn--</option>
        <?php if($brands){ while($b=$brands->fetch_assoc()){ $sel = ($row['id_loai']==$b['brand_id'])?'selected':''; ?>
          <option value="<?= $b['brand_id'] ?>" <?= $sel ?>><?= htmlspecialchars($b['brand_name']) ?> (<?= htmlspecialchars($b['ten_danh_muc']) ?>)</option>
        <?php }} ?>
      </select>
      <label>Giá sản phẩm</label>
      <input name="price" type="number" value="<?= (int)$row['gia'] ?>">
      <label>Giá khuyến mãi</label>
      <input name="sale_price" type="number" value="<?= (int)$row['gia_khuyen_mai'] ?>">
      <label>Mô tả sản phẩm</label>
      <textarea name="description" cols="30" rows="10"><?= htmlspecialchars($row['mo_ta'] ?? '') ?></textarea>
      <label>Ảnh (đường dẫn)</label>
      <input name="thumb" type="text" value="<?= htmlspecialchars($row['anh'] ?? '') ?>">
      <button type="submit">Lưu</button>
    </form>
  </div>
</div>
</section></body></html>
