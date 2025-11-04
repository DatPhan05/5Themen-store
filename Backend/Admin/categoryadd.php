
<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/category_class.php";

$msg = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['category_name'] ?? '');
    if($name!==""){
        $cg = new category();
        $cg->insert_category($name);
        $msg = "Đã thêm danh mục.";
    } else {
        $msg = "Vui lòng nhập tên danh mục.";
    }
}
?>
<div class="admin-content-right">
  <div class="admin-content-right-category_add">
    <h1>Thêm Danh mục</h1>
    <?php if($msg){ echo "<p>$msg</p>"; }?>
    <form action="" method="POST">
      <input name="category_name" type="text" placeholder="Nhập tên danh Mục">
      <button type="submit">Thêm</button>
    </form>
  </div>
</div>
</section></body></html>
