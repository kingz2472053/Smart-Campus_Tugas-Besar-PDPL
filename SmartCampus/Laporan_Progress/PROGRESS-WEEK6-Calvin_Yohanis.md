# 📈 LAPORAN PROGRES TOTAL — SmartCampus

**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Tim Pengembang:**

1. Dave Andrew (2172015)
2. Francisco Valentino (2472040)
3. Teofilus Juan Puapadang (2472053)
4. Calvin Yohanis (2272017)

---
# PROGRESS WEEK 6 — SmartCampus
**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Anggota:** Calvin Yohanis (2272017)  
**Branch:** `Week-6---Calvin-Yohanis`  
**Tanggal:** 13 Juni 2026

---

## 1. Gambaran Umum Fitur

Pada **Week 6**, fokus pengembangan diarahkan pada **Stabilisasi Antarmuka (UI/UX Optimization)**, khususnya integrasi visual *Dark Mode* secara menyeluruh pada komponen tabel riwayat data sistem menggunakan penegasan gaya (*style enforcement*). Langkah ini krusial untuk memastikan komponen pemantauan keamanan tetap memiliki tingkat keterbacaan (*readability*) yang tinggi di berbagai kondisi pencahayaan.

### Apa yang dilakukan:
- **Sinkronisasi Skema Warna Mode Gelap:** Memperbaiki degradasi warna teks pada komponen tabel Bootstrap agar kontras elemen anak (*child nodes*) tetap terjaga saat menggunakan tema gelap.
- **Peningkatan Kontras Alamat IP via `!important`:** Menerapkan penegasan gaya secara agresif pada data Alamat IP (*IP Address*) pelacak aktivitas untuk mempermudah audit keamanan oleh Administrator.
- **Refactoring Arsitektur CSS Tampilan:** Menyusun ulang aturan pewarnaan kustom agar terisolasi dengan baik dan tidak terdistorsi oleh utilitas class bawaan framework.

---

## 2. Design Pattern & Refactoring — UI Style Enforcement

Untuk menjamin konsistensi tampilan pada *Dark Mode*, dilakukan penerapan aturan penegasan gaya menggunakan properti deklaratif guna memastikan tidak ada kebocoran warna teks dari class bawaan Bootstrap.

### Mengapa Menggunakan Deklarasi `!important` pada Komponen ini?
- **Pencegahan Override Otomatis:** Memastikan bahwa class utilitas bawaan framework (seperti `.text-muted` atau `.small`) tidak menimpa warna teks kustom yang telah ditentukan saat komponen beralih ke mode gelap.
- **Konsistensi Visual Keamanan:** Data sensitif seperti Alamat IP harus dipastikan selalu terlihat menonjol dalam kondisi apa pun tanpa risiko terpengaruh oleh perubahan stylesheet eksternal.

### Struktur Hirarki Elemen Tampilan:
```text
html.dark (Akar Tema)
│
└── .table (Komponen Tabel Riwayat)
    ├── td, td *, .text-muted (Dipaksa Menjadi Putih Kontras Tinggi via !important)
    └── td:nth-child(4) (Kolom IP Address — Dipaksa Menjadi Merah Jelas via !important)
--
3. Implementasi Teknis — Penataan Gaya Antarmuka
Perbaikan diimplementasikan langsung pada file tata letak utama (app.blade.php) dengan mempertegas bobot spesifisitas menggunakan modifikasi deklarasi warna:

CSS
/* Menargetkan teks umum dalam tabel dark mode */
html.dark .table td,
html.dark .table td *,
html.dark .table .text-muted,
html.dark .table small {
    color: #F8FAFC !important; 
}

/* Memaksa IP Address menjadi merah pekat terang agar menonjol di background gelap */
html.dark .table td:nth-child(4), 
html.dark .table td:nth-child(4) *,
html.dark .table td:has(i.bi-laptop),
html.dark .table td:has(i.bi-laptop) * {
    color: #F87171 !important;
    font-weight: 600 !important;
}

/* Penyesuaian ikon pembantu */
html.dark .table td .bi-laptop {
    color: #F87171 !important;
}
4. Integrasi Dashboard & Tampilan Web
Optimasi antarmuka ini memberikan dampak visual instan pada kenyamanan pengguna:

Keterbacaan Log Aktivitas Tangguh: Seluruh informasi krusial pada tabel Activity Log kini memiliki kontras warna yang tepat di Dark Mode, mencegah teks tenggelam ke dalam latar belakang gelap.

Penyorotan Data Sensitif: Kolom Alamat IP kini otomatis dipaksa berwarna merah pekat terang (#F87171) berserta ketebalan font font-weight: 600, mempermudah proses pemantauan akses masuk akun.

Sidebar Khusus Mode Gelap: Informasi identitas pengguna (Nama dan Role) di area bawah sidebar dipastikan tetap menyala putih bersih tanpa terpengaruh penurunan warna elemen teks lainnya.

5. Struktur File yang Dibuat/Dimodifikasi
Plaintext
SmartCampus/
├── app/
│   // (Logika Repository & Command Pattern dari minggu sebelumnya tetap terjaga aman)
│
└── resources/views/layouts/
    └── app.blade.php                            [MODIFIED] Refactor CSS Dark Mode, penegasan warna tabel, & kustomisasi warna IP Address via !important
