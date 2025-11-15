<?php session_start(); ?>
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

<?php include "header.php"; ?>


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
<?php include "footer.php"; ?>



<script src="js/slider.js"></script>

</body>
</html>
