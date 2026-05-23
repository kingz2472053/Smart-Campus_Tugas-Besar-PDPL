# Laporan Progres Mingguan - SmartCampus

## Detail Laporan
- **Minggu ke-** : 3 (Tiga)
- **Fokus Pengerjaan** : Penyelesaian Fitur Francisco (Activity Log, Real Email OTP, Role Management), Fitur Dave Andrew (Penilaian Otomatis - Strategy Pattern), dan Fitur Teofilus Juan P. (Notifikasi MultiChannel - Factory Method Pattern).
- **Tanggal Update** : 23 Mei 2026

---

## Kontributor Kelompok
1. **Francisco Valentino (21720XX)** — Riwayat Aktivitas, OTP Decorator, Login & Role Management
2. **Dave Andrew (2172015)** — Penilaian Otomatis (Strategy Pattern)
3. **Teofilus Juan P. (21720XX)** — Notifikasi MultiChannel (Factory Method Pattern)

---

## Pencapaian Progres Minggu 3

### Bagian 1: Francisco Valentino (Selesai 100%)

Pada minggu ketiga ini, fokus pengerjaan berada pada penyelesaian akhir fitur-fitur yang menjadi tanggung jawab Francisco Valentino (Fitur 1, Fitur 7, dan Fitur 12), serta mempersiapkan dokumentasi arsitektur perangkat lunak untuk persiapan tanya-jawab (Q&A) dengan Dosen PDPL.

#### 1. Master To-Do List & Dokumentasi Arsitektur
* **`TODO-LIST-PROJECT.md`**: Pembuatan file panduan kerja seluruh kelompok dari awal fase hingga fase akhir integrasi.
* **`README.md`**: Memperbarui penjelasan mendalam mengenai 12 Design Pattern yang digunakan, mencakup alasan teknis (seperti *Open/Closed Principle*, efisiensi memori, skalabilitas) untuk persiapan Q&A dengan dosen.

#### 2. Fitur 7: Riwayat Aktivitas (Activity Log - Singleton Pattern)
* Mengembangkan `ActivityLogController` yang mampu memfilter log aktivitas berdasarkan role (Admin dapat melihat semua log, Dosen/Mahasiswa hanya melihat log miliknya sendiri).
* Mengimplementasikan UI/UX lengkap pada `resources/views/activity-logs/index.blade.php` dengan badge interaktif, form filter, dan modal JSON.
* Menambahkan `show.blade.php` untuk melihat detail lengkap satu log aktivitas khusus Admin.
* Melakukan perbaikan pada routing `web.php` dan `layouts/app.blade.php` sehingga navigasi *sidebar* berfungsi normal tanpa link yang terputus.

#### 3. Fitur 12: Autentikasi Bertingkat OTP (Decorator Pattern)
* Membuat file *migration* baru untuk menambahkan kolom `otp_enabled` pada tabel `users`.
* Mengubah logika `OTPDecorator` agar mengecek status `otp_enabled` di database, sehingga aktivasi OTP menjadi dinamis (tidak di-hardcode).
* **Implementasi Real Email (Laravel Mail):** Mengubah sistem pengiriman OTP yang awalnya hanya tampil di layar menjadi pengiriman email sungguhan (*production-ready*) menggunakan Gmail SMTP (`smartcampus.pdpl@gmail.com`).
* Menerapkan trik *Gmail Aliases* (`smartcampus.pdpl+admin@gmail.com`) pada `SmartCampusSeeder` untuk mempermudah demonstrasi fitur email tanpa memerlukan banyak akun percobaan.
* Menghapus fitur kode *[DEV MODE]* dari layar verifikasi untuk simulasi yang realistis.

#### 4. Fitur 1: Login & Role Management (Abstract Factory)
* Sinkronisasi ulang data di `SmartCampusSeeder` (Courses, Enrollments, Assignments, Submissions) agar saling terhubung tanpa *crash*.
* Memastikan `RoleMiddleware` dan routing bekerja harmonis membatasi akses URL berdasarkan *role* yang sedang aktif.

---

### Bagian 2: Dave Andrew (Selesai 100%)

Mengimplementasikan **Strategy Pattern** (Fitur 5: Penilaian Otomatis) untuk menangani berbagai algoritma penilaian yang dapat disesuaikan untuk setiap mata kuliah secara dinamis saat runtime.

#### 1. Konsep Strategy Pattern
* **Algoritma yang Dapat Ditukar**: Dosen dapat melakukan penilaian tugas mahasiswa menggunakan strategi penilaian yang bervariasi (Numeric, Letter, atau Predicate) saat runtime tanpa perlu mengubah kode inti controller atau service.
* **Open/Closed Principle**: Menambahkan metode penilaian baru cukup dengan membuat kelas strategi baru yang mengimplementasikan interface tanpa menyentuh kode sistem penilaian utama yang sudah berjalan.

#### 2. Struktur Kelas & Desain Arsitektur
```
GradingStrategyInterface (Interface)
├── NumericGradingStrategy (Concrete Strategy) - Menilai dalam skala angka desimal (misal: 85.50)
├── LetterGradingStrategy (Concrete Strategy) - Mengonversi nilai ke dalam bentuk huruf (A, B, C, D, E)
└── PredicateGradingStrategy (Concrete Strategy) - Mengonversi nilai ke dalam bentuk predikat kelulusan (LULUS / TIDAK LULUS)

GradingService (Context)
└── Mengeksekusi strategi penilaian yang dipilih untuk menghitung nilai tugas mahasiswa, memperbarui state submission, dan memicu notifikasi
```

#### 3. Implementasi Teknis & Berkas Berubah
* **`app/Contracts/GradingStrategyInterface.php`**: Kontrak standar metode `calculate(float $rawScore)`.
* **`app/Services/Grading/NumericGradingStrategy.php`**: Berkas strategi penilaian numerik.
* **`app/Services/Grading/LetterGradingStrategy.php`**: Berkas strategi penilaian berbasis huruf.
* **`app/Services/Grading/PredicateGradingStrategy.php`**: Berkas strategi penilaian berbasis kelulusan.
* **`app/Services/Grading/GradingService.php`**: Service pengelola (Context) yang memicu perhitungan nilai menggunakan strategy serta memicu transisi state penugasan (State Pattern).
* **`database/migrations/2026_05_23_095035_add_grading_type_to_courses_table.php`**: Menambahkan kolom jenis strategi penilaian pada tabel mata kuliah.
* **`database/migrations/2026_05_23_102705_modify_grading_strategy_in_grades_table.php`**: Menambahkan kolom `grading_strategy` pada tabel `grades` untuk audit trail.

---

### Bagian 3: Teofilus Juan P. (Selesai 100%)

Mengembangkan fitur **Notifikasi MultiChannel** (Fitur 10) menggunakan pola desain **Factory Method (GoF Standard)** secara murni. Fitur ini dirancang untuk dapat bertahan terhadap kegagalan pengiriman (resilient) dan dilengkapi dengan antarmuka UI/UX premium yang interaktif.

#### 1. Desain Arsitektur Factory Method Pattern
Pola ini memisahkan instansiasi saluran pengiriman notifikasi (*Product*) dari logika pemrosesan dan pengirimannya (*Creator*), mempromosikan hubungan *loose coupling*.

```
NotificationSender (Abstract Creator)
├── sendNotification(User, string, array) [Metode Pembantu]
└── createChannel(): NotificationChannelInterface [Factory Method Abstrak]
    ├── EmailNotificationSender (Concrete Creator) ──> menciptakan ──> EmailChannel (Concrete Product)
    └── DashboardNotificationSender (Concrete Creator) ──> menciptakan ──> DashboardChannel (Concrete Product)

NotificationChannelInterface (Product Interface)
├── send(User, string, array)
```

##### Berkas Arsitektur yang Dibuat:
* **[NotificationChannelInterface.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Contracts/NotificationChannelInterface.php)**: Kontrak antarmuka produk notifikasi.
* **[NotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/NotificationSender.php)**: Creator abstrak yang mendefinisikan metode *factory* `createChannel()`.
* **[DashboardNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardNotificationSender.php)** & **[EmailNotificationSender.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailNotificationSender.php)**: Concrete Creator yang mengembalikan instance produk masing-masing.
* **[DashboardChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/DashboardChannel.php)** & **[EmailChannel.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Notification/EmailChannel.php)**: Concrete Product yang menangani penyimpanan di database (Dashboard) dan pengiriman e-mail riil via SMTP (Email).

#### 2. Integrasi Pemicu Notifikasi (Notification Triggers)
Notifikasi berjalan otomatis dengan terintegrasi secara harmonis pada beberapa bagian penting sistem:
1. **Pengingat Deadline H-1 (Integrasi Observer)**:
   * Di dalam [DeadlineNotifier.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Observers/DeadlineNotifier.php), memicu pengiriman notifikasi dashboard H-1 kepada mahasiswa yang belum mengumpulkan tugas.
2. **Publikasi Tugas Baru (Integrasi Command)**:
   * Di dalam [CreateTaskCommand.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Task/CreateTaskCommand.php), sesaat setelah tugas baru dibuat oleh Dosen, sistem mendeteksi seluruh mahasiswa terdaftar dan mengirimkan notifikasi tugas baru ke Dashboard mereka.
3. **Tugas Selesai Dinilai (Integrasi Strategy)**:
   * Di dalam [GradingService.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/app/Services/Grading/GradingService.php), setelah dosen menyimpan nilai tugas mahasiswa, sistem langsung mengirimkan notifikasi secara **MultiChannel (Dashboard + Email secara simultan)**.

#### 3. Ketahanan Terhadap Kegagalan (Resilience & Error Handling)
* **SMTP Fail-Safe**: Pengiriman email di `EmailChannel` dibungkus sepenuhnya dalam blok `try-catch`. Jika koneksi email/SMTP mati, sistem akan mencatat rincian kegagalan ke Laravel log (`storage/logs/laravel.log`) tetapi **aplikasi tidak akan crash/berhenti**. Logic utama sistem (seperti proses penilaian atau publikasi tugas) akan tetap berjalan mulus bagi pengguna.

#### 4. Antarmuka UI/UX Premium (AJAX Interaktif)
* **Dropdown Lonceng Topbar**: Ditambahkan ke [app.blade.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/resources/views/layouts/app.blade.php). Menampilkan jumlah notifikasi unread dengan badge merah menyala. Dropdown memuat 5 notifikasi unread teratas secara asinkronus (polling tiap 60 detik) dengan tombol "Tandai Semua Terbaca" yang akan mereduksi badge dan memperbarui status secara real-time via AJAX (tanpa reload).
* **Pusat Notifikasi (Notification Center)**: Dibuat di [index.blade.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/resources/views/notifications/index.blade.php). Menyajikan riwayat seluruh notifikasi dengan filter pill (Semua, Belum Dibaca, Sudah Dibaca), ikon pembeda jenis channel, serta dukungan penandaan terbaca parsial dan penghapusan data.
* **Email HTML Premium**: Templat email yang elegan dirancang di [notification.blade.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/resources/views/emails/notification.blade.php) menggunakan warna korporat SmartCampus dan tata letak responsif.

#### 5. Automated Testing (100% Green)
Telah dibuat berkas pengujian fungsionalitas di [NotificationMultiChannelTest.php](file:///c:/Semester%204/PDPL/Tubes/Smart-Campus_Tugas-Besar-PDPL/SmartCampus/tests/Feature/NotificationMultiChannelTest.php) untuk memvalidasi kebenaran arsitektur:
* `test_factory_method_creates_correct_channels` (Verifikasi kecocokan Creator & Product)
* `test_dashboard_notification_sender_saves_to_database` (Verifikasi penyimpanan database)
* `test_email_notification_sender_sends_email_and_logs` (Verifikasi integrasi e-mail menggunakan `Mail::fake()`)

Seluruh pengujian sukses dilewati dengan status **PASS**.

---

## Tabel Ringkasan Design Pattern (Minggu 3)

| Design Pattern | Komponen | Kontributor | Peran dalam Sistem |
| :--- | :--- | :--- | :--- |
| **Singleton Pattern** | `ActivityLogger` | Francisco V. | Memastikan pencatatan log aktivitas diakses melalui satu gerbang instansiasi terpusat. |
| **Decorator Pattern** | `OTPDecorator` | Francisco V. | Menambahkan lapisan keamanan tambahan (OTP) secara dinamis di atas proses autentikasi standar. |
| **Strategy Pattern** | `GradingService` | Dave Andrew | Memungkinkan dosen memilih algoritma penilaian tugas (Numeric/Letter/Predicate) saat runtime secara fleksibel. |
| **Factory Method** | `NotificationSender` | Teofilus Juan P. | Menangani pembuatan saluran pengiriman notifikasi (Email & Dashboard) secara dinamis tanpa keterikatan erat (*decoupled*). |

---

## Rencana Kerja Selanjutnya (Minggu 4)
* Implementasi UI Theme Factory untuk Light/Dark Mode (Fitur 9 - Abstract Factory).
* Penyimpanan Data File Tugas (Fitur 6 - Strategy Pattern).
* Penggabungan seluruh fitur ke dalam branch utama (`main`) dan finalisasi pengetesan integrasi akhir sistem (*end-to-end*).
