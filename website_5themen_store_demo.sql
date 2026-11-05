-- phpMyAdmin SQL Dump
-- CSDL chính thức cho dự án: 5THEMEN – FASHION STORE
-- Tên CSDL: website_5themen_store_demo
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Bảng: `tbl_admin` (Tài khoản Quản trị)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_user` varchar(100) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `idx_admin_user` (`admin_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mật khẩu mặc định: admin123
INSERT INTO `tbl_admin` (`admin_id`, `admin_user`, `admin_pass`, `admin_name`) VALUES
(1, 'admin', '$2y$10$AxX6NDkxHdR4dLA/6STqVOvW5RXuA6f0GqdR9uT6mNlcj0KBKtFtC', 'Quản trị viên');

-- --------------------------------------------------------
-- Bảng: `tbl_category` (Danh mục đa cấp)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_category` (`category_id`, `category_name`, `parent_id`) VALUES
(1, 'SẢN PHẨM', 0),
(2, 'SẢN PHẨM MỚI', 0),
(3, 'KHUYẾN MÃI HOT', 0),
(4, 'THÔNG TIN', 0),
(5, 'ÁO NAM', 1),
(6, 'QUẦN NAM', 1),
(7, 'PHỤ KIỆN', 1),
(8, 'GIÀY DÉP', 1);

-- --------------------------------------------------------
-- Bảng: `tbl_brand` (Thương hiệu / Phân loại theo danh mục)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `fk_brand_category` (`category_id`),
  CONSTRAINT `fk_brand_category` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_brand` (`brand_id`, `brand_name`, `category_id`) VALUES
(1, 'Áo khoác', 5),
(2, 'Áo polo', 5),
(3, 'Áo sơ mi', 5),
(4, 'Áo thun', 5),
(5, 'Hoodie & Len', 5),
(6, 'Quần jean', 6),
(7, 'Quần kaki', 6),
(8, 'Quần jogger', 6),
(9, 'Quần short', 6),
(10, 'Thắt lưng', 7),
(11, 'Ví & Túi', 7),
(12, 'Nón & Kính', 7),
(13, 'Dép nam', 8);

-- --------------------------------------------------------
-- Bảng: `tbl_product` (Sản phẩm)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `product_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `product_sale` decimal(12,2) NOT NULL DEFAULT 0.00,
  `product_desc` text DEFAULT NULL,
  `product_img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `fk_product_category` (`category_id`),
  KEY `fk_product_brand` (`brand_id`),
  CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `tbl_brand` (`brand_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_product` (`product_id`, `product_name`, `category_id`, `brand_id`, `product_price`, `product_sale`, `product_desc`, `product_img`) VALUES
(1, 'Áo Khoác Bomber Kaki Đen Form Rộng', 5, 1, 450000.00, 380000.00, 'Chất liệu vải kaki 2 lớp dày dặn, lót dù thoáng khí, kiểu dáng trẻ trung năng động.', 'admin/uploads/ao-khoac.jpg'),
(2, 'Áo Khoác Gió Nam Chống Nước Phản Quang', 5, 1, 390000.00, 0.00, 'Áo khoác dù cao cấp trượt nước nhẹ, cản gió giữ ấm tốt trong thời tiết se lạnh.', 'admin/uploads/ao-khoac-1.jpg'),
(3, 'Áo Polo Nam Phối Cổ Basic Thanh Lịch', 5, 2, 280000.00, 220000.00, 'Vải cá sấu cotton co giãn 4 chiều, thấm hút mồ hôi tốt, phù hợp đi học đi làm.', 'admin/uploads/ao-polo.png'),
(4, 'Áo Polo Trơn Regular Fit Tối Giản', 5, 2, 260000.00, 0.00, 'Thiết kế tối giản phong cách Hàn Quốc, form tôn dáng nam tính.', 'admin/uploads/ao-polo-1.png'),
(5, 'Áo Sơ Mi Nam Tay Dài Oxford Trắng', 5, 3, 350000.00, 290000.00, 'Sơ mi chất vải oxford đứng form, ít nhăn, dễ ủi, đường may sắc sảo cao cấp.', 'admin/uploads/ao-so-mi.png'),
(6, 'Áo Sơ Mi Cổ Bẻ Kẻ Sọc Hiện Đại', 5, 3, 340000.00, 0.00, 'Họa tiết kẻ sọc thanh lịch, phong cách thời thượng lịch lãm.', 'admin/uploads/ao-so-mi-1.png'),
(7, 'Áo Thun Nam Cotton 100% In Chữ Graphic', 5, 4, 220000.00, 180000.00, 'Chất cotton 250gsm thoáng mát, mực in lụa sắc nét không bong tróc.', 'admin/uploads/ao-thun.jpg'),
(8, 'Áo Hoodie Nỉ Bông Unisex Túi Trước', 5, 5, 420000.00, 350000.00, 'Nỉ bông lót lông êm ái, mũ trùm 2 lớp chuẩn form streetwear.', 'admin/uploads/hoodie.png'),
(9, 'Áo Len Dệt Kim Cổ Tròn Mùa Đông', 5, 5, 390000.00, 0.00, 'Len dệt mịn màng, không xù lông, giữ ấm vượt trội.', 'admin/uploads/ao-ni-len.png'),
(10, 'Quần Jean Nam Slimfit Co Giãn Rách Gối', 6, 6, 480000.00, 420000.00, 'Denim cotton 12oz bền đẹp, wash màu tự nhiên, co giãn vận động thoải mái.', 'admin/uploads/quan-jean.jpg'),
(11, 'Quần Kaki Nam Ống Đứng Regular Công Sở', 6, 7, 380000.00, 320000.00, 'Kaki thun mềm mịn, tôn dáng chuẩn nam tính, không phai màu.', 'admin/uploads/quankaki.jpg'),
(12, 'Quần Jogger Nam Thể Thao Bo Gấu Khỏe Khoắn', 6, 8, 350000.00, 299000.00, 'Thiết kế thể thao túi hộp cá tính, lưng thun rút dây tiện dụng.', 'admin/uploads/quanjogger2.jpg'),
(13, 'Quần Short Kaki Túi Hộp Mát Mẻ', 6, 9, 240000.00, 199000.00, 'Phù hợp dạo phố, du lịch mùa hè, năng động và thoải mái.', 'admin/uploads/quanshort1.jpg'),
(14, 'Thắt Lưng Nam Da Bò Khóa Tự Động Sang Trọng', 7, 10, 320000.00, 250000.00, 'Da bò dập vân tinh xảo, mặt khóa hợp kim không gỉ sáng bóng.', 'admin/uploads/thatlung.jpg'),
(15, 'Ví Da Nam Cầm Tay Đứng Da Thật', 7, 11, 290000.00, 0.00, 'Nhiều ngăn đựng thẻ và tiền mặt tiện dụng, đường may thủ công tỉ mỉ.', 'admin/uploads/vi.jpg'),
(16, 'Túi Đeo Chéo Canvas Phong Cách Retro', 7, 11, 260000.00, 210000.00, 'Vải canvas chống thấm nhẹ, kích thước gọn nhẹ tiện lợi đựng điện thoại, ví.', 'admin/uploads/tui.jpg'),
(17, 'Nón Lưỡi Trai Classic Đen Thêu Logo', 7, 12, 160000.00, 120000.00, 'Form cứng cáp, khóa gài kim loại điều chỉnh kích cỡ linh hoạt.', 'admin/uploads/non.jpg'),
(18, 'Kính Mát Nam Chống Tia UV Tròng Vuông', 7, 12, 220000.00, 180000.00, 'Tròng kính phân cực Polarized chống chói, bảo vệ mắt tối đa.', 'admin/uploads/kinh.jpg'),
(19, 'Dép Quai Ngang Đúc Nguyên Khối Êm Chân', 8, 13, 190000.00, 150000.00, 'Chất liệu EVA đúc nhẹ êm, rãnh chống trơn trượt hiệu quả.', 'admin/uploads/dep.jpg');

-- --------------------------------------------------------
-- Bảng: `tbl_user` (Tài khoản Khách hàng)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `idx_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mật khẩu mặc định: user123
INSERT INTO `tbl_user` (`user_id`, `fullname`, `email`, `password`, `phone`, `address`) VALUES
(1, 'Nguyễn Văn A', 'khachhang@5themen.vn', '$2y$10$SuX928aeHFguvUANefEO6u3xf6xdbsGrveh/aU2oz.Hhs89y4o/iS', '0901234567', 'Số 123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh');

-- --------------------------------------------------------
-- Bảng: `tbl_order` (Đơn hàng)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `fullname` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `idx_order_user` (`user_id`),
  KEY `idx_order_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_order` (`order_id`, `user_id`, `fullname`, `phone`, `address`, `total_price`, `payment_method`, `status`, `created_at`) VALUES
(1, 1, 'Nguyễn Văn A', '0901234567', 'Số 123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 600000.00, 'cod', 'processing', '2026-11-01 10:15:00'),
(2, 1, 'Nguyễn Văn A', '0901234567', 'Số 123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 350000.00, 'momo', 'completed', '2026-11-03 14:30:00');

-- --------------------------------------------------------
-- Bảng: `tbl_order_detail` (Chi tiết mặt hàng trong đơn)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_order_detail` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 1,
  `size` varchar(10) NOT NULL DEFAULT 'L',
  PRIMARY KEY (`detail_id`),
  KEY `fk_detail_order` (`order_id`),
  KEY `fk_detail_product` (`product_id`),
  CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_product` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_order_detail` (`detail_id`, `order_id`, `product_id`, `price`, `qty`, `size`) VALUES
(1, 1, 1, 380000.00, 1, 'L'),
(2, 1, 3, 220000.00, 1, 'XL'),
(3, 2, 8, 350000.00, 1, 'L');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

