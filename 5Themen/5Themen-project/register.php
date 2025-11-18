<?php
include "admin/database.php";
$db = new Database();
$conn = $db->link;   // lấy mysqli connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    // Kiểm tra trùng email
    $emailEscaped = $conn->real_escape_string($email);
    $check = $conn->query("SELECT * FROM tbl_user WHERE email = '$emailEscaped' LIMIT 1");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email đã tồn tại. Vui lòng dùng email khác!');</script>";
    } else {

        // HASH mật khẩu chuẩn PHP
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $fullnameEsc = $conn->real_escape_string($fullname);
        $phoneEsc    = $conn->real_escape_string($phone);
        $addressEsc  = $conn->real_escape_string($address);

        $query = "
        INSERT INTO tbl_user(fullname, email, password, phone, address)
        VALUES ('$fullnameEsc', '$emailEscaped', '$passHash', '$phoneEsc', '$addressEsc')";

        if ($conn->query($query)) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            echo "<script>alert('Đăng ký thất bại.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
</head>

<body>

<?php include "header.php"; ?>

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

                <p style="margin-top:10px;">Đã có tài khoản <a href="login.php">Đăng nhập</a></p>

            </form>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>
