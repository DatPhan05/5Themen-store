<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

// Nếu trang chủ có dùng Category/Product:
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/product_class.php';

$db = new Database();
$conn = $db->link;

$categoryModel = new Category();
$productModel  = new Product();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <title>5Themen</title>

    

</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>


<!---------------------------------- SLIDER ------------------------------>
<section id="Sliders">
    <div class="aspect-ratio-169">
        <img src="images/Slide1.png">
        <img src="images/Slide2.png">
        <img src="images/Slide3.png">
        <img src="images/Slide4.png">
        <img src="images/Slide5.png">
    </div>

    <div class="dot-container">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</section>


<!---------------------------------- FOOTER ------------------------------>
<?php require_once __DIR__ . "/partials/footer.php"; ?>



<script src="js/slider.js"></script>
<script src="js/megamenu.js"></script>
<script src="js/main.js"></script>

</body>
</html>
