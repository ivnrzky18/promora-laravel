# Requirements Document

## Introduction

Promora adalah platform web full-stack berbasis Laravel yang berfungsi sebagai pusat informasi terpusat bagi UMKM (Usaha Mikro, Kecil, dan Menengah) Indonesia untuk mempublikasikan promosi dan acara secara real-time. Platform ini menghubungkan konsumen dengan penjual lokal melalui fitur pencarian, kalender promo, notifikasi push, dan sistem ulasan — semuanya dalam satu destinasi yang terfokus tanpa konten yang tidak relevan.

Stack teknologi: Laravel 13 (PHP 8.3), Blade + Tailwind CSS + Alpine.js, MySQL 8.0 via Laragon, Laravel Breeze (dual guard), Laravel Notifications + Web Push.

---

## Glossary

- **System**: Platform Promora secara keseluruhan
- **Consumer**: Pengguna terdaftar dengan peran `consumer` yang menjelajahi dan menyimpan promosi
- **Seller**: Pengguna terdaftar dengan peran `seller` yang mewakili UMKM dan mempublikasikan promosi/acara
- **Admin**: Pengguna terdaftar dengan peran `admin` yang mengelola verifikasi dan moderasi konten
- **Promo**: Entri promosi yang dibuat oleh Seller, berisi diskon, harga, dan rentang tanggal berlaku
- **Event**: Entri acara yang dibuat oleh Seller, berisi lokasi dan tanggal pelaksanaan
- **Hot Deal**: Promo dengan status `active` yang berakhir dalam 48 jam ke depan
- **Bookmark**: Promo yang disimpan oleh Consumer untuk referensi di masa mendatang
- **Subscription**: Hubungan antara Consumer dan Seller di mana Consumer memilih menerima notifikasi dari Seller tersebut
- **Notification**: Pesan yang dikirim ke Consumer ketika Seller yang diikuti mempublikasikan Promo baru
- **SellerProfile**: Profil bisnis yang terhubung ke akun Seller, berisi informasi UMKM
- **Review**: Penilaian bintang dan komentar yang diberikan Consumer kepada Seller
- **Category**: Klasifikasi UMKM (Kuliner, Fashion, Jasa, Kesehatan, Pendidikan, Hiburan)
- **Guard**: Mekanisme autentikasi Laravel yang memisahkan sesi Consumer, Seller, dan Admin
- **Scheduler**: Laravel Task Scheduler yang menjalankan perintah terjadwal secara otomatis
- **FormRequest**: Kelas validasi Laravel yang memvalidasi input formulir dengan pesan kesalahan
- **ResourceController**: Laravel Controller yang mengimplementasikan metode CRUD standar (index, create, store, show, edit, update, destroy)

---

## Requirements

### Requirement 1: Autentikasi Dual Guard

**User Story:** Sebagai pengguna baru, saya ingin mendaftar dan masuk sesuai peran saya (Consumer, Seller, atau Admin), agar saya dapat mengakses fitur yang sesuai dengan peran tersebut.

#### Acceptance Criteria

1. WHEN seorang pengguna mengakses halaman registrasi Consumer, THE System SHALL menampilkan formulir dengan field: nama, email, kata sandi, konfirmasi kata sandi, dan lokasi (kota)
2. WHEN seorang pengguna mengakses halaman registrasi Seller, THE System SHALL menampilkan formulir dengan field: nama, email, kata sandi, nama bisnis, kategori bisnis, alamat, dan deskripsi
3. WHEN formulir registrasi Consumer dikirim dengan data valid, THE System SHALL membuat akun dengan `role = consumer` dan mengarahkan pengguna ke Consumer Dashboard
4. WHEN formulir registrasi Seller dikirim dengan data valid, THE System SHALL membuat akun dengan `role = seller`, membuat entri SellerProfile terkait, dan mengarahkan pengguna ke Seller Dashboard
5. WHEN formulir registrasi dikirim dengan email yang sudah terdaftar, THE System SHALL menampilkan pesan kesalahan "Email sudah digunakan" tanpa membuat akun baru
6. WHEN seorang Consumer berhasil login, THE System SHALL mengarahkan pengguna ke Consumer Dashboard menggunakan Guard `consumer`
7. WHEN seorang Seller berhasil login, THE System SHALL mengarahkan pengguna ke Seller Dashboard menggunakan Guard `seller`
8. WHEN seorang Admin berhasil login, THE System SHALL mengarahkan pengguna ke Admin Panel menggunakan Guard `admin`
9. IF kredensial login tidak valid, THEN THE System SHALL menampilkan pesan kesalahan "Email atau kata sandi salah" dan tidak membuat sesi baru
10. WHEN pengguna yang sudah login mengakses rute yang dilindungi Guard berbeda, THE System SHALL mengarahkan pengguna ke halaman login yang sesuai dengan Guard tersebut
11. THE System SHALL memvalidasi semua input formulir autentikasi menggunakan FormRequest dengan pesan kesalahan dalam Bahasa Indonesia

---

### Requirement 2: Consumer Dashboard

**User Story:** Sebagai Consumer, saya ingin melihat ringkasan aktivitas dan feed promosi yang relevan setelah login, agar saya dapat dengan cepat menemukan penawaran terbaru dari Seller yang saya ikuti.

#### Acceptance Criteria

1. WHEN Consumer mengakses Consumer Dashboard, THE System SHALL menampilkan banner sambutan dengan nama Consumer
2. WHEN Consumer mengakses Consumer Dashboard, THE System SHALL menampilkan statistik ringkas: jumlah Bookmark tersimpan dan jumlah Subscription aktif
3. WHEN Consumer mengakses Consumer Dashboard, THE System SHALL menampilkan feed Promo aktif dari Seller yang diikuti Consumer, diurutkan berdasarkan tanggal terbaru
4. WHEN Consumer mengakses Consumer Dashboard, THE System SHALL menampilkan seksi Hot Deals berisi Promo dengan `end_date` dalam 48 jam ke depan dan `status = active`
5. WHEN Consumer mengklik ikon bookmark pada kartu Promo, THE System SHALL menyimpan atau menghapus Bookmark melalui permintaan AJAX tanpa memuat ulang halaman
6. THE System SHALL menampilkan jumlah total Bookmark pada setiap kartu Promo
7. THE System SHALL menyediakan navigasi ke halaman: Jelajahi (Explore), Kalender, Notifikasi, dan Profil Consumer

---

### Requirement 3: Seller Dashboard

**User Story:** Sebagai Seller, saya ingin mengelola promosi dan acara bisnis saya serta melihat performa bisnis saya, agar saya dapat mengoptimalkan strategi promosi UMKM saya.

#### Acceptance Criteria

1. WHEN Seller mengakses Seller Dashboard, THE System SHALL menampilkan kartu ringkasan SellerProfile dengan logo, nama bisnis, dan status verifikasi
2. WHEN Seller mengakses Seller Dashboard, THE System SHALL menampilkan statistik: total Promo, total penayangan (view_count), Promo aktif, jumlah Subscriber, dan rata-rata rating
3. WHEN Seller mengirim formulir unggah Promo dengan data valid, THE System SHALL membuat entri Promo baru dengan `status = draft` dan menghubungkannya ke Seller tersebut
4. WHEN Seller mengunggah gambar poster Promo, THE System SHALL menyimpan file ke disk `public` menggunakan Laravel Storage dan menyimpan path-nya di kolom `poster_image`
5. WHEN Seller mengirim formulir unggah Event dengan data valid, THE System SHALL membuat entri Event baru dan menghubungkannya ke Seller tersebut
6. THE System SHALL menampilkan daftar Promo milik Seller dengan badge status: `active`, `expired`, atau `draft`
7. WHEN Seller mengklik tombol edit pada Promo, THE System SHALL menampilkan formulir yang telah terisi dengan data Promo yang ada
8. WHEN Seller mengklik tombol hapus pada Promo, THE System SHALL melakukan soft delete pada entri Promo tersebut
9. THE System SHALL menampilkan daftar Review yang diterima Seller beserta rating dan komentar dari Consumer

---

### Requirement 4: Admin Panel

**User Story:** Sebagai Admin, saya ingin mengelola verifikasi Seller dan moderasi konten platform, agar kualitas dan kepercayaan platform Promora tetap terjaga.

#### Acceptance Criteria

1. WHEN Admin mengakses Admin Panel, THE System SHALL menampilkan daftar Seller yang menunggu verifikasi (`is_verified = false`)
2. WHEN Admin mengklik tombol "Setujui" pada Seller, THE System SHALL memperbarui `is_verified = true` pada SellerProfile terkait
3. WHEN Admin mengklik tombol "Tolak" pada Seller, THE System SHALL menghapus akun Seller dan SellerProfile terkait setelah konfirmasi
4. WHEN Admin mengakses halaman moderasi Promo, THE System SHALL menampilkan daftar Promo dengan `status = draft` yang menunggu persetujuan
5. WHEN Admin menyetujui sebuah Promo, THE System SHALL memperbarui `status = active` pada Promo tersebut
6. WHEN Admin menolak sebuah Promo, THE System SHALL menghapus entri Promo tersebut setelah konfirmasi
7. THE System SHALL menyediakan halaman manajemen Category dengan operasi CRUD: tambah, lihat, edit, dan hapus Category
8. WHEN Admin mengakses halaman statistik platform, THE System SHALL menampilkan: total Consumer terdaftar, total Seller terdaftar, total Promo aktif, dan total Event aktif

---

### Requirement 5: Kalender Promo dan Event

**User Story:** Sebagai Consumer, saya ingin melihat semua promosi dan acara dalam tampilan kalender interaktif, agar saya dapat merencanakan pembelian dan kehadiran acara dengan lebih mudah.

#### Acceptance Criteria

1. WHEN Consumer mengakses halaman Kalender, THE System SHALL menampilkan kalender interaktif menggunakan FullCalendar.js yang dimuat dari CDN
2. WHEN kalender dimuat, THE System SHALL menampilkan Promo aktif sebagai entri kalender berwarna oranye pada tanggal `start_date` hingga `end_date`
3. WHEN kalender dimuat, THE System SHALL menampilkan Event aktif sebagai entri kalender berwarna biru pada tanggal `event_date`
4. WHEN Consumer memilih filter Category pada halaman Kalender, THE System SHALL memperbarui tampilan kalender hanya menampilkan Promo dan Event dari Category yang dipilih
5. WHEN Consumer mengklik entri kalender, THE System SHALL menampilkan detail singkat Promo atau Event dalam popup atau modal

---

### Requirement 6: Hot Deals

**User Story:** Sebagai Consumer, saya ingin melihat promosi yang hampir berakhir dengan penghitung waktu mundur, agar saya tidak melewatkan penawaran terbatas.

#### Acceptance Criteria

1. WHEN Consumer mengakses halaman Hot Deals, THE System SHALL menampilkan Promo dengan `status = active` dan `end_date` dalam 48 jam ke depan, diurutkan berdasarkan `end_date` terdekat
2. WHEN Hot Deals ditampilkan, THE System SHALL menampilkan badge "Berakhir Segera" pada setiap kartu Promo
3. WHEN Hot Deals ditampilkan, THE System SHALL menampilkan penghitung waktu mundur JavaScript yang menunjukkan sisa waktu hingga `end_date` pada setiap kartu Promo
4. THE Scheduler SHALL menjalankan perintah setiap jam untuk memperbarui kolom `is_hot_deal = true` pada Promo yang memenuhi kriteria Hot Deal
5. THE Scheduler SHALL menjalankan perintah setiap jam untuk memperbarui `status = expired` pada Promo yang `end_date`-nya telah terlewati

---

### Requirement 7: Notifikasi Push Subscription

**User Story:** Sebagai Consumer, saya ingin berlangganan ke Seller tertentu dan menerima notifikasi ketika mereka memposting promosi baru, agar saya tidak melewatkan penawaran dari UMKM favorit saya.

#### Acceptance Criteria

1. WHEN Consumer mengunjungi halaman profil publik Seller, THE System SHALL menampilkan tombol toggle berlangganan (subscribe/unsubscribe)
2. WHEN Consumer mengklik tombol subscribe, THE System SHALL membuat entri Subscription yang menghubungkan Consumer dengan Seller tersebut
3. WHEN Consumer mengklik tombol unsubscribe, THE System SHALL menghapus entri Subscription terkait
4. WHEN Seller mempublikasikan Promo baru (status berubah menjadi `active`), THE System SHALL mengirim Notification ke semua Consumer yang memiliki Subscription aktif ke Seller tersebut
5. WHEN Consumer mengakses halaman Notifikasi, THE System SHALL menampilkan semua Notification milik Consumer diurutkan berdasarkan waktu terbaru
6. WHEN Consumer mengklik sebuah Notification, THE System SHALL menandai Notification tersebut sebagai telah dibaca dengan mengisi kolom `read_at`
7. THE System SHALL menampilkan jumlah Notification yang belum dibaca pada ikon lonceng di navigasi

---

### Requirement 8: Kategorisasi UMKM dan Pencarian

**User Story:** Sebagai Consumer, saya ingin mencari dan memfilter promosi berdasarkan kategori, lokasi, dan kata kunci, agar saya dapat menemukan penawaran yang relevan dengan kebutuhan saya.

#### Acceptance Criteria

1. WHEN Consumer mengakses halaman Jelajahi (Explore), THE System SHALL menampilkan filter Category: Kuliner, Fashion, Jasa, Kesehatan, Pendidikan, dan Hiburan
2. WHEN Consumer memilih satu atau lebih Category, THE System SHALL memfilter hasil pencarian hanya menampilkan Promo dari Category yang dipilih
3. WHEN Consumer memasukkan nama kota atau kecamatan pada filter lokasi, THE System SHALL memfilter hasil pencarian berdasarkan kolom `location` pada SellerProfile
4. WHEN Consumer memasukkan kata kunci pada kolom pencarian, THE System SHALL mencari kecocokan pada kolom `title` dan `description` Promo serta kolom `business_name` SellerProfile
5. THE System SHALL menampilkan halaman hasil pencarian dengan opsi pengurutan: terbaru, berakhir segera, dan paling banyak dilihat
6. THE System SHALL menggunakan parameter URL untuk menyimpan filter aktif sehingga tautan hasil pencarian dapat dibagikan
7. WHEN Consumer mengakses URL hasil pencarian dengan parameter filter, THE System SHALL menerapkan filter yang sesuai secara otomatis

---

### Requirement 9: Pencarian Berbasis Lokasi

**User Story:** Sebagai Consumer, saya ingin menemukan promosi dari UMKM terdekat berdasarkan lokasi saya, agar saya dapat dengan mudah mengunjungi toko yang menawarkan promosi tersebut.

#### Acceptance Criteria

1. WHEN Seller mendaftar atau mengedit SellerProfile, THE System SHALL menyediakan field input untuk `latitude` dan `longitude`
2. WHEN Consumer mengizinkan akses geolokasi browser, THE System SHALL mengambil koordinat Consumer menggunakan Geolocation API browser dan menggunakannya sebagai titik referensi pencarian
3. WHEN Consumer memasukkan nama kota secara manual, THE System SHALL menggunakan nilai tersebut sebagai filter lokasi berbasis teks pada kolom `address` SellerProfile
4. WHEN hasil pencarian ditampilkan dengan referensi lokasi Consumer, THE System SHALL menghitung dan menampilkan jarak dalam kilometer antara Consumer dan setiap Seller pada kartu Promo
5. THE System SHALL menyediakan opsi pengurutan hasil berdasarkan jarak terdekat ke terjauh

---

### Requirement 10: Sistem Rating dan Ulasan

**User Story:** Sebagai Consumer, saya ingin memberikan rating dan ulasan kepada Seller setelah berinteraksi dengan promosi mereka, agar Consumer lain dapat membuat keputusan yang lebih baik berdasarkan pengalaman nyata.

#### Acceptance Criteria

1. WHEN Consumer yang sudah login mengunjungi halaman profil publik Seller, THE System SHALL menampilkan formulir untuk memberikan rating (1–5 bintang) dan komentar
2. WHEN Consumer mengirim Review untuk Seller yang belum pernah diulas oleh Consumer tersebut, THE System SHALL menyimpan entri Review baru
3. IF Consumer mencoba mengirim Review kedua untuk Seller yang sama, THEN THE System SHALL menolak permintaan dan menampilkan pesan "Anda sudah memberikan ulasan untuk seller ini"
4. THE System SHALL menghitung dan menampilkan rata-rata rating Seller pada halaman profil publik Seller dan pada setiap kartu Promo milik Seller tersebut
5. WHEN halaman profil publik Seller dimuat, THE System SHALL menampilkan daftar semua Review yang diterima Seller beserta nama Consumer, rating, dan komentar

---

### Requirement 11: Bookmark / Simpan Promo

**User Story:** Sebagai Consumer, saya ingin menyimpan promosi yang menarik untuk dilihat kembali nanti, agar saya tidak kehilangan penawaran yang ingin saya manfaatkan.

#### Acceptance Criteria

1. WHEN Consumer yang sudah login mengklik ikon bookmark pada kartu Promo, THE System SHALL membuat entri Bookmark melalui permintaan AJAX jika belum ada
2. WHEN Consumer yang sudah login mengklik ikon bookmark pada Promo yang sudah di-bookmark, THE System SHALL menghapus entri Bookmark melalui permintaan AJAX
3. THE System SHALL mengembalikan respons JSON dari operasi bookmark yang berisi status terbaru (`bookmarked: true/false`) dan jumlah total Bookmark Promo tersebut
4. WHEN Consumer mengakses tab "Tersimpan" di Consumer Dashboard, THE System SHALL menampilkan semua Promo yang di-bookmark oleh Consumer tersebut
5. IF Promo yang di-bookmark telah dihapus (soft deleted) atau kedaluwarsa, THEN THE System SHALL tetap menampilkan Promo tersebut di daftar Bookmark dengan label "Tidak Tersedia"

---

### Requirement 12: Halaman Profil Publik Seller

**User Story:** Sebagai Consumer, saya ingin melihat halaman profil lengkap sebuah UMKM, agar saya dapat mengetahui informasi bisnis, semua promosi aktif, dan ulasan dari Consumer lain sebelum memutuskan untuk berlangganan.

#### Acceptance Criteria

1. WHEN Consumer mengakses URL profil publik Seller, THE System SHALL menampilkan: logo, nama bisnis, Category, deskripsi, dan alamat Seller
2. WHEN halaman profil publik Seller dimuat, THE System SHALL menampilkan semua Promo aktif milik Seller tersebut
3. WHEN halaman profil publik Seller dimuat, THE System SHALL menampilkan rata-rata rating dan daftar Review dari Consumer
4. WHEN Consumer yang sudah login mengunjungi halaman profil publik Seller, THE System SHALL menampilkan tombol subscribe/unsubscribe sesuai status Subscription Consumer saat ini
5. THE System SHALL menampilkan tombol "Bagikan Profil" yang menyalin URL profil publik Seller ke clipboard Consumer

---

### Requirement 13: Unggah Gambar dan Penyimpanan File

**User Story:** Sebagai Seller, saya ingin mengunggah gambar poster untuk promosi dan acara saya, agar tampilan promosi lebih menarik bagi Consumer.

#### Acceptance Criteria

1. WHEN Seller mengunggah gambar poster Promo atau Event, THE System SHALL menerima file dengan format JPEG, PNG, atau WebP dengan ukuran maksimum 2MB
2. IF Seller mengunggah file dengan format atau ukuran yang tidak valid, THEN THE System SHALL menampilkan pesan kesalahan "Format file tidak didukung atau ukuran melebihi 2MB"
3. THE System SHALL menyimpan file yang diunggah ke disk `public` menggunakan `storage:link` Laravel dan menyimpan path relatif di kolom `poster_image`
4. WHEN Seller memperbarui gambar poster Promo yang sudah ada, THE System SHALL menghapus file lama dari storage sebelum menyimpan file baru

---

### Requirement 14: Endpoint API JSON

**User Story:** Sebagai developer pihak ketiga, saya ingin mengakses data Promora melalui endpoint API JSON, agar saya dapat mengintegrasikan data promosi ke dalam aplikasi lain.

#### Acceptance Criteria

1. WHEN permintaan GET dikirim ke endpoint `/api/promos`, THE System SHALL mengembalikan daftar Promo aktif dalam format JSON dengan field: id, title, description, discount_percentage, promo_price, start_date, end_date, poster_image, seller name, dan category name
2. WHEN permintaan GET dikirim ke endpoint `/api/sellers`, THE System SHALL mengembalikan daftar Seller yang telah diverifikasi dalam format JSON dengan field: id, business_name, business_category, description, address, logo, dan average_rating
3. WHEN permintaan GET dikirim ke endpoint `/api/promos/{id}`, THE System SHALL mengembalikan detail lengkap satu Promo dalam format JSON
4. IF permintaan GET dikirim ke endpoint `/api/promos/{id}` dengan ID yang tidak ada, THEN THE System SHALL mengembalikan respons JSON dengan HTTP status 404 dan pesan "Promo tidak ditemukan"

---

### Requirement 15: Database Seeding dan Migrasi

**User Story:** Sebagai developer, saya ingin menjalankan satu perintah untuk menyiapkan database dengan data awal, agar proses setup dan pengujian platform dapat dilakukan dengan cepat.

#### Acceptance Criteria

1. WHEN perintah `php artisan migrate --seed` dijalankan, THE System SHALL membuat semua tabel database: users, seller_profiles, categories, promos, events, subscriptions, bookmarks, reviews, dan notifications
2. WHEN perintah `php artisan migrate --seed` dijalankan, THE System SHALL mengisi database dengan data awal: minimal 3 Category, 2 Seller dengan SellerProfile, dan 5 Promo dengan berbagai status
3. THE System SHALL mengimplementasikan soft delete pada model Promo dan SellerProfile menggunakan kolom `deleted_at`
4. WHEN migrasi dijalankan, THE System SHALL membuat tabel `users` dengan kolom tambahan: `role` (enum: consumer/seller/admin), `phone`, `avatar`, dan `location`

---

### Requirement 16: Desain Responsif

**User Story:** Sebagai Consumer yang menggunakan perangkat mobile, saya ingin platform Promora dapat digunakan dengan nyaman di layar kecil, agar saya dapat menjelajahi promosi kapan saja dan di mana saja.

#### Acceptance Criteria

1. THE System SHALL mengimplementasikan desain mobile-first menggunakan Tailwind CSS pada semua halaman
2. WHEN halaman diakses pada viewport dengan lebar kurang dari 768px, THE System SHALL menampilkan layout satu kolom yang dapat digunakan dengan nyaman menggunakan layar sentuh
3. WHEN halaman diakses pada viewport dengan lebar 768px atau lebih, THE System SHALL menampilkan layout multi-kolom yang memanfaatkan ruang layar yang lebih luas
4. THE System SHALL memastikan semua elemen interaktif (tombol, tautan, input) memiliki ukuran target sentuh minimal 44x44 piksel pada tampilan mobile
