# 🎓 SmartCampus - Sistem Manajemen Tugas & Proyek Mahasiswa

SmartCampus adalah sebuah platform web untuk memanajemen tugas perkuliahan yang dibangun dengan menerapkan berbagai **Pola Desain Perangkat Lunak (Software Design Patterns)** secara ekstensif untuk memastikan kode yang *scalable*, *maintainable*, dan *clean*.

Berikut adalah penjelasan mengapa kami menggunakan pola desain (Design Pattern) tertentu pada setiap fitur dalam proyek ini. Penjelasan ini sangat berguna untuk menjawab pertanyaan dosen saat presentasi.

---

## 🛠️ Pembagian Fitur & Analisis Design Pattern

### 1. Login & Role Management
- **Aktor:** Semua Pengguna
- **Design Pattern:** Abstract Factory + Factory Method
- **Alasan Penggunaan:** 
  Dalam SmartCampus, kita memiliki beberapa tipe pengguna (Mahasiswa, Dosen, Admin) yang memiliki profil, dashboard, dan hak akses yang sama sekali berbeda. Jika kita menggunakan `if-else` panjang di dalam controller saat registrasi atau login, kode akan menjadi berantakan. **Abstract Factory** memungkinkan kita untuk mendelegasikan pembuatan objek pengguna (beserta profil spesifiknya) ke *factory* masing-masing (misal: `StudentFactory`, `LecturerFactory`). Hal ini membuat penambahan role baru di masa depan sangat mudah tanpa mengganggu kode yang sudah ada (memenuhi prinsip *Open/Closed Principle*).

### 2. Manajemen Tugas (CRUD)
- **Aktor:** Dosen
- **Design Pattern:** Command Pattern
- **Alasan Penggunaan:**
  Operasi CRUD (Create, Read, Update, Delete) tugas dienkapsulasi menjadi sebuah objek *Command* yang independen (misal: `CreateTaskCommand`, `DeleteTaskCommand`). Tujuannya adalah untuk memisahkan logika eksekusi dari Controller. Lebih penting lagi, pola ini dipilih karena sangat erat kaitannya dengan kebutuhan fitur **Activity Logging** dan **Undo/Redo**. Dengan Command Pattern, setiap aksi dapat menyimpan "state" (data sebelum dan sesudah diubah) yang nantinya sangat mudah untuk direkam atau dibatalkan.

### 3. Deadline Reminder Otomatis
- **Aktor:** Sistem / Mahasiswa
- **Design Pattern:** Observer Pattern
- **Alasan Penggunaan:**
  Pola *Observer* sangat cocok untuk skenario yang memiliki hubungan "*one-to-many dependency*". Ketika sebuah tugas (Assignment) mendekati *deadline*, status waktu pada tugas tersebut berubah (Subject). Ratusan mahasiswa yang mengambil mata kuliah tersebut (Observers) perlu diberitahu secara otomatis. Dengan *Observer*, kita memisahkan logika utama tugas dengan logika pengiriman notifikasi, sehingga kode tidak saling tumpang tindih.

### 4. Tracking Progress Tugas
- **Aktor:** Mahasiswa
- **Design Pattern:** State Pattern
- **Alasan Penggunaan:**
  Sebuah tugas (Submission) melewati beberapa fase (State) yang spesifik: *Not Started* -> *On Progress* -> *Completed*. Pada setiap fase, tugas tersebut memiliki aturan (behavior) yang berbeda. Contoh: Jika tugas sudah "Completed", maka mahasiswa tidak boleh mengedit file lagi. **State Pattern** memungkinkan objek *Submission* mengubah perilakunya sendiri ketika state internalnya berubah, sehingga kita menghindari penggunaan `switch-case` atau `if-else` yang sangat panjang untuk mengecek status.

### 5. Sistem Penilaian Otomatis
- **Aktor:** Dosen
- **Design Pattern:** Strategy Pattern
- **Alasan Penggunaan:**
  Dosen bisa saja memberikan tugas dengan skema penilaian yang berbeda-beda. Ada yang menggunakan angka (0-100), huruf (A/B/C/D), atau sekadar predikat (Lulus/Tidak Lulus). Karena algoritmanya bisa berubah-ubah *at runtime* (saat program berjalan), kita menggunakan **Strategy Pattern**. Dengan ini, algoritma penilaian dipisahkan menjadi class *Strategy* yang berbeda, dan dosen dapat memilih *grading strategy* mana yang akan dipakai tanpa mengubah struktur utama aplikasi.

### 6. Penyimpanan Data (Storage)
- **Aktor:** Sistem
- **Design Pattern:** Strategy Pattern
- **Alasan Penggunaan:**
  Sistem harus fleksibel dalam menyimpan file submission mahasiswa. Kadang kita ingin menyimpannya di memori lokal (*Local Disk*), kadang di *Cloud* (seperti AWS S3 atau Google Cloud). **Strategy Pattern** memungkinkan kita untuk mendefinisikan *interface* penyimpanan tunggal, dan menukar implementasi penyimpanannya dengan mudah (tinggal *inject* strategy yang berbeda) tanpa perlu membongkar logika utama controller.

### 7. Riwayat Aktivitas (Activity Log)
- **Aktor:** Sistem / Admin
- **Design Pattern:** Singleton Pattern
- **Alasan Penggunaan:**
  Log aktivitas dibutuhkan secara global dan dipanggil terus-menerus oleh hampir seluruh fitur di aplikasi (saat user login, buat tugas, edit profil, dsb). Jika kita membuat instance baru (`new ActivityLogger()`) setiap kali mau mencatat log, itu akan sangat memboroskan memori. **Singleton** memastikan bahwa hanya ada **satu** instance logger (*Centralized Access*) yang hidup selama aplikasi berjalan, menjamin efisiensi memori dan menghindari redudansi koneksi.

### 8. Fitur Undo / Redo
- **Aktor:** Dosen / Mahasiswa
- **Design Pattern:** Command Pattern
- **Alasan Penggunaan:**
  Ini adalah kombinasi langsung dari fitur Manajemen Tugas (No. 2). Karena setiap aksi telah dibungkus menjadi objek *Command*, sistem cukup menyimpan *Command* tersebut ke dalam sebuah *Stack* (tumpukan riwayat). Jika user menekan tombol "Undo", sistem hanya perlu mengambil command terakhir dari tumpukan dan memanggil method `undo()` di mana command tersebut sudah memegang *snapshot* data sebelum aksi dilakukan.

### 9. Mode Tampilan (Light/Dark)
- **Aktor:** Semua Pengguna
- **Design Pattern:** Abstract Factory Pattern
- **Alasan Penggunaan:**
  Sistem membutuhkan rendering sekumpulan komponen UI yang harus konsisten (misal: *DarkButton*, *DarkPanel* untuk Dark Mode, dan *LightButton*, *LightPanel* untuk Light Mode). **Abstract Factory** sangat ideal untuk membuat *famili* (kelompok) objek yang saling terkait tanpa harus menyebutkan kelas konkretnya. Sehingga, ketika user mengganti tema, *Factory* akan langsung memproduksi komponen-komponen yang senada.

### 10. Notifikasi Multi-Channel
- **Aktor:** Sistem
- **Design Pattern:** Factory Method
- **Alasan Penggunaan:**
  Pengguna bisa memilih untuk menerima notifikasi lewat berbagai saluran (Email, Dashboard, atau mungkin WhatsApp/SMS). Ketimbang menggunakan banyak percabangan `if-else` untuk mengecek preferensi pengguna dan mengirim notifikasi, **Factory Method** mendelegasikan pembuatan objek channel notifikasi yang tepat sesuai preferensi tersebut di saat *runtime*.

### 11. Export Data (PDF/CSV)
- **Aktor:** Dosen / Admin
- **Design Pattern:** Strategy Pattern
- **Alasan Penggunaan:**
  Mirip dengan logika penyimpanan data, algoritma yang dibutuhkan untuk mengekspor data ke dalam bentuk **PDF** sangat jauh berbeda dengan algoritma untuk **CSV**. **Strategy Pattern** membungkus algoritma ekspor ini secara terpisah, sehingga *Client* (Controller) hanya perlu memanggil method `export()` dan *Context* (Pengekspor) yang akan menjalankan logikanya berdasarkan *Strategy* (PDF atau CSV) yang dipilih pengguna.

### 12. Autentikasi Bertingkat (OTP)
- **Aktor:** Semua Pengguna
- **Design Pattern:** Decorator Pattern
- **Alasan Penggunaan:**
  Sistem pada dasarnya sudah memiliki autentikasi standar (email & password) bernama `BasicAuth`. Fitur OTP adalah sebuah tambahan lapisan keamanan opsional. Jika kita memodifikasi `BasicAuth` secara langsung untuk memasukkan OTP, kita telah melanggar *Open/Closed Principle*. Dengan **Decorator Pattern**, kita "membungkus" `BasicAuth` dengan `OTPDecorator`. Ini memungkinkan kita menambahkan fungsionalitas OTP ke dalam proses autentikasi secara dinamis dan transparan, tanpa mengubah satu baris pun kode pada `BasicAuth` yang asli.