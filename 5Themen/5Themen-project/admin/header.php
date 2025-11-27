<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>

    <!-- Font & Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(45deg, #f3e7e9 0%, #e3eeff 100%);
            min-height: 100vh;
            padding-top: var(--header-height);
        }

        /* Header Glass Effect */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--header-height);
            padding: 0 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        header h1 a {
            text-decoration: none;
            font-size: 22px;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-user-info {
            font-size: 14px;
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.35);
            padding: 6px 18px;
            border-radius: 30px;
        }

        .header-user-info i {
            font-size: 18px;
        }

        .main-content-wrapper {
            width: 100%;
            padding: 20px;
        }
    </style>
</head>

<body>

<header>
    <h1>
        <a href="admin_home.php">
            <i class="fa-solid fa-shield-halved"></i> TRANG QUẢN TRỊ
        </a>
    </h1>

    <div class="header-user-info">
        <i class="fa-solid fa-circle-user"></i>
        <span>Xin chào, Admin</span>

        <!-- Nút đăng xuất -->
        <a href="logout_admin.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
        </a>
    </div>
</header>


<div class="main-content-wrapper">
