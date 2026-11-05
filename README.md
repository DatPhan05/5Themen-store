# 5THEMEN – FASHION STORE

Dự án website bán hàng trực tuyến cho **Shop thời trang nam 5THEMEN**, bao gồm:

- **Giao diện khách hàng (Client Storefront):** Mua sắm, giỏ hàng AJAX, thanh toán, theo dõi đơn hàng và tài khoản người dùng.
- **Trang quản trị (Admin Panel):** Dashboard trực quan, quản lý danh mục đa cấp, thương hiệu, sản phẩm và tiến trình đơn hàng.

Dự án được xây dựng phục vụ môn **Lập trình Web (Web Developer)** và thực hành ứng dụng PHP – MySQL.

---

## 🚀 TÍNH NĂNG CHÍNH

### 1. Giao diện khách hàng (Client)
- **Trang chủ (`trangchu.php`):** Banner slider tự động, hiển thị danh sách sản phẩm mới (`NEW`) và sản phẩm khuyến mãi (`HOT`).
- **Mega Menu đa tầng (`partials/header.php`):** Danh mục phân cấp cha - con kết hợp thương hiệu động từ cơ sở dữ liệu.
- **Danh mục sản phẩm:**
  - `category.php` – Xem toàn bộ sản phẩm (hỗ trợ lọc kép theo danh mục & thương hiệu, phân trang).
  - `category_new.php` – Danh sách sản phẩm mới nhất.
  - `category_sale.php` – Danh sách sản phẩm đang giảm giá.
  - `category_group.php` – Nhóm sản phẩm theo phân loại.
- **Chi tiết sản phẩm (`product_detail.php`):** Ảnh sản phẩm, chọn size (S, M, L, XL), số lượng, tính phần trăm giảm giá và gợi ý sản phẩm liên quan.
- **Giỏ hàng tương tác cao (`giohang.php`, `them_giohang.php`):**
  - Tích hợp Fetch API (AJAX): Cập nhật số lượng và thay đổi size tức thì không reload trang.
  - Tính tổng giá trị đơn hàng tự động.
  - `remove_cart.php` – Xóa sản phẩm khỏi giỏ.
  - `reorder.php` – Mua lại đơn hàng cũ chỉ với một cú click.
- **Đặt hàng & Thanh toán (`thanhtoan.php`, `xuly_thanhtoan.php`):**
  - Hỗ trợ 3 hình thức thanh toán: COD (tiền mặt khi nhận hàng), VNPay (mô phỏng quét mã QR), MoMo (mô phỏng quét mã QR).
  - `order_success.php` – Trang thông báo đặt hàng thành công và tóm tắt biên lai.
  - `order_detail.php` – Xem chi tiết đơn hàng cá nhân.
- **Quản lý tài khoản:**
  - `login.php` / `login_process.php` – Đăng nhập an toàn (bảo vệ bằng CSRF Token & `password_verify`).
  - `register.php` – Đăng ký tài khoản (mã hóa mật khẩu chuẩn `password_hash`).
  - `logout.php` – Đăng xuất tài khoản khách hàng.
  - `account.php` – Quản lý thông tin hồ sơ, lịch sử mua hàng theo trạng thái, đổi mật khẩu.
- **Tìm kiếm sản phẩm (`search.php`):** Tìm kiếm theo tên và mô tả sản phẩm có phân trang.
- **Thông tin cửa hàng (`info.php`):** Giới thiệu shop, chính sách vận chuyển, bảo hành và đổi trả.

### 2. Trang quản trị (Admin Panel)
- **Bảo mật truy cập (`admin/auth_check.php`):** Cơ chế middleware kiểm tra phiên đăng nhập trên toàn bộ các trang quản trị và thao tác xóa dữ liệu.
- **Đăng nhập & Đăng xuất:**
  - `admin/login_admin.php` – Đăng nhập quản trị viên (mật khẩu băm Bcrypt).
  - `admin/logout_admin.php` – Đăng xuất an toàn.
- **Bảng điều khiển (`admin/admin_home.php`):** Thống kê tổng số danh mục, thương hiệu, sản phẩm với phong cách đồ họa kính hiện đại (Glassmorphism).
- **Quản lý danh mục:**
  - `admin/categorylist.php`, `categoryadd.php`, `categoryedit.php`, `categorydelete.php`.
  - Hỗ trợ cây danh mục phân cấp (`parent_id`) và cơ chế Safe Delete (chặn xóa nếu còn danh mục con).
- **Quản lý thương hiệu:**
  - `admin/brandlist.php`, `brandadd.php`, `brandedit.php`, `brandelete.php`.
  - Liên kết trực tiếp theo danh mục, kiểm tra ràng buộc khóa ngoại trước khi xóa.
- **Quản lý sản phẩm:**
  - `admin/productlist.php`, `productadd.php`, `productedit.php`, `productdelete.php`.
  - Tải ảnh sản phẩm an toàn (kiểm tra định dạng ảnh JPG/PNG/WEBP, giới hạn dung lượng, chống upload file thực thi).
- **Quản lý đơn hàng & In ấn:**
  - `admin/orders.php` – Danh sách đơn hàng, lọc theo trạng thái: Chờ duyệt, Đang xử lý, Đang giao, Hoàn tất, Đã hủy.
  - `admin/order_edit.php` – Xem chi tiết sản phẩm và cập nhật trạng thái giao vận.
  - `admin/order_invoice.php` – Mẫu hóa đơn bán hàng chuẩn khổ in ấn (hỗ trợ in trực tiếp).

---

## 🛠️ CÔNG NGHỆ SỬ DỤNG

- **Backend:** PHP 8.x (Hướng module hóa & OOP)
- **Cơ sở dữ liệu:** MySQL / MariaDB (kết nối qua extension `mysqli`)
- **Frontend:** HTML5, CSS3 (Glassmorphism, Flexbox, Grid), JavaScript (Vanilla JS, Fetch API AJAX)
- **Thư viện icon:** Font Awesome 6
- **CI/CD:** GitHub Actions (tự động kiểm tra cú pháp PHP Syntax Check)

---

## 📁 CẤU TRÚC THƯ MỤC

```text
5THEMEN-STORE/
├── .github/workflows/
│   └── php.yml                        # GitHub Actions kiểm tra cú pháp mã nguồn
├── website_5themen_store_demo.sql     # File CSDL mẫu đầy đủ (Import vào phpMyAdmin)
├── README.md                          # Tài liệu dự án
└── 5Themen/
    └── 5Themen-project/
        ├── admin/                     # Phân hệ quản trị
        │   ├── class/                 # Các class nghiệp vụ dữ liệu (OOP Data Layer)
        │   │   ├── brand_class.php
        │   │   ├── category_class.php
        │   │   ├── order_class.php
        │   │   ├── product_class.php
        │   │   └── user_class.php
        │   ├── uploads/               # Thư mục lưu ảnh sản phẩm
        │   ├── auth_check.php         # Guard kiểm tra đăng nhập Admin
        │   ├── admin_home.php         # Dashboard thống kê
        │   ├── header.php / slider.php
        │   ├── login_admin.php / logout_admin.php
        │   ├── category*.php / brand*.php / product*.php
        │   ├── orders.php / order_edit.php / order_invoice.php
        │   └── website_5themen_store_demo.sql
        ├── CSS/
        │   └── style.css              # Tệp CSS phong cách Ivy Moda & Glassmorphism
        ├── images/                    # Tài nguyên hình ảnh, banner, logo, icon QR
        ├── include/                   # Thư viện dùng chung
        │   ├── auth.php               # Helper xác thực khách hàng
        │   ├── config.php             # Cấu hình kết nối Database & URL gốc
        │   ├── database.php           # MySQLi Wrapper Class
        │   ├── helpers.php            # Format helper
        │   └── session.php            # Session helper tập trung
        ├── js/
        │   ├── main.js                # Hiệu ứng dính Header khi cuộn
        │   ├── megamenu.js            # Delay đóng/mở Mega Menu
        │   └── slider.js              # Banner Auto-slider
        ├── partials/                  # Thành phần giao diện dùng chung
        │   ├── breadcrumb.php
        │   ├── footer.php
        │   └── header.php
        ├── account.php                # Quản lý tài khoản cá nhân & đơn hàng
        ├── category*.php              # Các trang danh mục sản phẩm
        ├── giohang.php / them_giohang.php / remove_cart.php
        ├── info.php / search.php
        ├── login.php / login_process.php / register.php / logout.php
        ├── order_detail.php / order_success.php / reorder.php
        ├── thanhtoan.php / xuly_thanhtoan.php
        └── trangchu.php               # Trang chủ chính của website
```

---

## ⚙️ CÀI ĐẶT VÀ CHẠY DỰ ÁN

### 1. Chuẩn bị môi trường
- Cài đặt **XAMPP** (hỗ trợ PHP 8.0 trở lên).
- Khởi động module **Apache** và **MySQL** trong XAMPP Control Panel.

### 2. Thiết lập cơ sở dữ liệu
1. Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
2. Tạo mới một cơ sở dữ liệu với tên:
   ```sql
   CREATE DATABASE website_5themen_store_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Chọn cơ sở dữ liệu vừa tạo, chuyển sang tab **Import** (Nhập) và tải lên file:
   - `website_5themen_store_demo.sql` (nằm ở thư mục gốc của dự án hoặc trong `admin/`).

### 3. Cấu hình kết nối CSDL
Mở file `5Themen/5Themen-project/include/config.php` và kiểm tra lại thông số:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'website_5themen_store_demo');
define('BASE_URL', '/5Themen/5Themen-project/');
```

### 4. Truy cập ứng dụng
Đặt thư mục dự án vào thư mục `htdocs` của XAMPP:
- **Giao diện khách hàng:**  
  `http://localhost/5Themen-store/5Themen/5Themen-project/trangchu.php`
- **Giao diện quản trị Admin:**  
  `http://localhost/5Themen-store/5Themen/5Themen-project/admin/login_admin.php`

---

## 🔑 TÀI KHOẢN MẪU DÙNG THỬ

| Phân hệ | Tên đăng nhập / Email | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị (Admin)** | `admin` | `admin123` | Toàn quyền quản trị hệ thống |
| **Khách hàng (Client)** | `khachhang@5themen.vn` | `user123` | Tài khoản khách hàng mẫu có lịch sử đơn hàng |

---

## 🛡️ BẢO MẬT & CẢI TIẾN ĐÃ THỰC HIỆN

- ✅ **Khóa bảo mật Admin Guard (`admin/auth_check.php`):** Ngăn chặn người dùng vãng lai truy cập trái phép vào các trang quản lý hoặc gửi request xóa sản phẩm/danh mục/đơn hàng.
- ✅ **Chống upload mã độc (RCE Protection):** Kiểm tra chặt chẽ định dạng ảnh (JPG, PNG, WEBP, GIF), xác thực ảnh bằng `getimagesize()` và giới hạn kích thước tối đa 5MB tại `admin/productadd.php`.
- ✅ **Đồng bộ hóa mã hóa mật khẩu:** Chuẩn hóa thuật toán băm Bcrypt (`password_hash` và `password_verify`) đồng bộ giữa Đăng ký, Đăng nhập, và Đổi mật khẩu trong `account.php` & `user_class.php`.
- ✅ **Sửa lỗi đồng bộ bảng đơn hàng:** Khắc phục truy vấn trong `order_success.php` liên kết chính xác với bảng `tbl_order_detail` và `tbl_product`.
- ✅ **Tối ưu tìm kiếm:** Nâng cấp thuật toán tìm kiếm trong `product_class.php` để tìm kiếm linh hoạt theo từ khóa trong tên và mô tả sản phẩm.
- ✅ **Chuẩn hóa CI/CD:** Thay thế workflow Laravel kiểm thử không tương thích bằng workflow PHP Syntax Check tự động.
