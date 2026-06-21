<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. CATEGORIES ───────────────────────────────────────────────
        $categories = [
            ['id' => 1,  'name' => 'Makanan & Minuman'],
            ['id' => 2,  'name' => 'Fashion'],
            ['id' => 3,  'name' => 'Kecantikan & Perawatan'],
            ['id' => 4,  'name' => 'Elektronik'],
            ['id' => 5,  'name' => 'Kesehatan'],
            ['id' => 6,  'name' => 'Pendidikan'],
            ['id' => 7,  'name' => 'Otomotif'],
            ['id' => 8,  'name' => 'Hiburan & Wisata'],
            ['id' => 9,  'name' => 'Properti & Rumah'],
            ['id' => 10, 'name' => 'Jasa & Layanan'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['id' => $cat['id']],
                [
                    'name'       => $cat['name'],
                    'slug'       => Str::slug($cat['name']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ─── 2. USERS (role: seller) ─────────────────────────────────────
        $sellers = [
            ['name' => 'Warung Mak Siti',          'email' => 'maksiti@promora.test',       'phone' => '081234560001', 'location' => 'Pekanbaru', 'business_name' => 'Warung Mak Siti',          'business_category' => 'Makanan & Minuman',    'address' => 'Jl. Sudirman No. 12, Pekanbaru, Riau',               'lat' => 0.5335,   'lng' => 101.4503],
            ['name' => 'Kedai Kopi Rempah',         'email' => 'kopirempah@promora.test',    'phone' => '081234560002', 'location' => 'Pekanbaru', 'business_name' => 'Kedai Kopi Rempah',        'business_category' => 'Makanan & Minuman',    'address' => 'Jl. Imam Bonjol No. 5, Pekanbaru, Riau',             'lat' => 0.5071,   'lng' => 101.4478],
            ['name' => 'Butik Cantika',             'email' => 'cantika@promora.test',       'phone' => '081234560003', 'location' => 'Medan',     'business_name' => 'Butik Cantika',            'business_category' => 'Fashion',              'address' => 'Jl. Tuanku Tambusai No. 88, Medan, Sumatera Utara',  'lat' => 3.5952,   'lng' => 98.6722],
            ['name' => 'Toko Mode Elegan',          'email' => 'modeelegan@promora.test',    'phone' => '081234560004', 'location' => 'Jakarta',   'business_name' => 'Toko Mode Elegan',         'business_category' => 'Fashion',              'address' => 'Mall Grand Indonesia Lt. 3, Jakarta Pusat',          'lat' => -6.1954,  'lng' => 106.8200],
            ['name' => 'Salon Ayu Lestari',         'email' => 'ayulestari@promora.test',    'phone' => '081234560005', 'location' => 'Bandung',   'business_name' => 'Salon Ayu Lestari',        'business_category' => 'Kecantikan & Perawatan','address' => 'Jl. Riau No. 44, Bandung, Jawa Barat',              'lat' => -6.9175,  'lng' => 107.6191],
            ['name' => 'Beauty Studio Shafa',       'email' => 'shafa@promora.test',         'phone' => '081234560006', 'location' => 'Surabaya',  'business_name' => 'Beauty Studio Shafa',      'business_category' => 'Kecantikan & Perawatan','address' => 'Jl. Darmo No. 21, Surabaya, Jawa Timur',            'lat' => -7.2575,  'lng' => 112.7521],
            ['name' => 'TeknoMart',                 'email' => 'teknomart@promora.test',     'phone' => '081234560007', 'location' => 'Jakarta',   'business_name' => 'TeknoMart',                'business_category' => 'Elektronik',           'address' => 'ITC Roxy Mas Lt. 2 Blok C11, Jakarta Pusat',        'lat' => -6.1659,  'lng' => 106.8043],
            ['name' => 'GadgetZone Pekanbaru',      'email' => 'gadgetzone@promora.test',    'phone' => '081234560008', 'location' => 'Pekanbaru', 'business_name' => 'GadgetZone Pekanbaru',     'business_category' => 'Elektronik',           'address' => 'Jl. Sudirman No. 7, Pekanbaru, Riau',               'lat' => 0.5320,   'lng' => 101.4475],
            ['name' => 'Apotek Sehat Bersama',      'email' => 'sehatbersama@promora.test',  'phone' => '081234560009', 'location' => 'Pekanbaru', 'business_name' => 'Apotek Sehat Bersama',     'business_category' => 'Kesehatan',            'address' => 'Jl. Hang Tuah No. 33, Pekanbaru, Riau',             'lat' => 0.5280,   'lng' => 101.4530],
            ['name' => 'Klinik Herbal Nusantara',   'email' => 'herbalnusa@promora.test',    'phone' => '081234560010', 'location' => 'Yogyakarta','business_name' => 'Klinik Herbal Nusantara',  'business_category' => 'Kesehatan',            'address' => 'Jl. Malioboro No. 10, Yogyakarta',                  'lat' => -7.7928,  'lng' => 110.3660],
            ['name' => 'Bimbel Cerdas Indonesia',   'email' => 'bimbelcerdas@promora.test',  'phone' => '081234560011', 'location' => 'Jakarta',   'business_name' => 'Bimbel Cerdas Indonesia',  'business_category' => 'Pendidikan',           'address' => 'Jl. Kebon Jeruk No. 15, Jakarta Barat',             'lat' => -6.1944,  'lng' => 106.7796],
            ['name' => 'Kursus Bahasa Inggris EF',  'email' => 'kursusef@promora.test',      'phone' => '081234560012', 'location' => 'Bandung',   'business_name' => 'Kursus Bahasa Inggris EF', 'business_category' => 'Pendidikan',           'address' => 'Jl. Asia Afrika No. 8, Bandung, Jawa Barat',        'lat' => -6.9218,  'lng' => 107.6076],
            ['name' => 'Bengkel Maju Jaya',         'email' => 'majujaya@promora.test',      'phone' => '081234560013', 'location' => 'Pekanbaru', 'business_name' => 'Bengkel Maju Jaya',        'business_category' => 'Otomotif',             'address' => 'Jl. Soekarno Hatta No. 200, Pekanbaru, Riau',       'lat' => 0.4811,   'lng' => 101.3869],
            ['name' => 'Auto Detailing Pro',        'email' => 'autodetailing@promora.test', 'phone' => '081234560014', 'location' => 'Medan',     'business_name' => 'Auto Detailing Pro',       'business_category' => 'Otomotif',             'address' => 'Jl. Gatot Subroto No. 45, Medan, Sumatera Utara',   'lat' => 3.5870,   'lng' => 98.6740],
            ['name' => 'Waterboom Fantasi',         'email' => 'waterboom@promora.test',     'phone' => '081234560015', 'location' => 'Pekanbaru', 'business_name' => 'Waterboom Fantasi',        'business_category' => 'Hiburan & Wisata',     'address' => 'Jl. Raya Pekanbaru-Bangkinang KM 12, Pekanbaru',    'lat' => 0.5700,   'lng' => 101.4200],
            ['name' => 'Bioskop CineStar',          'email' => 'cinestar@promora.test',      'phone' => '081234560016', 'location' => 'Surabaya',  'business_name' => 'Bioskop CineStar',         'business_category' => 'Hiburan & Wisata',     'address' => 'Tunjungan Plaza Lt. 5, Surabaya, Jawa Timur',       'lat' => -7.2616,  'lng' => 112.7378],
            ['name' => 'Toko Bangunan Kokoh',       'email' => 'bangkokoh@promora.test',     'phone' => '081234560017', 'location' => 'Pekanbaru', 'business_name' => 'Toko Bangunan Kokoh',      'business_category' => 'Properti & Rumah',     'address' => 'Jl. Nangka No. 99, Pekanbaru, Riau',                'lat' => 0.5120,   'lng' => 101.4620],
            ['name' => 'Furniture Jati Indah',      'email' => 'jatiindah@promora.test',     'phone' => '081234560018', 'location' => 'Jepara',    'business_name' => 'Furniture Jati Indah',     'business_category' => 'Properti & Rumah',     'address' => 'Jl. Raya Jepara-Kudus KM 5, Jepara, Jawa Tengah',  'lat' => -6.5891,  'lng' => 110.6688],
            ['name' => 'Laundry Kilat Express',     'email' => 'laundrykilat@promora.test',  'phone' => '081234560019', 'location' => 'Pekanbaru', 'business_name' => 'Laundry Kilat Express',    'business_category' => 'Jasa & Layanan',       'address' => 'Jl. Cempaka No. 18, Pekanbaru, Riau',               'lat' => 0.5401,   'lng' => 101.4510],
            ['name' => 'Jasa Foto & Video Pro',     'email' => 'fotovideo@promora.test',     'phone' => '081234560020', 'location' => 'Jakarta',   'business_name' => 'Jasa Foto & Video Pro',    'business_category' => 'Jasa & Layanan',       'address' => 'Jl. Kemang Raya No. 5, Jakarta Selatan',            'lat' => -6.2607,  'lng' => 106.8137],
        ];

        $sellerProfileIds = [];

        foreach ($sellers as $index => $s) {
            // Insert user
            $userId = DB::table('users')->insertGetId([
                'name'              => $s['name'],
                'email'             => $s['email'],
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'seller',
                'phone'             => $s['phone'],
                'location'          => $s['location'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Insert seller_profile
            $profileId = DB::table('seller_profiles')->insertGetId([
                'user_id'           => $userId,
                'business_name'     => $s['business_name'],
                'business_category' => $s['business_category'],
                'description'       => 'Toko ' . $s['business_name'] . ' menawarkan produk dan layanan terbaik untuk pelanggan setia kami.',
                'address'           => $s['address'],
                'latitude'          => $s['lat'],
                'longitude'         => $s['lng'],
                'is_verified'       => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $sellerProfileIds[$index + 1] = $profileId;
        }

        // ─── 3. PROMOS ───────────────────────────────────────────────────
        $promos = [
            // Makanan & Minuman
            [
                'title'               => 'Diskon 30% Nasi Goreng Spesial',
                'description'         => 'Nikmati nasi goreng ayam kampung dengan bumbu rempah khas Melayu. Diskon 30% untuk pembelian di atas Rp 50.000. Cocok untuk makan siang bersama keluarga!',
                'poster_image'        => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800',
                'discount_percentage' => 30.00,
                'original_price'      => 35000,
                'promo_price'         => 24500,
                'category_id'         => 1,
                'seller_id'           => 1,
                'start_date'          => Carbon::now()->subDays(2)->toDateString(),
                'end_date'            => Carbon::now()->addDays(10)->toDateString(),
                'view_count'          => 320,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Buy 1 Get 1 Kopi Susu Aren',
                'description'         => 'Promo spesial Buy 1 Get 1 untuk semua varian kopi susu aren kami. Kopi arabika pilihan dengan gula aren asli dari Riau. Berlaku setiap hari pukul 07.00-12.00.',
                'poster_image'        => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600',
                'discount_percentage' => 50.00,
                'original_price'      => 28000,
                'promo_price'         => 14000,
                'category_id'         => 1,
                'seller_id'           => 2,
                'start_date'          => Carbon::now()->subDays(1)->toDateString(),
                'end_date'            => Carbon::now()->addDays(7)->toDateString(),
                'view_count'          => 512,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            // Fashion
            [
                'title'               => 'Sale Baju Batik Riau 50% Off',
                'description'         => 'Koleksi batik motif Melayu Riau dengan kualitas premium. Diskon hingga 50% untuk semua model baju batik wanita. Stok terbatas, segera kunjungi toko kami!',
                'poster_image'        => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
                'discount_percentage' => 50.00,
                'original_price'      => 250000,
                'promo_price'         => 125000,
                'category_id'         => 2,
                'seller_id'           => 3,
                'start_date'          => Carbon::now()->subDays(3)->toDateString(),
                'end_date'            => Carbon::now()->addDays(5)->toDateString(),
                'view_count'          => 780,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            [
                'title'               => 'Promo Dress Kondangan Mulai 99rb',
                'description'         => 'Tampil memesona di setiap acara dengan koleksi dress kondangan kami. Pilihan warna dan model beragam, bahan premium, harga mulai dari Rp 99.000 saja!',
                'poster_image'        => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600',
                'discount_percentage' => 71.71,
                'original_price'      => 350000,
                'promo_price'         => 99000,
                'category_id'         => 2,
                'seller_id'           => 4,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(14)->toDateString(),
                'view_count'          => 1200,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            // Kecantikan & Perawatan
            [
                'title'               => 'Paket Creambath + Hairspa Rp 75rb',
                'description'         => 'Manjakan rambut Anda dengan paket creambath dan hair spa menggunakan bahan alami. Termasuk pijat kepala gratis! Cocok untuk relaksasi akhir pekan.',
                'poster_image'        => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=600',
                'discount_percentage' => 40.00,
                'original_price'      => 125000,
                'promo_price'         => 75000,
                'category_id'         => 3,
                'seller_id'           => 5,
                'start_date'          => Carbon::now()->subDays(1)->toDateString(),
                'end_date'            => Carbon::now()->addDays(6)->toDateString(),
                'view_count'          => 430,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Facial Glowing Treatment Diskon 35%',
                'description'         => 'Dapatkan kulit cerah bersinar dengan perawatan facial glowing menggunakan teknologi terkini. Cocok untuk semua jenis kulit. Booking sekarang dan dapatkan diskon 35%!',
                'poster_image'        => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=600',
                'discount_percentage' => 35.00,
                'original_price'      => 300000,
                'promo_price'         => 195000,
                'category_id'         => 3,
                'seller_id'           => 6,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(12)->toDateString(),
                'view_count'          => 655,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            // Elektronik
            [
                'title'               => 'Flash Sale TWS Earbuds Rp 149rb',
                'description'         => 'TWS earbuds bluetooth 5.0 dengan suara jernih dan bass kuat. Battery tahan 6 jam, case charging tahan 24 jam. Garansi toko 6 bulan. Stok terbatas!',
                'poster_image'        => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600',
                'discount_percentage' => 54.85,
                'original_price'      => 330000,
                'promo_price'         => 149000,
                'category_id'         => 4,
                'seller_id'           => 7,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(3)->toDateString(),
                'view_count'          => 2100,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            [
                'title'               => 'Promo Powerbank 20000mAh Rp 199rb',
                'description'         => 'Powerbank kapasitas 20.000 mAh dengan dual port USB dan fast charging 22.5W. Desain tipis dan ringan, cocok dibawa bepergian.',
                'poster_image'        => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=600',
                'discount_percentage' => 40.60,
                'original_price'      => 335000,
                'promo_price'         => 199000,
                'category_id'         => 4,
                'seller_id'           => 8,
                'start_date'          => Carbon::now()->subDays(1)->toDateString(),
                'end_date'            => Carbon::now()->addDays(8)->toDateString(),
                'view_count'          => 870,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            // Kesehatan
            [
                'title'               => 'Diskon 20% Vitamin & Suplemen',
                'description'         => 'Jaga kesehatan keluarga dengan vitamin dan suplemen berkualitas. Diskon 20% untuk semua produk vitamin C, D, dan multivitamin.',
                'poster_image'        => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600',
                'discount_percentage' => 20.00,
                'original_price'      => 85000,
                'promo_price'         => 68000,
                'category_id'         => 5,
                'seller_id'           => 9,
                'start_date'          => Carbon::now()->subDays(2)->toDateString(),
                'end_date'            => Carbon::now()->addDays(15)->toDateString(),
                'view_count'          => 390,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Konsultasi Herbal + Jamu Gratis',
                'description'         => 'Konsultasi kesehatan dengan herbalis berpengalaman dan dapatkan 1 paket jamu gratis. Menggunakan bahan herbal pilihan dari seluruh Nusantara.',
                'poster_image'        => 'https://images.unsplash.com/photo-1543362906-acfc16c67564?w=600',
                'discount_percentage' => 50.00,
                'original_price'      => 150000,
                'promo_price'         => 75000,
                'category_id'         => 5,
                'seller_id'           => 10,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(20)->toDateString(),
                'view_count'          => 290,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            // Pendidikan
            [
                'title'               => 'Diskon 50% Biaya Pendaftaran Bimbel',
                'description'         => 'Daftarkan putra-putri Anda sekarang dan hemat 50% biaya pendaftaran. Program belajar intensif untuk SD, SMP, SMA dengan pengajar berpengalaman.',
                'poster_image'        => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600',
                'discount_percentage' => 50.00,
                'original_price'      => 500000,
                'promo_price'         => 250000,
                'category_id'         => 6,
                'seller_id'           => 11,
                'start_date'          => Carbon::now()->subDays(5)->toDateString(),
                'end_date'            => Carbon::now()->addDays(25)->toDateString(),
                'view_count'          => 610,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Free Trial Kursus Bahasa Inggris 2 Minggu',
                'description'         => 'Coba gratis kursus bahasa Inggris selama 2 minggu! Metode komunikatif dengan native speaker dan instruktur bersertifikat. Kelas tersedia pagi, siang, dan malam.',
                'poster_image'        => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600',
                'discount_percentage' => 100.00,
                'original_price'      => 800000,
                'promo_price'         => 0,
                'category_id'         => 6,
                'seller_id'           => 12,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(30)->toDateString(),
                'view_count'          => 920,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            // Otomotif
            [
                'title'               => 'Servis Motor Lengkap + Gratis Oli',
                'description'         => 'Paket servis motor lengkap: ganti oli, cek rem, rantai, busi, dan filter udara. Gratis oli mesin 800ml. Dikerjakan oleh mekanik berpengalaman bersertifikat.',
                'poster_image'        => 'https://images.unsplash.com/photo-1558618047-3c3a8a1a06ac?w=600',
                'discount_percentage' => 45.00,
                'original_price'      => 180000,
                'promo_price'         => 99000,
                'category_id'         => 7,
                'seller_id'           => 13,
                'start_date'          => Carbon::now()->subDays(3)->toDateString(),
                'end_date'            => Carbon::now()->addDays(10)->toDateString(),
                'view_count'          => 475,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Cuci + Poles Mobil Full Body Rp 150rb',
                'description'         => 'Paket cuci mobil + poles eksterior full body dengan produk premium. Hasilnya mengkilap seperti baru! Proses 3-4 jam. Tersedia juga paket interior detailing.',
                'poster_image'        => 'https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=600',
                'discount_percentage' => 25.00,
                'original_price'      => 200000,
                'promo_price'         => 150000,
                'category_id'         => 7,
                'seller_id'           => 14,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(7)->toDateString(),
                'view_count'          => 340,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            // Hiburan & Wisata
            [
                'title'               => 'Tiket Waterboom 2 Orang Rp 120rb',
                'description'         => 'Ajak keluarga liburan seru di wahana air terbesar di Pekanbaru! Paket 2 tiket seharian hanya Rp 120.000. Tersedia berbagai wahana seru untuk semua usia.',
                'poster_image'        => 'https://images.unsplash.com/photo-1531722569936-825d4eea342d?w=600',
                'discount_percentage' => 40.00,
                'original_price'      => 200000,
                'promo_price'         => 120000,
                'category_id'         => 8,
                'seller_id'           => 15,
                'start_date'          => Carbon::now()->subDays(1)->toDateString(),
                'end_date'            => Carbon::now()->addDays(14)->toDateString(),
                'view_count'          => 1500,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            [
                'title'               => 'Nonton Bioskop 2 Tiket + Popcorn',
                'description'         => 'Nikmati pengalaman nonton film terbaru dengan paket hemat 2 tiket + 1 popcorn large + 2 minuman. Berlaku untuk semua film di semua studio.',
                'poster_image'        => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600',
                'discount_percentage' => 35.22,
                'original_price'      => 230000,
                'promo_price'         => 149000,
                'category_id'         => 8,
                'seller_id'           => 16,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(10)->toDateString(),
                'view_count'          => 1100,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
            // Properti & Rumah
            [
                'title'               => 'Diskon Cat Tembok 20% Semua Merk',
                'description'         => 'Renovasi rumah lebih hemat! Diskon 20% untuk semua merk cat tembok: Dulux, Catylac, Avian. Tersedia lengkap semua warna. Gratis konsultasi warna.',
                'poster_image'        => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=600',
                'discount_percentage' => 20.00,
                'original_price'      => 180000,
                'promo_price'         => 144000,
                'category_id'         => 9,
                'seller_id'           => 17,
                'start_date'          => Carbon::now()->subDays(4)->toDateString(),
                'end_date'            => Carbon::now()->addDays(20)->toDateString(),
                'view_count'          => 280,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Sofa Minimalis Jati Rp 1,5jt',
                'description'         => 'Sofa minimalis 3 dudukan dari bahan jati pilihan, finishing halus tahan lama. Harga promo sudah termasuk ongkos kirim area Jepara dan sekitarnya.',
                'poster_image'        => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600',
                'discount_percentage' => 30.23,
                'original_price'      => 2150000,
                'promo_price'         => 1500000,
                'category_id'         => 9,
                'seller_id'           => 18,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(21)->toDateString(),
                'view_count'          => 415,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            // Jasa & Layanan
            [
                'title'               => 'Cuci Pakaian 5kg Hanya Rp 15rb',
                'description'         => 'Laundry kilat selesai dalam 24 jam! Paket 5kg hanya Rp 15.000. Tersedia juga layanan setrika, cuci kering, dan dry cleaning. Antar jemput gratis radius 3km.',
                'poster_image'        => 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=600',
                'discount_percentage' => 25.00,
                'original_price'      => 20000,
                'promo_price'         => 15000,
                'category_id'         => 10,
                'seller_id'           => 19,
                'start_date'          => Carbon::now()->subDays(2)->toDateString(),
                'end_date'            => Carbon::now()->addDays(30)->toDateString(),
                'view_count'          => 560,
                'is_hot_deal'         => false,
                'status'              => 'active',
            ],
            [
                'title'               => 'Paket Foto Produk 20 Foto Rp 200rb',
                'description'         => 'Tingkatkan penjualan online Anda dengan foto produk profesional! Paket 20 foto hasil edit siap upload. Cocok untuk UMKM dan toko online.',
                'poster_image'        => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=600',
                'discount_percentage' => 50.00,
                'original_price'      => 400000,
                'promo_price'         => 200000,
                'category_id'         => 10,
                'seller_id'           => 20,
                'start_date'          => Carbon::now()->toDateString(),
                'end_date'            => Carbon::now()->addDays(14)->toDateString(),
                'view_count'          => 730,
                'is_hot_deal'         => true,
                'status'              => 'active',
            ],
        ];

        // Hapus promo lama
        DB::table('promos')->delete();

        foreach ($promos as $index => $promo) {
            // Ganti seller_id (nomor urut) dengan seller_profile id yang baru dibuat
            $promo['seller_id'] = $sellerProfileIds[$promo['seller_id']];
            DB::table('promos')->insert(array_merge($promo, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Selesai! ' . count($promos) . ' promo + 20 seller + 10 kategori berhasil dibuat!');
    }
}