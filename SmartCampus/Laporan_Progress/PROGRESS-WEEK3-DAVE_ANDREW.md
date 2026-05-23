# PROGRESS WEEK 3 — SmartCampus

**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Anggota:** Dave Andrew (2172015)  
**Branch:** `week-3---Dave-Andrew`  
**Tanggal:** 23 Mei 2026

---

## 1. Gambaran Umum Fitur

Pada minggu ke-3, fokus pengembangan beralih ke otomatisasi penilaian dan pengelolaan notifikasi multi-channel. Fitur yang diimplementasikan adalah **Sistem Penilaian Otomatis (F5)** dan **Sistem Notifikasi Multi-Channel (F10)**.

### Apa yang dilakukan:

-   **Penilaian Otomatis:** Menggunakan kriteria yang ditentukan oleh dosen untuk menilai submission secara otomatis saat dikumpulkan (jika memenuhi syarat tertentu).
-   **Multi-Channel Notifikasi:** Memungkinkan sistem mengirimkan notifikasi melalui berbagai kanal (Database, Email).
-   **Integrasi:** Mengintegrasikan notifikasi dengan sistem autentikasi dan status tugas.

---

## 2. Design Pattern — Strategy Pattern (Fitur 5: Penilaian Otomatis)

Mengimplementasikan **Strategy Pattern** untuk menangani berbagai algoritma penilaian yang mungkin berbeda-beda untuk setiap jenis tugas.

### Mengapa Strategy Pattern?

-   **Algoritma yang Dapat Ditukar:** Dosen dapat memilih strategi penilaian (misal: `FixedScoreStrategy`, `AutomatedTestStrategy`) tanpa mengubah kode controller.
-   **Open/Closed Principle:** Menambahkan metode penilaian baru cukup dengan membuat kelas strategi baru.

### Struktur Kelas:

```
GradingStrategyInterface (Interface)
│
└── FixedScoreStrategy (Concrete Strategy)
└── AutomatedTestStrategy (Concrete Strategy)

GradingContext (Context)
└── Menggunakan strategi yang dipilih untuk menghitung nilai tugas
```

---

## 3. Design Pattern — Factory Method (Fitur 10: Notifikasi Multi-Channel)

Mengimplementasikan **Factory Method Pattern** untuk menangani pembuatan objek notifikasi berdasarkan channel yang dipilih.

### Mengapa Factory Method?

-   **Decoupling:** Kode klien tidak perlu tahu kelas konkret (EmailNotifier, DatabaseNotifier) yang digunakan.
-   **Scalability:** Menambahkan kanal notifikasi baru (seperti WhatsApp atau Slack) hanya memerlukan penambahan factory baru.

### Struktur Kelas:

```
NotificationFactory (Creator)
│
└── DatabaseNotificationFactory
└── EmailNotificationFactory

NotificationInterface (Product)
│
└── DatabaseNotifier
└── EmailNotifier
```

---

## 4. Implementasi Teknis

### Sistem Penilaian Otomatis:

Dibuat Service `GradingService` yang memanfaatkan `GradingContext` untuk mengeksekusi strategi penilaian berdasarkan konfigurasi `Assignment`.

### Sistem Notifikasi:

Sistem diperluas untuk mendukung pengiriman notifikasi via email (SMTP) menggunakan `EmailNotifier` yang diinstansiasi melalui `NotificationFactory`.

---

## 5. Struktur File yang Dibuat/Dimodifikasi

```
SmartCampus/
├── app/
│   ├── Contracts/
│   │   ├── GradingStrategyInterface.php   [NEW]
│   │   └── NotificationInterface.php      [NEW]
│   │
│   ├── Services/
│   │   ├── Grading/
│   │   │   ├── GradingContext.php         [NEW]
│   │   │   └── Strategies/                [NEW]
│   │   │       ├── FixedScoreStrategy.php
│   │   │       └── AutomatedTestStrategy.php
│   │   │
│   │   └── Notifications/
│   │       ├── NotificationFactory.php    [NEW]
│   │       └── Notifiers/                 [NEW]
│   │           ├── DatabaseNotifier.php
│   │           └── EmailNotifier.php
│
├── database/migrations/
│   └── add_grading_strategy_to_assignments.php [NEW]
│
└── routes/
    └── web.php                            [MODIFIED]
```

---

## 6. Testing & Verifikasi

-   ✅ `php artisan test` — Menjalankan unit test untuk `GradingService`.
-   ✅ Verifikasi Notifikasi — Memastikan notifikasi terkirim ke Database dan Email (via Mailtrap/Log).
-   ✅ Verifikasi Nilai — Memastikan `AutomatedTestStrategy` memberikan nilai yang benar berdasarkan file yang di-submit.

---

## 7. Ringkasan Design Pattern yang Digunakan

| Design Pattern       | Komponen              | Fungsi                                      |
| -------------------- | --------------------- | ------------------------------------------- |
| **Strategy Pattern** | `GradingService`      | Memilih algoritma penilaian saat runtime.   |
| **Factory Method**   | `NotificationFactory` | Membuat objek notifikasi berdasarkan kanal. |

---

## TODO — Week 4

-   [ ] Implementasi fitur Light/Dark Mode (F9).
-   [ ] Export data ke PDF/CSV (F11).
-   [ ] Integrasi fitur dari anggota tim lain.
