#  5THEMEN – FASHION STORE

Dự án website bán hàng cho **shop thời trang nam 5THEMEN**, gồm:

- Giao diện khách hàng (Client)
- Trang quản trị (Admin Panel)

Dự án được xây dựng phục vụ môn **Web Developer** và thực hành PHP – MySQL. :contentReference[oaicite:0]{index=0}  


##  TÍNH NĂNG CHÍNH

###  Giao diện khách hàng (Client)
- Trang chủ hiển thị sản phẩm nổi bật, banner, slider
- Danh mục sản phẩm:
  - `category.php` – danh mục tất cả
  - `category_new.php` – sản phẩm mới
  - `category_sale.php` – sản phẩm khuyến mãi
  - `category_group.php` – nhóm sản phẩm
- Chi tiết sản phẩm: `product_detail.php`
- Giỏ hàng:
  - `giohang.php` – xem giỏ hàng
  - `them_giohang.php` – thêm sản phẩm vào giỏ
  - `remove_cart.php` – xóa sản phẩm khỏi giỏ
  - `reorder.php` – mua lại
- Đặt hàng & thanh toán:
  - `thanhtoan.php`
  - `xuly_thanhtoan.php`
  - `order_success.php` – đặt hàng thành công
  - `order_detail.php` – xem chi tiết đơn hàng (phía user)
- Tài khoản:
  - `login.php` / `login_process.php`
  - `register.php`
  - `logout.php`
  - `account.php` – thông tin tài khoản
- Tìm kiếm sản phẩm: `search.php`
- Thông tin cửa hàng: `info.php`
- Header / Footer / Breadcrumb dùng chung trong thư mục `partials/`

###  Trang quản trị (Admin Panel)

- Đăng nhập / đăng xuất admin:
  - `admin/login_admin.php`
  - `admin/logout_admin.php`
- Trang tổng quan: `admin/admin_home.php`
- Quản lý danh mục:
  - `admin/categorylist.php`
  - `admin/categoryadd.php`
  - `admin/categoryedit.php`
  - `admin/categorydelete.php`
- Quản lý thương hiệu:
  - `admin/brandlist.php`
  - `admin/brandadd.php`
  - `admin/brandedit.php`
  - `admin/branddelete.php`
- Quản lý sản phẩm:
  - `admin/productlist.php`
  - `admin/productadd.php`
  - `admin/productedit.php`
  - `admin/productdelete.php`
- Quản lý đơn hàng:
  - `admin/orders.php`
  - `admin/order_invoice.php`
- Quản lý tài khoản admin (class `user_class.php`)

## CÔNG NGHỆ SỬ DỤNG

PHP, 
MySQL, 
HTML, CSS, JavaScript, 
Font Awesome; sử dụng PHP Session để quản lý đăng nhập và giỏ hàng.

## CẤU TRÚC THƯ MỤC
5THEMEN-STORE/
└── 5Themen/5Themen-project/
    ├── admin/                     # Khu vực quản trị
    │   ├── class/                 # Các lớp làm việc với CSDL
    │   │   ├── brand_class.php
    │   │   ├── category_class.php
    │   │   ├── order_class.php
    │   │   ├── product_class.php
    │   │   └── user_class.php
    │   ├── uploads/               # Ảnh sản phẩm (admin upload)
    │   ├── admin_home.php
    │   ├── brandadd.php
    │   ├── branddelete.php
    │   ├── brandedit.php
    │   ├── brandlist.php
    │   ├── categoryadd.php
    │   ├── categorydelete.php
    │   ├── categoryedit.php
    │   ├── categorylist.php
    │   ├── header.php             # Header riêng cho admin
    │   ├── login_admin.php
    │   ├── logout_admin.php
    │   ├── order_invoice.php
    │   ├── orders.php
    │   ├── productadd.php
    │   ├── productdelete.php
    │   ├── productedit.php
    │   └── productlist.php
    │
    ├── CSS/
    │   └── style.css              # Toàn bộ style frontend
    │
    ├── images/                    # Ảnh slider, banner, logo, sản phẩm
    │
    ├── include/                   # File cấu hình + tiện ích dùng chung
    │   ├── auth.php               # Kiểm tra đăng nhập
    │   ├── config.php             # Thông tin cấu hình DB
    │   ├── database.php           # Lớp Database kết nối MySQL
    │   ├── helpers.php            # Hàm hỗ trợ (format tiền, redirect,…)
    │   └── session.php            # Lớp Session helper
    │
    ├── js/
    │   ├── main.js                # JS chung cho giao diện
    │   ├── megamenu.js            # Xử lý mega menu
    │   └── slider.js              # Slider banner
    │
    ├── partials/                  # Thành phần HTML tái sử dụng
    │   ├── breadcrumb.php
    │   ├── footer.php
    │   └── header.php
    │
    ├── account.php
    ├── category_group.php
    ├── category_new.php
    ├── category_sale.php
    ├── category.php
    ├── giohang.php
    ├── info.php
    ├── login.php
    ├── login_process.php
    ├── logout.php
    ├── order_detail.php
    ├── order_success.php
    ├── product_detail.php
    ├── register.php
    ├── remove_cart.php
    ├── reorder.php
    ├── search.php
    ├── thanhtoan.php
    ├── them_giohang.php
    ├── trangchu.php               # Trang chủ người dùng
    ├── xuly_thanhtoan.php
    └── README.md

## CÀI ĐẶT VÀ CHẠY DỰ ÁN

Cài đặt XAMPP , bật Apache và MySQL.  
Mở phpMyAdmin → tạo database:

CREATE DATABASE website_5themen_store_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

Import file .sql của dự án.  

Sau đó mở file /include/config.php và chỉnh cấu hình:

define('DB_HOST', 'localhost');  
define('DB_USER', 'root');  
define('DB_PASS', '');  
define('DB_NAME', 'website_5themen_store_demo');

Chạy website:  
Client 
→ http://5themen.localhost/5Themen/5Themen-project/trangchu.php  
Admin 
→ http://5themen.localhost/5Themen/5Themen-project/admin/login_admin.php

## BẢO MẬT & QUY ƯỚC CODE

Dự án sử dụng class Database để hạn chế SQL Injection, session quản lý đăng nhập người dùng và admin, tách header/footer/breadcrumb để tái sử dụng, mã nguồn được tổ chức rõ ràng theo từng thư mục.
