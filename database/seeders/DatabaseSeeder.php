<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Brand;
use App\Models\BrandPartnership;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warranty;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\UserVoucher;
use App\Models\Game;
use App\Models\GameReward;
use App\Models\RewardItem;
use App\Models\NewsCategory;
use App\Models\News;
use App\Models\ProductReview;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Locations from Vietnam Provinces API
        $this->call(LocationSeeder::class);

        // Seed Coupons
        \App\Models\Coupon::create([
            'code' => 'GIADUNG100',
            'type' => 'fixed',
            'value' => 100000,
            'min_order_value' => 500000,
            'max_uses' => 50,
            'uses' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Seed Brands
        $br1 = Brand::create(['name' => 'Philips', 'slug' => 'philips', 'logo' => 'brands/philips.svg', 'is_featured' => true]);
        $br2 = Brand::create(['name' => 'Sunhouse', 'slug' => 'sunhouse', 'logo' => 'brands/sunhouse.png', 'is_featured' => true]);
        $br3 = Brand::create(['name' => 'Tefal', 'slug' => 'tefal', 'logo' => 'brands/tefal.svg', 'is_featured' => true]);
        $br4 = Brand::create(['name' => 'Panasonic', 'slug' => 'panasonic', 'logo' => 'brands/panasonic.svg', 'is_featured' => true]);
        $br5 = Brand::create(['name' => 'Xiaomi', 'slug' => 'xiaomi', 'logo' => 'brands/xiaomi.svg', 'is_featured' => true]);
        $br6 = Brand::create(['name' => 'Cuckoo', 'slug' => 'cuckoo', 'logo' => 'brands/cuckoo.svg', 'is_featured' => true]);

        // Seed Brand Partnerships (About Us page logos)
        BrandPartnership::create(['name' => 'Philips Vietnam', 'logo' => 'brands/philips.svg', 'type' => 'current']);
        BrandPartnership::create(['name' => 'Tefal France', 'logo' => 'brands/tefal.svg', 'type' => 'current']);
        BrandPartnership::create(['name' => 'Sunhouse Group', 'logo' => 'brands/sunhouse.png', 'type' => 'current']);
        BrandPartnership::create(['name' => 'Sanyo Electrics', 'type' => 'past']);

        // Seed Colors
        $cRed = Color::create(['name' => 'Đỏ', 'hex_code' => '#EF4444']);
        $cBlue = Color::create(['name' => 'Xanh dương', 'hex_code' => '#3B82F6']);
        $cBlack = Color::create(['name' => 'Đen huyền bí', 'hex_code' => '#111827']);
        $cSilver = Color::create(['name' => 'Bạc kim loại', 'hex_code' => '#9CA3AF']);

        // Seed Sizes
        $sSmall = Size::create(['name' => 'Nhỏ']);
        $sMedium = Size::create(['name' => 'Vừa']);
        $sLarge = Size::create(['name' => 'Lớn']);
        $s18 = Size::create(['name' => '1.8 Lít']);

        // Seed Vouchers
        $v1 = Voucher::create(['code' => 'WHEEL50', 'name' => 'Vòng quay may mắn 50k', 'type' => 'fixed', 'value' => 50000, 'min_order_value' => 200000, 'max_uses' => 100, 'start_date' => now()->subDay(), 'end_date' => now()->addMonths(2)]);
        $v2 = Voucher::create(['code' => 'WHEEL100', 'name' => 'Vòng quay may mắn 100k', 'type' => 'fixed', 'value' => 100000, 'min_order_value' => 400000, 'max_uses' => 50, 'start_date' => now()->subDay(), 'end_date' => now()->addMonths(2)]);
        $v3 = Voucher::create(['code' => 'FREE10', 'name' => 'Voucher đăng ký mới 10%', 'type' => 'percent', 'value' => 10, 'min_order_value' => 0, 'max_uses' => 1000, 'start_date' => now()->subDay(), 'end_date' => now()->addYear()]);

        // 1. Create Users
        $admin = User::create([
            'name' => 'Quản trị viên',
            'username' => 'admin',
            'email' => 'admin@giadung.com',
            'phone' => '0999999999',
            'password' => Hash::make('123'),
            'address' => 'Hệ thống GiaDụngPro',
            'role' => 'admin',
            'status' => 'active',
            'points' => 1000,
        ]);

        // Seed 3 Staff Members (1 Warehouse, 2 Sales)
        $warehouse1 = User::create([
            'name' => 'Trần Văn Kho',
            'username' => 'warehouse1',
            'email' => 'warehouse1@giadung.com',
            'phone' => '0912345678',
            'password' => Hash::make('123'),
            'address' => 'Kho hàng GiaDụngPro - Tổng kho',
            'role' => 'warehouse',
            'status' => 'active',
        ]);

        $sales1 = User::create([
            'name' => 'Nguyễn Thị Mai',
            'username' => 'sales1',
            'email' => 'sales1@giadung.com',
            'phone' => '0988776655',
            'password' => Hash::make('123'),
            'address' => 'Showroom 1 - GiaDụngPro',
            'role' => 'sales',
            'status' => 'active',
        ]);

        $sales2 = User::create([
            'name' => 'Lê Văn Nam',
            'username' => 'sales2',
            'email' => 'sales2@giadung.com',
            'phone' => '0977889900',
            'password' => Hash::make('123'),
            'address' => 'Showroom 2 - GiaDụngPro',
            'role' => 'sales',
            'status' => 'active',
        ]);

        $user1 = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'user1@gmail.com',
            'phone' => '0987654321',
            'password' => Hash::make('user123'),
            'address' => 'Số 10 Cầu Giấy, Quận Cầu Giấy, Hà Nội',
            'role' => 'customer',
            'status' => 'active',
            'points' => 250,
        ]);

        $user2 = User::create([
            'name' => 'Trần Thị B',
            'email' => 'user2@gmail.com',
            'phone' => '0977654322',
            'password' => Hash::make('user123'),
            'address' => 'Số 20 Trần Hưng Đạo, Quận 1, TP Hồ Chí Minh',
            'role' => 'customer',
            'status' => 'active',
            'points' => 10,
        ]);

        $user3 = User::create([
            'name' => 'Lê Hoàng C',
            'email' => 'user3@gmail.com',
            'phone' => '0967654323',
            'password' => Hash::make('user123'),
            'address' => 'Số 30 Hùng Vương, Quận Hải Châu, Đà Nẵng',
            'role' => 'customer',
            'status' => 'locked',
        ]);

        // User settings
        UserSetting::create(['user_id' => $admin->id, 'theme' => 'light']);
        UserSetting::create(['user_id' => $user1->id, 'theme' => 'light']);
        UserSetting::create(['user_id' => $user2->id, 'theme' => 'dark']);

        // Give claimed vouchers to user1
        UserVoucher::create(['user_id' => $user1->id, 'voucher_id' => $v1->id]);
        UserVoucher::create(['user_id' => $user1->id, 'voucher_id' => $v3->id]);

        // 2. Create Categories
        $cat1 = Category::create(['name' => 'Nồi & Chảo', 'slug' => 'noi-chao', 'description' => 'Các loại nồi, chảo chống dính, nồi lẩu điện đa năng', 'image' => 'categories/sample.png', 'status' => 'active']);
        $cat2 = Category::create(['name' => 'Máy xay sinh tố', 'slug' => 'may-xay-sinh-to', 'description' => 'Máy xay cầm tay, máy xay đa năng chính hãng', 'image' => 'categories/sample.png', 'status' => 'active']);
        $cat3 = Category::create(['name' => 'Nồi cơm điện', 'slug' => 'noi-com-dien', 'description' => 'Nồi cơm điện cao tần, nồi cơm điện tử chống dính', 'image' => 'categories/sample.png', 'status' => 'active']);
        $cat4 = Category::create(['name' => 'Ấm siêu tốc', 'slug' => 'am-sieu-toc', 'description' => 'Ấm đun nước siêu tốc inox, thủy tinh cao cấp', 'image' => 'categories/sample.png', 'status' => 'active']);
        $cat5 = Category::create(['name' => 'Thiết bị vệ sinh', 'slug' => 'thiet-bi-ve-sinh', 'description' => 'Máy hút bụi, máy lọc không khí và robot dọn nhà', 'image' => 'categories/sample.png', 'status' => 'active']);
        $cat6 = Category::create(['name' => 'Máy lọc nước', 'slug' => 'may-loc-nuoc', 'description' => 'Máy lọc nước RO, máy lọc nước nóng lạnh cao cấp', 'image' => 'categories/sample.png', 'status' => 'active']);

        // 3. Create Products
        $productsData = [
            // Category 1: Nồi & Chảo
            [
                'category_id' => $cat1->id, 'brand_id' => $br2->id, 'code' => 'NC-01', 'name' => 'Nồi lẩu điện đa năng Sunhouse SHD4527', 'slug' => 'noi-lau-dien-sunhouse-shd4527',
                'brand' => 'Sunhouse', 'short_description' => 'Công suất lớn 1600W, dung tích 4 lít tiện lợi.', 'description' => 'Nồi lẩu điện đa năng Sunhouse SHD4527 sở hữu lòng nồi chống dính cao cấp, núm xoay điều chỉnh nhiệt độ dễ dàng. Phù hợp cho bữa tiệc gia đình ấm cúng.',
                'price' => 850000, 'sale_price' => 720000, 'stock_quantity' => 15, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/sunhouse_shd4527.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => true
            ],
            [
                'category_id' => $cat1->id, 'brand_id' => $br1->id, 'code' => 'NC-02', 'name' => 'Nồi chiên không dầu Philips HD9200', 'slug' => 'noi-chien-khong-dau-philips-hd9200',
                'brand' => 'Philips', 'short_description' => 'Công nghệ Rapid Air giảm đến 90% lượng mỡ thừa.', 'description' => 'Thiết kế sang trọng, lòng nồi dung tích 4.1 lít thích hợp cho gia đình 3-4 người. Điều chỉnh cơ học bền bỉ, dễ tháo rời vệ sinh bằng máy rửa bát.',
                'price' => 2500000, 'sale_price' => 1950000, 'stock_quantity' => 8, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/philips_hd9200.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],
            [
                'category_id' => $cat1->id, 'brand_id' => $br3->id, 'code' => 'NC-03', 'name' => 'Chảo chống dính Tefal Natura 24cm', 'slug' => 'chao-chong-dinh-tefal-natura-24cm',
                'brand' => 'Tefal', 'short_description' => 'Lớp chống dính phủ khoáng tự nhiên siêu bền.', 'description' => 'Chảo chống dính Tefal Natura làm từ nhôm đúc nguyên khối, truyền nhiệt nhanh. Điểm báo nhiệt Thermo-spot thông minh báo thời điểm chảo đủ nóng để chiên xào.',
                'price' => 420000, 'sale_price' => 350000, 'stock_quantity' => 30, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/tefal_natura.png', 'status' => 'active', 'is_featured' => false, 'is_domestic' => false
            ],
            [
                'category_id' => $cat1->id, 'brand_id' => $br3->id, 'code' => 'NC-04', 'name' => 'Nồi chiên không dầu kết hợp hấp Tefal Ultimate FW5018', 'slug' => 'noi-chien-khong-dau-tefal-ultimate-fw5018',
                'brand' => 'Tefal', 'short_description' => 'Công nghệ 3-in-1: Chiên không dầu, Nướng và Hấp điện tử.', 'description' => 'Nồi chiên hấp cao cấp Tefal FW5018 sở hữu dung tích khủng 6.5L, 7 chương trình nấu tự động. Tích hợp khay hấp inox 304 cao cấp giữ trọn dưỡng chất thực phẩm.',
                'price' => 3800000, 'sale_price' => 3190000, 'stock_quantity' => 15, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/tefal_fw5018.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],

            // Category 2: Máy xay sinh tố
            [
                'category_id' => $cat2->id, 'brand_id' => $br1->id, 'code' => 'MX-01', 'name' => 'Máy xay sinh tố Philips HR2221', 'slug' => 'may-xay-sinh-to-philips-hr2221',
                'brand' => 'Philips', 'short_description' => 'Công suất 700W mạnh mẽ, 5 tốc độ xay tiện dụng.', 'description' => 'Lưỡi dao 4 cánh bằng thép không gỉ sắc bén kết hợp công nghệ ProBlend Crush xay đá nhanh gấp 2 lần. Cối nhựa kháng vỡ dung tích lớn 1.5 lít.',
                'price' => 1655000, 'sale_price' => 1290000, 'stock_quantity' => 12, 'unit' => 'bộ', 'thumbnail' => 'products/thumbnails/philips_hr2221.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],
            [
                'category_id' => $cat2->id, 'brand_id' => $br4->id, 'code' => 'MX-02', 'name' => 'Máy xay cầm tay đa năng Braun MultiQuick 9', 'slug' => 'may-xay-cam-tay-braun-multiquick-9',
                'brand' => 'Panasonic', 'short_description' => 'Công suất 1200W công nghệ lưỡi dao di chuyển ACTIVEBlade.', 'description' => 'Máy xay cầm tay Braun MQ9 với công nghệ đòn bẩy thông minh SmartSpeed, dễ dàng điều khiển tốc độ bằng lực nhấn. Kèm cối xay thịt và cây đánh trứng cao cấp.',
                'price' => 2100000, 'sale_price' => 1750000, 'stock_quantity' => 20, 'unit' => 'bộ', 'thumbnail' => 'products/thumbnails/braun_mq9.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],
            
            // Category 3: Nồi cơm điện
            [
                'category_id' => $cat3->id, 'brand_id' => $br6->id, 'code' => 'NCD-03', 'name' => 'Nồi cơm điện cao tần Cuckoo CRP-L1055F', 'slug' => 'noi-com-cao-tan-cuckoo-crp-l1055f',
                'brand' => 'Cuckoo', 'short_description' => 'Nấu áp suất cao tần IH chín hạt gạo từ trong lõi.', 'description' => 'Dòng sản phẩm cao cấp từ Hàn Quốc. Có chức năng tự động làm sạch bằng hơi nước, màn hình hiển thị LCD hiện đại, lòng nồi phủ men X-Wall Black Shine độc quyền.',
                'price' => 5500000, 'sale_price' => 4800000, 'stock_quantity' => 3, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/sample.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],
            [
                'category_id' => $cat3->id, 'brand_id' => $br4->id, 'code' => 'NCD-04', 'name' => 'Nồi cơm điện cao tần Toshiba RC-18IX1PV', 'slug' => 'noi-com-dien-cao-tan-toshiba-rc-18ix1pv',
                'brand' => 'Panasonic', 'short_description' => 'Công nghệ nấu cao tần IH 1300W, lòng nồi hợp kim nhôm đúc 3mm.', 'description' => 'Nồi cơm điện cao tần Toshiba dung tích 1.8L với thiết kế mặt kính đen bóng sang trọng. Lòng nồi phủ chống dính Binchotan Nhật Bản siêu bền và an toàn.',
                'price' => 3200000, 'sale_price' => 2650000, 'stock_quantity' => 12, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/toshiba_rc18.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],

            // Category 4: Ấm siêu tốc
            [
                'category_id' => $cat4->id, 'brand_id' => $br1->id, 'code' => 'AST-01', 'name' => 'Ấm siêu tốc Philips HD9303', 'slug' => 'am-sieu-toc-philips-hd9303',
                'brand' => 'Philips', 'short_description' => 'Dung tích 1.2 lít, thân ấm làm bằng inox 304 an toàn.', 'description' => 'Đế xoay 360 độ tiện dụng, rơ-le cảm biến Strix cao cấp tự động ngắt khi nước sôi hoặc cạn nước. Đèn LED chỉ báo trạng thái hoạt động dễ quan sát.',
                'price' => 680000, 'sale_price' => 550000, 'stock_quantity' => 22, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/philips_hd9303.png', 'status' => 'active', 'is_featured' => false, 'is_domestic' => false
            ],
            [
                'category_id' => $cat4->id, 'brand_id' => $br2->id, 'code' => 'AST-02', 'name' => 'Ấm đun nước thủy tinh chịu nhiệt Sunhouse SHD1353', 'slug' => 'am-dun-nuoc-thuy-tinh-sunhouse-shd1353',
                'brand' => 'Sunhouse', 'short_description' => 'Thân thủy tinh chịu nhiệt Borosilicate kèm dải đèn LED xanh huyền ảo.', 'description' => 'Ấm siêu tốc thủy tinh Sunhouse SHD1353 dung tích 1.8L, công suất 1800W đun sôi cực nhanh. Lưới lọc cặn vi mô ở vòi rót giúp nguồn nước luôn tinh khiết.',
                'price' => 590000, 'sale_price' => 450000, 'stock_quantity' => 25, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/sunhouse_glass_kettle.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => true
            ],

            // Category 5: Thiết bị vệ sinh
            [
                'category_id' => $cat5->id, 'brand_id' => $br5->id, 'code' => 'VS-02', 'name' => 'Robot hút bụi lau nhà Roborock Q7 Max', 'slug' => 'robot-hut-bui-roborock-q7-max',
                'brand' => 'Xiaomi', 'short_description' => 'Lidar PreciSense lập bản đồ 3D, lực hút 4200Pa.', 'description' => 'Hệ thống định vị LiDAR hiện đại tự động tránh chướng ngại vật. Thiết lập tường ảo qua app điện thoại thông minh, vừa hút bụi vừa lau nhà cùng lúc.',
                'price' => 11500000, 'sale_price' => 9990000, 'stock_quantity' => 5, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/sample.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],
            [
                'category_id' => $cat5->id, 'brand_id' => $br5->id, 'code' => 'VS-03', 'name' => 'Máy lọc không khí Xiaomi Smart Air Purifier 4 Pro', 'slug' => 'may-loc-khong-khi-xiaomi-air-purifier-4-pro',
                'brand' => 'Xiaomi', 'short_description' => 'Bộ lọc HEPA 3in1 lọc 99.97% bụi mịn PM2.5 và vi khuẩn.', 'description' => 'Máy lọc không khí Xiaomi Air Purifier 4 Pro phù hợp diện tích phòng đến 60m2. Màn hình OLED hiển thị chỉ số AQI thời gian thực, điều khiển giọng nói qua Google Assistant.',
                'price' => 4500000, 'sale_price' => 3890000, 'stock_quantity' => 10, 'unit' => 'cái', 'thumbnail' => 'products/thumbnails/xiaomi_air_purifier.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => false
            ],

            // Category 6: Máy lọc nước
            [
                'category_id' => $cat6->id, 'brand_id' => $br2->id, 'code' => 'LN-01', 'name' => 'Máy lọc nước RO nóng lạnh Sunhouse SHA76213CK', 'slug' => 'may-loc-nuoc-ro-nong-lanh-sunhouse-sha76213ck',
                'brand' => 'Sunhouse', 'short_description' => '10 lõi lọc RO thế hệ mới, vòi kép Nóng - Lạnh - Nguội tiện lợi.', 'description' => 'Máy lọc nước Sunhouse SHA76213CK sở hữu thiết kế tủ kính tràn viền 3D sang trọng. Màng lọc RO nhập khẩu USA loại bỏ 99.99% kim loại nặng và vi khuẩn.',
                'price' => 7900000, 'sale_price' => 6490000, 'stock_quantity' => 8, 'unit' => 'tủ', 'thumbnail' => 'products/thumbnails/sunhouse_water_purifier.png', 'status' => 'active', 'is_featured' => true, 'is_domestic' => true
            ]
        ];

        foreach ($productsData as $prodData) {
            $product = Product::create($prodData);

            // Add Product Images
            ProductImage::create(['product_id' => $product->id, 'image_path' => 'products/gallery/sample_detail.png', 'sort_order' => 1]);

            // Add Warranties for this product
            Warranty::create(['product_id' => $product->id, 'name' => 'Bảo hành vàng (1 đổi 1)', 'duration_months' => 12, 'price' => 150000]);
            Warranty::create(['product_id' => $product->id, 'name' => 'Bảo hành kim cương (24 tháng)', 'duration_months' => 24, 'price' => 300000]);

            // Add color/size variants to "Nồi lẩu" and "Nồi cơm"
            if (in_array($product->code, ['NC-01', 'NCD-03'])) {
                // Variant 1
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $cRed->id,
                    'size_id' => $sMedium->id,
                    'sku' => $product->code . '-RED-MED',
                    'price' => $product->price + 50000,
                    'sale_price' => $product->sale_price ? $product->sale_price + 50000 : null,
                    'stock_quantity' => 5,
                    'thumbnail' => 'products/thumbnails/sample.png',
                ]);
                // Variant 2
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $cBlack->id,
                    'size_id' => $sLarge->id,
                    'sku' => $product->code . '-BLK-LRG',
                    'price' => $product->price + 100000,
                    'sale_price' => $product->sale_price ? $product->sale_price + 100000 : null,
                    'stock_quantity' => 10,
                    'thumbnail' => 'products/thumbnails/sample.png',
                ]);
            }
        }

        // 5. Create Sample Orders
        // Order 1: Completed order for User 1
        $order1 = Order::create([
            'user_id' => $user1->id,
            'order_code' => 'GD-' . date('Ymd') . '-LUCKY9',
            'receiver_name' => 'Nguyễn Văn A',
            'receiver_phone' => '0987654321',
            'receiver_email' => 'user1@gmail.com',
            'province' => 'Hà Nội',
            'province_code' => '01',
            'district' => 'Quận Cầu Giấy',
            'district_code' => '005',
            'ward' => 'Phường Dịch Vọng',
            'ward_code' => '00151',
            'address' => 'Số 10 Cầu Giấy',
            'note' => 'Giao hàng giờ hành chính',
            'subtotal' => 900000,
            'shipping_fee' => 0,
            'total_amount' => 900000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'est_delivery_start' => now()->addDays(1)->toDateString(),
            'est_delivery_end' => now()->addDays(2)->toDateString(),
            'created_at' => now()->subMonth(), // To build monthly stats
        ]);

        $prod_NC03 = Product::where('code', 'NC-03')->first();
        $prod_AST01 = Product::where('code', 'AST-01')->first();

        OrderItem::create([
            'order_id' => $order1->id, 'product_id' => $prod_NC03->id, 'product_code' => $prod_NC03->code, 'product_name' => $prod_NC03->name, 'product_image' => $prod_NC03->thumbnail, 'price' => 350000, 'quantity' => 1, 'total' => 350000
        ]);
        OrderItem::create([
            'order_id' => $order1->id, 'product_id' => $prod_AST01->id, 'product_code' => $prod_AST01->code, 'product_name' => $prod_AST01->name, 'product_image' => $prod_AST01->thumbnail, 'price' => 550000, 'quantity' => 1, 'total' => 550000
        ]);

        Payment::create(['order_id' => $order1->id, 'payment_code' => 'PAY-COMPLETED123', 'payment_method' => 'cod', 'amount' => 900000, 'status' => 'paid', 'paid_at' => now()->subMonth()]);

        // Seed Product Reviews
        ProductReview::create([
            'user_id' => $user1->id,
            'product_id' => $prod_NC03->id,
            'order_id' => $order1->id,
            'rating' => 5,
            'comment' => 'Chảo chống dính dùng cực kỳ thích, chiên không cần mỡ luôn.',
            'status' => 'approved',
            'is_featured' => true
        ]);
        ProductReview::create([
            'user_id' => $user1->id,
            'product_id' => $prod_AST01->id,
            'order_id' => $order1->id,
            'rating' => 4,
            'comment' => 'Ấm siêu tốc sôi nhanh, chất liệu inox dầy dặn, rất hài lòng.',
            'status' => 'approved',
            'is_featured' => true
        ]);

        // Seed News
        $newsCat = NewsCategory::create(['name' => 'Sản phẩm mới', 'slug' => 'san-pham-moi']);
        NewsCategory::create(['name' => 'Khuyến mãi hot', 'slug' => 'khuyen-mai-hot']);

        News::create([
            'category_id' => $newsCat->id,
            'title' => 'GiaDụngPro ra mắt dòng robot hút bụi Roborock Q7 Max thông minh',
            'slug' => 'ra-mat-roborock-q7-max',
            'summary' => 'Dòng robot hút bụi và lau nhà cao cấp thế hệ mới tích hợp bản đồ 3D thông minh.',
            'content' => 'Roborock Q7 Max mang đến lực hút cực mạnh 4200Pa cùng khay chứa bụi bẩn dung tích lớn. Sản phẩm được phân phối chính hãng toàn quốc với chính sách bảo hành vàng lên tới 12 tháng tại GiaDụngPro.',
            'type' => 'new_release',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'related_product_ids' => [Product::where('code', 'VS-02')->value('id')],
        ]);

        // Seed Games Config
        $g1 = Game::create([
            'name' => 'Vòng quay may mắn',
            'slug' => 'vong-quay-may-man',
            'description' => 'Quay thưởng mỗi ngày nhận ngay voucher cực chất.',
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonths(6),
            'status' => 'active',
            'type' => 'lucky_wheel',
        ]);

        $g2 = Game::create([
            'name' => 'Game dự đoán chẵn lẻ',
            'slug' => 'doan-chan-le',
            'description' => 'Chọn chẵn hoặc lẻ, thắng nhân đôi điểm thưởng.',
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonths(6),
            'status' => 'active',
            'type' => 'even_odd',
        ]);

        // Seed Lucky Wheel Rewards
        GameReward::create(['game_id' => $g1->id, 'name' => 'Chúc bạn may mắn lần sau', 'type' => 'none', 'value' => null, 'probability' => 40.0]);
        GameReward::create(['game_id' => $g1->id, 'name' => 'Cộng 10 điểm thưởng', 'type' => 'points', 'value' => '10', 'probability' => 30.0]);
        GameReward::create(['game_id' => $g1->id, 'name' => 'Cộng 50 điểm thưởng', 'type' => 'points', 'value' => '50', 'probability' => 15.0]);
        GameReward::create(['game_id' => $g1->id, 'name' => 'Voucher 50K (Mã WHEEL50)', 'type' => 'voucher', 'value' => 'WHEEL50', 'probability' => 10.0]);
        GameReward::create(['game_id' => $g1->id, 'name' => 'Voucher 100K (Mã WHEEL100)', 'type' => 'voucher', 'value' => 'WHEEL100', 'probability' => 5.0]);

        // Seed Reward shop items
        RewardItem::create(['name' => 'WHEEL50 - Voucher 50k', 'description' => 'Đổi voucher 50k cho đơn hàng từ 200k', 'points_required' => 100, 'stock_quantity' => 99, 'start_date' => now()->subDay(), 'end_date' => now()->addMonths(2)]);
        RewardItem::create(['name' => 'WHEEL100 - Voucher 100k', 'description' => 'Đổi voucher 100k cho đơn hàng từ 400k', 'points_required' => 200, 'stock_quantity' => 50, 'start_date' => now()->subDay(), 'end_date' => now()->addMonths(2)]);

        // Seed Sales Schedules for sales staff members
        if (isset($sales1) && isset($sales2) && isset($admin)) {
            \App\Models\SalesSchedule::create([
                'user_id' => $sales1->id,
                'work_date' => now()->toDateString(),
                'shift_name' => 'Ca Sáng (08:00 - 12:00)',
                'notes' => 'Trực quầy Nồi cơm điện & Thiết bị nhà bếp',
                'status' => 'assigned',
                'created_by' => $admin->id,
            ]);
            \App\Models\SalesSchedule::create([
                'user_id' => $sales2->id,
                'work_date' => now()->toDateString(),
                'shift_name' => 'Ca Chiều (13:00 - 17:00)',
                'notes' => 'Trực quầy Ấm siêu tốc & Máy lọc nước',
                'status' => 'assigned',
                'created_by' => $admin->id,
            ]);
            \App\Models\SalesSchedule::create([
                'user_id' => $sales1->id,
                'work_date' => now()->addDay()->toDateString(),
                'shift_name' => 'Cả Ngày (08:00 - 17:00)',
                'notes' => 'Tư vấn trực tiếp & Hỗ trợ hotline bán hàng',
                'status' => 'assigned',
                'created_by' => $admin->id,
            ]);
            \App\Models\SalesSchedule::create([
                'user_id' => $sales2->id,
                'work_date' => now()->addDays(2)->toDateString(),
                'shift_name' => 'Ca Tối (17:00 - 21:00)',
                'notes' => 'Trực ca tối showroom chính',
                'status' => 'assigned',
                'created_by' => $admin->id,
            ]);
        }
    }
}
