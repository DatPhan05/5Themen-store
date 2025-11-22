<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/admin/class/product_class.php';

$productModel = new Product();

/*************************************************
 * KHỞI TẠO GIỎ NẾU CHƯA CÓ
 *************************************************/
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/*************************************************
 * 1. UPDATE SỐ LƯỢNG (+ / -)
 *    URL dạng: them_giohang.php?action=update&id=10&qty=2
 *************************************************/
if (isset($_GET['action']) && $_GET['action'] == "update") {

    $id  = (int)$_GET['id'];
    $qty = (int)$_GET['qty'];

    if ($qty < 1) $qty = 1;

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = $qty;
    }

    header("Location: giohang.php");
    exit;
}


/*************************************************
 * 2. THAY ĐỔI SIZE
 *    URL dạng: them_giohang.php?action=changesize&id=10&size=M
 *************************************************/
if (isset($_GET['action']) && $_GET['action'] == "changesize") {

    $id   = (int)$_GET['id'];
    $size = $_GET['size'] ?? "L";

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['size'] = $size;
    }

    header("Location: giohang.php");
    exit;
}


/*************************************************
 * 3. THÊM SẢN PHẨM VÀO GIỎ (POST từ trang chi tiết)
 *************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $qty   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $size  = $_POST['option2'] ?? "L";

    if ($id <= 0) {
        header("Location: giohang.php");
        exit;
    }

    // Lấy sản phẩm
    $product = $productModel->get_product($id);

    if (!$product) {
        header("Location: giohang.php");
        exit;
    }

    // Nếu chưa có thì thêm mới
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            "name"  => $product["product_name"],
            "price" => (float) $product["product_price"],
            "qty"   => $qty,
            "image" => $product["product_img"],
            "size"  => $size,
            "color" => ""
        ];
    }
    // Nếu đã có → cộng dồn
    else {
        $_SESSION['cart'][$id]['qty'] += $qty;
    }

    header("Location: giohang.php");
    exit;
}


/*************************************************
 * 4. THÊM TỪ GET (category.php?action=add)
 *************************************************/
if (isset($_GET['action']) && $_GET['action'] == "add") {

    $id = (int)$_GET['id'];

    if ($id > 0) {
        $product = $productModel->get_product($id);

        if ($product) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = [
                    "name"  => $product["product_name"],
                    "price" => (float) $product["product_price"],
                    "qty"   => 1,
                    "image" => $product["product_img"],
                    "size"  => "L",
                    "color" => ""
                ];
            } else {
                $_SESSION['cart'][$id]['qty']++;
            }
        }
    }

    header("Location: giohang.php");
    exit;
}

header("Location: giohang.php");
exit;
?>
