<?php
include "admin/database.php";
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $pass     = md5($_POST['password']);
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];

    $query = "INSERT INTO tbl_user(fullname,email,password,phone,address)
              VALUES('$fullname','$email','$pass','$phone','$address')";
    $result = $db->insert($query);

    if ($result) {
        header("Location: login.php?registered=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css"> <!-- FIX CHỮ HOA -->
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
</head>

<body>

<?php include "header.php"; ?>   <!-- CHỈ GỒM HEADER -->

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Đăng ký tài khoản</h1>

        <div class="auth-card auth-card-register">
            <form method="POST" class="auth-form">

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Họ tên</label>
                        <input type="text" name="fullname" required>
                    </div>

                    <div class="auth-field">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone">
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="auth-field">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <div class="auth-field">
                    <label>Địa chỉ</label>
                    <input type="text" name="address">
                </div>

                <button type="submit" class="btn-primary btn-full">Đăng ký</button>

                <p style="margin-top:10px;">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>

            </form>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>
