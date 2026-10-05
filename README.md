# Website Bán Đồ Gia Dụng Cao Cấp - GiaDụngPro (Laravel 12)

Dự án website thương mại điện tử chuyên nghiệp cung cấp các sản phẩm thiết bị đồ gia dụng gia đình cao cấp (nồi chiên không dầu, chảo chống dính, máy xay sinh tố, nồi cơm điện, ấm siêu tốc, robot hút bụi, thiết bị lọc nước & không khí) được xây dựng trên nền tảng **Laravel 12**, **Vite** và **Tailwind CSS v4** (sử dụng phông chữ chuẩn **Plus Jakarta Sans**, hỗ trợ chế độ Dark Mode tự động & thủ công) chạy trong môi trường **Laragon** trên hệ điều hành **Windows**.

---

## 1. Thành phần Công nghệ sử dụng
* **Môi trường**: Laragon 6.0+ (PHP 8.3+, MySQL 8.4.3, Node.js v24+, npm 11+).
* **Framework**: Laravel 12.x (Kiến trúc Controller implements `HasMiddleware` chuẩn mới).
* **Frontend**: Blade Templates, Tailwind CSS v4, JavaScript (Vanilla ES6, CSS 3D Transforms).
* **Bundler**: Vite 7.x.
* **Database**: MySQL (quản lý bằng Eloquent ORM, 47 bảng chức năng, đi kèm file sao lưu `giadung_1.sql`).

---

## 2. Hướng dẫn cài đặt trên Laragon (Windows)

Mở Terminal (CMD hoặc PowerShell) tại thư mục dự án: `c:\laragon\www\giadung` (hoặc `c:\laragon\www\giadung_1`)

### Bước 1: Cài đặt các gói phụ thuộc PHP (Composer)
```bash
composer install
```

### Bước 2: Thiết lập file cấu hình môi trường
Sao chép cấu hình mẫu `.env.example` thành `.env` (nếu chưa có):
```bash
copy .env.example .env
```
Cấu hình kết nối MySQL trong `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=giadung
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 3: Tạo mã khóa ứng dụng
```bash
php artisan key:generate
```

### Bước 4: Khởi tạo Cơ sở dữ liệu (Database)
Bạn có thể chọn **1 trong 2 cách** sau:

* **Cách 1 (Khuyên dùng)**: Import trực tiếp file sao lưu **`giadung_1.sql`** có sẵn trong thư mục gốc của dự án vào database `giadung` thông qua **phpMyAdmin** hoặc **HeidiSQL** trên Laragon để có ngay dữ liệu đầy đủ (bao gồm 47 bảng, ảnh, sản phẩm, phân công ca trực, voucher).
* **Cách 2**: Chạy lệnh migration và nạp dữ liệu mẫu bằng Artisan:
```bash
php artisan migrate --seed
```

### Bước 5: Tạo liên kết thư mục lưu trữ ảnh (Storage Link)
```bash
php artisan storage:link
```

### Bước 6: Cài đặt và biên dịch tài nguyên Frontend (CSS/JS)
```bash
npm install
npm.cmd run build
# (hoặc npm run build tùy theo thiết lập PowerShell/CMD)
```

### Bước 7: Khởi chạy dự án
* **Qua Laragon Virtual Host**: Truy cập trực tiếp qua tên miền ảo tự động: **`http://giadung.test`** (hoặc **`http://giadung_1.test`**)
* **Hoặc qua Artisan Serve**:
```bash
php artisan serve
```
*(Truy cập qua: `http://127.0.0.1:8000`)*

---

## 3. Danh sách Tài khoản kiểm thử (Dữ liệu mẫu)

Hệ thống phân quyền 4 cấp độ: **Quản trị viên (Admin)**, **Nhân viên kho (Warehouse)**, **Nhân viên bán hàng (Sales)** và **Khách hàng (Customer)**:

| Vai trò | Tên tài khoản / Username | Email | Mật khẩu | Phạm vi quyền hạn |
| :--- | :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `admin@giadung.com` | `123` | Toàn quyền quản trị hệ thống, nhân sự, phân công ca trực, đơn hàng, kho. |
| **Nhân viên kho (Warehouse)** | `warehouse1` | `warehouse1@giadung.com` | `123` | Quản lý sản phẩm, danh mục, cập nhật số lượng tồn kho. |
| **Nhân viên bán hàng (Sales)** | `sales1` | `sales1@giadung.com` | `123` | Theo dõi và cập nhật trạng thái lịch trực ca bán hàng cá nhân. |
| **Nhân viên bán hàng 2** | `sales2` | `sales2@giadung.com` | `123` | Theo dõi và cập nhật trạng thái ca trực được phân công. |
| **Khách hàng thân thiết** | `user1` | `user1@gmail.com` | `user123` | Mua sắm, tích điểm, quay thưởng, đổi voucher, đánh giá sản phẩm. |
| **Khách hàng 2** | `user2` | `user2@gmail.com` | `user123` | Mua sắm, kết bạn người dùng. |
| **Khách hàng (Bị khóa)** | `user3` | `user3@gmail.com` | `user123` | Kiểm thử tính năng khóa tài khoản vi phạm của Quản trị viên. |

---

## 4. Kiến trúc Cơ sở dữ liệu (47 Bảng)

Hệ thống được thiết kế hoàn chỉnh theo mô hình quan hệ chuẩn:

1. **Người dùng & Hệ thống**: `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.
2. **Sản phẩm & Danh mục**: `categories`, `products`, `product_images`, `brands`, `brand_partnerships`, `colors`, `sizes`, `warranties`, `product_variants`, `product_views`.
3. **Giỏ hàng & Đơn hàng**: `carts`, `cart_items`, `orders`, `order_items`, `payments`.
4. **Khuyến mãi & Voucher**: `coupons`, `vouchers`, `user_vouchers`.
5. **Đánh giá & Quan tâm**: `product_reviews`, `review_votes`, `wishlists`.
6. **Địa giới hành chính Việt Nam**: `provinces`, `districts`, `wards` (hỗ trợ chọn Tỉnh/Thành -> Quận/Huyện -> Phường/Xã liên hoàn).
7. **Tin tức & Thông báo**: `news_categories`, `news`, `notifications`.
8. **Gamification & Tích điểm thưởng**: `games`, `game_rewards`, `game_attempts`, `daily_checkins`, `events`, `reward_items`, `reward_redemptions`, `point_transactions`.
9. **Xã hội & Cá nhân hóa**: `friendships`, `reports`, `user_settings`.
10. **Lịch trực & Bán hàng**: `sales_schedules` (quản lý phân ca trực theo ngày, tháng, ghi chú và trạng thái ca).

---

## 5. Danh sách các Chức năng nổi bật

### A. Giao diện & Trải nghiệm Người dùng (Customer / Guest)
1. **Giao diện Đăng nhập & Đăng ký trượt chéo (Theatrical Diagonal Sliding Auth)**:
   * Khung hợp nhất `Ghost Button Diagonal Sliding Form` với bảng màu Candy Blue kết hợp Onyx.
   * Input phong cách gạch chân tối giản (Line-style Inputs) với icon sang trọng.
   * Hiệu ứng chuyển cảnh quét nghiêng 6 giai đoạn mượt mà, chuyển đổi URL động (`/login` <-> `/register`) không reload trang.
2. **Hero Banner V2 Điện ảnh (Parallax 3D & Hiệu ứng Đẩy tách không gian)**:
   * Ảnh nền căn bếp hiện đại với chiều sâu Parallax 3D bám theo chuyển động chuột.
   * Cụm 3 vật phẩm bay bồng bềnh (Tefal, Philips, Sunhouse) với chuyển động float tự do.
   * Hiệu ứng độc quyền khi rê chuột vào 1 vật phẩm: phóng to nổi bật (`scale-118`), 2 sản phẩm còn lại tự động giãn ra và làm dịu màu.
3. **Thương hiệu Đối tác 3D Coverflow Carousel**:
   * Chuyển động 3D chiều sâu (`transform`, `rotateY`, `scale`, `opacity`) phỏng theo phong cách OriginKit.
   * Tự động điều hướng phím mũi tên, autoplay thông minh dừng khi hover, hỗ trợ `prefers-reduced-motion` và Accessibility A11y.
4. **Lưới Danh mục Sản phẩm phân Tab (Tabbed Grid Categories)**:
   * Phân chia thành 4 nhóm chính: *Thiết Bị Bếp*, *Lọc Nước & Khí*, *Làm Mát & Gia Dụng*, *Dọn Dẹp & Sức Khỏe*. Chuyển tab tức thời với hiệu ứng Scale & Fade.
5. **Navbar trong suốt cuộn mượt (Transparent Sticky Navbar)**:
   * Cố định trong suốt lồng vào Hero Banner tại trang chủ, tự động chuyển nền trắng mờ khi cuộn trang > 20px.
6. **Dark Mode toàn diện**:
   * Bộ chuyển đổi giao diện sáng/tối lưu trữ cookie, áp dụng qua `@custom-variant dark` của Tailwind CSS v4.
7. **Mua sắm & Thanh toán chuẩn Thương mại điện tử**:
   * Xem chi tiết sản phẩm, chọn biến thể (màu sắc, kích thước, gói bảo hành), thư viện ảnh đa góc chụp.
   * Giỏ hàng đồng bộ Session & Database khi đăng nhập.
   * Đặt hàng với form địa chỉ hành chính Việt Nam (Tỉnh -> Huyện -> Xã), kiểm tra tồn kho bằng Database Transaction, hoàn trả tồn kho tự động khi hủy đơn.
   * Hỗ trợ mã giảm giá Coupon và Voucher cá nhân.
8. **Gamification & Tích điểm đổi quà (Loyalty System)**:
   * Điểm danh mỗi ngày (Daily Check-in) nhận điểm theo chuỗi ngày liên tiếp.
   * Vòng quay may mắn (Lucky Wheel) trừ điểm mỗi lượt quay, trúng voucher hoặc điểm thưởng.
   * Đổi quà (Reward Redemption) từ kho quà tặng bằng điểm tích lũy.
9. **Tương tác Xã hội & Đánh giá**:
   * Hệ thống kết bạn (gửi lời mời, chấp nhận, từ chối, chặn người dùng).
   * Đánh giá sao, nhận xét sản phẩm, bình chọn nhận xét hữu ích.
   * Gửi báo cáo vi phạm, trung tâm thông báo người dùng thời gian thực.

10. **Tìm kiếm & Lọc sản phẩm Realtime với Thuật toán Xếp hạng độ liên quan (Smart Live Search & Relevance Ranking)**:
    * **Live Search tức thì**: Tự động gợi ý sản phẩm ngay khi gõ phím trên thanh tìm kiếm (Navbar & Trang Sản Phẩm) với hình ảnh thumbnail, tên, giá gốc, giá ưu đãi và phần trăm giảm giá. Tối ưu bằng Debounce (200ms) và `AbortController` hủy request dôi dư tránh nghẽn mạng.
    * **Lọc sản phẩm Realtime qua AJAX**: Lưới sản phẩm trên trang `/products` tự động cập nhật ngay khi gõ từ khóa hoặc thay đổi bất kỳ bộ lọc nào (Thương hiệu, Xuất xứ, Khoảng giá, Sắp xếp, Tồn kho, Danh mục) mà không cần tải lại trang. Hỗ trợ lớp phủ mờ loading và phân trang AJAX mượt mà.
    * **Thuật toán Relevance Ranking (5 cấp độ)**: Sắp xếp kết quả tìm kiếm thông minh bằng SQL `CASE WHEN`:
      1. Tên sản phẩm bắt đầu bằng từ khóa (`name LIKE 'từ_khóa%'`)
      2. Từ trong tên bắt đầu bằng từ khóa (`name LIKE '% từ_khóa%'`)
      3. Thương hiệu bắt đầu bằng từ khóa (`brand LIKE 'từ_khóa%'`)
      4. Khớp mã SKU sản phẩm
      5. Tên chứa từ khóa ở bất kỳ vị trí nào (`name LIKE '%từ_khóa%'`)
      *(Loại bỏ triệt để hiện tượng gõ "n" lại gợi ý "chảo chống dính" lên đầu).*

---

### B. Khu vực Quản trị (Admin Dashboard - `/admin`)
1. **Bảng điều khiển (Dashboard & Analytics)**:
   * Thống kê tổng quan: Doanh thu thực tế (chỉ tính đơn hoàn thành), tổng đơn hàng, sản phẩm, khách hàng đăng ký.
   * **Biểu đồ doanh thu động (Chart.js)**: Hỗ trợ chuyển đổi nhanh 4 góc nhìn thời gian (**Theo Ngày**, **Theo Tuần**, **Theo Tháng**, **Theo Năm**), tự động vẽ lại biểu đồ đường mượt mà, định dạng tiền tệ VNĐ và cập nhật tổng doanh thu theo kỳ.
   * **Bảng số liệu chi tiết & Cảnh báo tồn kho**: Danh sách đơn hàng gần đây và mục cảnh báo nhanh các sản phẩm sắp hết hàng (`tồn kho <= 5`).
2. **Quản lý danh mục (Categories)**: Thêm, sửa, xóa, phân nhóm tab, bật/tắt trạng thái. Ràng buộc an toàn: không thể xóa danh mục đang chứa sản phẩm.
3. **Quản lý sản phẩm (Products)**: Thêm/sửa sản phẩm, cấu hình biến thể (SKU, giá, tồn kho theo màu & kích thước), quản lý thư viện nhiều ảnh.
4. **Quản lý đơn hàng (Orders)**: Lọc theo trạng thái đơn, tìm kiếm theo người nhận/mã đơn, cập nhật tiến trình đơn hàng và thanh toán. Tự động hoàn tồn kho khi hủy đơn.
5. **Quản lý khách hàng (Customers)**: Tra cứu hồ sơ khách hàng, xem lịch sử mua hàng, khóa/mở khóa tài khoản khách hàng.
6. **Quản lý nhân sự (Employees)**:
   * Tạo tài khoản, chỉnh sửa thông tin và cấp quyền cho nhân viên.
   * Phân quyền rõ ràng theo 2 vị trí: **Nhân viên kho (`warehouse`)** và **Nhân viên bán hàng (`sales`)**.
7. **Lịch trực bán hàng (Sales Schedules)**:
   * Quản trị viên phân công ca trực (Sáng, Chiều, Tối) cho từng nhân viên bán hàng theo ngày.
   * Bộ lọc theo ngày, theo tháng và theo từng nhân viên.
   * Nhân viên bán hàng đăng nhập vào có thể theo dõi ca trực của riêng mình và cập nhật trạng thái hoàn thành ca trực.
8. **Trải nghiệm Chuyển trang mượt mà (Admin SPA Page Transition Engine)**:
   * Cơ chế SPA / Turbo Engine siêu nhẹ độc quyền viết bằng Vanilla JS (`admin-spa.js`), giữ cố định Sidebar và Header không bị giật nháy hay tải lại toàn trang.
   * Thanh tiến trình phát sáng mỏng trên mép đỉnh (Top Loading Progress Bar tương tự YouTube / GitHub).
   * Hiệu ứng chuyển cảnh nội dung Fade & Slide-up êm ái, đồng bộ lịch sử duyệt web `history.pushState` và tương thích phím Back/Forward của trình duyệt qua `popstate`.
   * Quản lý vòng đời biểu đồ Chart.js và script nội tuyến: tự động hủy (`chart.destroy()`) và khởi tạo lại biểu đồ khi chuyển trang, lắng nghe sự kiện custom `admin:page-loaded`.

---

### C. Công cụ & Tiện ích Tự động hóa Nội bộ (Internal Automation Utilities)
1. **Script PowerShell Xử lý Logo Đối tác (`process_logos.ps1`)**:
   * Tự động hóa chuẩn hóa và xử lý ảnh logo thương hiệu đối tác (Tefal, Philips, Sunhouse, Elmich, Lock&Lock...) trong thư mục `storage/app/public/brands`.
   * **Thuật toán quét Bounding Box**: Quét chính xác vùng nội dung theo ngưỡng sáng (Brightness < 220) và kênh Alpha (> 30).
   * **Triệt tiêu File Lock**: Đọc và ghi dữ liệu ảnh thông qua `MemoryStream`, không giữ khóa file trên Windows GDI+.
   * **Khử viền trắng (Anti-aliased white halo un-premultiplication)**: Tự động tách màu nền trắng bị hòa trộn ở mép chữ/hình, giúp logo sắc nét tuyệt đối trên cả nền sáng và Dark Mode.
   * **Tự động căn lề đối xứng (Symmetrical Padding 20px)**: Căn giữa logo và xuất ra file PNG trong suốt chuẩn kích thước 3D Carousel.

---

## 6. Cấu trúc Thư mục Dự án (Project Directory Structure)

```text
giadung/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Xử lý nghiệp vụ chính, Live Search API, Admin SPA
│   │   └── Middleware/           # Kiểm soát phân quyền Admin, Warehouse, Sales
│   ├── Models/                   # 47 Eloquent Models quản lý quan hệ dữ liệu
│   ├── Services/                 # OrderService, CartService, PointService...
│   └── Mail/                     # Mẫu email hóa đơn điện tử & chào mừng thành viên
├── database/
│   ├── migrations/               # Cấu trúc 47 bảng CSDL
│   └── seeders/                  # Dữ liệu mẫu (sản phẩm, tài khoản, ca trực...)
├── public/
│   ├── js/
│   │   ├── admin-spa.js          # Engine điều hướng SPA & hiệu ứng chuyển trang Admin
│   │   └── search-history.js     # Xử lý Live Search Debounce & Lịch sử tìm kiếm
│   └── build/                    # CSS/JS đã được đóng gói qua Vite
├── resources/
│   ├── views/
│   │   ├── admin/                # Giao diện Quản trị viên (Dashboard, Lịch trực, SP...)
│   │   ├── products/             # Giao diện Danh sách SP, Lọc Realtime, Chi tiết SP
│   │   └── layouts/              # Layout chính (app.blade.php, admin.blade.php)
│   └── css/                      # Cấu hình Tailwind CSS v4 và phông chữ
├── routes/
│   └── web.php                   # Định tuyến toàn bộ hệ thống web & API
├── tests/
│   ├── Feature/                  # Bộ kiểm thử chức năng tự động (E2E, Voucher, Auth...)
│   └── Unit/                     # Kiểm thử đơn vị
├── process_logos.ps1             # Tool PowerShell tự động hóa xử lý Logo đối tác
└── giadung_1.sql                 # Bản sao lưu toàn vẹn CSDL (47 bảng)
```

---

## 7. Kết quả Kiểm thử tự động (Automated Tests)

Toàn bộ các quy trình nghiệp vụ từ xác thực, giỏ hàng, đặt hàng, biến thể, hoàn kho, địa giới hành chính, voucher, hoá đơn điện tử, gamification và end-to-end purchasing flow đều vượt qua bộ kiểm thử tự động:

```bash
php artisan test
```

### Kết quả kiểm thử thực tế:
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AdminVoucherTest
  ✓ admin can view vouchers index
  ✓ admin can create new voucher
  ✓ admin can update voucher
  ✓ admin can toggle voucher status
  ✓ admin can delete voucher
  ✓ admin can give voucher to customers
  ✓ free shipping voucher deducts shipping fee not product price
  ✓ order placement with free shipping voucher keeps product price

   PASS  Tests\Feature\AdvancedECommerceTest
  ✓ daily checkin streak calculates points
  ✓ lucky wheel spin deducts points and returns correct response
  ✓ point redemption checks funds and stock
  ✓ friendships workflow
  ✓ merge session cart to db on login

   PASS  Tests\Feature\ECommerceTest
  ✓ register and login
  ✓ guest and customer cannot access admin
  ✓ admin can manage category
  ✓ cannot add more than stock
  ✓ place order success decrements stock and clears cart
  ✓ customer cannot view other users order
  ✓ cancel order restores stock
  ✓ historical price retained when product price changes
  ✓ location cascading data
  ✓ apply coupon success and failures
  ✓ checkout advanced methods

   PASS  Tests\Feature\ElectronicInvoiceMailTest
  ✓ electronic invoice mail renders properly
  ✓ order service sends electronic invoice when requested
  ✓ order service does not send if paper receipt

   PASS  Tests\Feature\FullE2EPurchasingFlowTest
  ✓ complete end to end purchasing and ordering flow

   PASS  Tests\Feature\GoogleAuthTest
  ✓ google redirect route
  ✓ google callback logs in existing user

   PASS  Tests\Feature\WelcomeUserMailTest
  ✓ welcome mail content renders correctly
  ✓ registration sends welcome email
  ✓ single step registration with email sends welcome mail
  ✓ registration without username sends email and generates username

  Tests:    35 passed (169 assertions)
```

---
*Dự án phát triển trên nền tảng Laravel 12 & Laragon.*
