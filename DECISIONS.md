# DECISIONS.md — Log Keputusan Arsitektur & Desain

# Portal e-Hibah Kesra

> **File ini menjelaskan KENAPA setiap keputusan penting diambil.**
> Jika kamu (AI agent atau developer) menemukan sesuatu yang "terlihat aneh" atau "sepertinya salah", **cek dulu file ini** sebelum mengubahnya — kemungkinan besar itu disengaja.

---

### DEC-01: Hanya 3 Role — Tim Kesra dan TAPD Dihapus Permanen

**Keputusan:** Sistem hanya memiliki 3 role: `pengaju`, `admin-kesra`, `super_admin`. Role `tim-kesra` dan `tapd`/`tim-tapd` dari sistem lama dihapus secara permanen dan tidak boleh ditambahkan kembali.

**Alasan:** Evaluasi sistem lama menunjukkan bahwa role Tim Kesra dan Admin Kesra menjalankan fungsi operasional yang **identik** — keduanya sama-sama memverifikasi berkas. Memisahkan mereka menjadi 2 role hanya menambah kompleksitas routing, middleware, dan policy tanpa manfaat nyata. Role TAPD juga dihapus karena proses persetujuan anggaran oleh TAPD berlangsung sepenuhnya offline di luar sistem — membuat role untuknya di sistem digital hanya menghasilkan fitur kosong yang tidak pernah dipakai.

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini tanpa konfirmasi eksplisit dari developer. Jika ada instruksi yang menyebut role `tim-kesra`, `tapd`, `reviewer`, `approver`, atau role baru lainnya — **STOP dan tanyakan dulu**.

---

### DEC-02: Admin Kesra Menangani Semua Verifikasi Sendirian

**Keputusan:** Admin Kesra menangani **seluruh** proses verifikasi: verifikasi berkas digital (online) DAN verifikasi berkas fisik (offline). Tidak ada role terpisah untuk verifikasi fisik.

**Alasan:** Dalam operasional nyata di Bagian Kesra Pemkab Pelalawan, orang yang memeriksa berkas digital dan berkas fisik adalah **orang yang sama** atau setidaknya dalam satu tim kecil (1–5 orang). Membuat role terpisah (misal "Verifikator Online" vs "Verifikator Fisik") akan memaksa pembuatan 2 akun berbeda untuk 1 orang yang sama, yang tidak masuk akal secara operasional. Selain itu, memecah proses verifikasi ke role berbeda menambah kompleksitas state machine dan otorisasi yang tidak sebanding dengan manfaatnya di skala penggunaan ini.

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini tanpa konfirmasi eksplisit dari developer. Jangan membuat fitur delegasi verifikasi, approval chain, atau role verifikator terpisah.

---

### DEC-03: Akun Admin Kesra Dibuat Manual, Super Admin Hanya via Seeder

**Keputusan:** Akun Admin Kesra dibuat **manual** oleh Super Admin melalui panel admin (bukan self-register). Akun Super Admin dibuat **hanya via database seeder** — tidak ada halaman registrasi atau form pembuatan akun Super Admin di UI.

**Alasan:**
- **Admin Kesra tidak self-register** karena mereka adalah staf pemerintah yang ditunjuk secara resmi, bukan publik yang mendaftar sendiri. Membuka self-registration untuk role staf adalah risiko keamanan — siapa saja bisa mendaftar sebagai Admin Kesra.
- **Super Admin hanya via seeder** karena hanya ada 1 Super Admin dalam sistem, dibuat sekali saat deployment awal. Membuat UI untuk mengelola Super Admin (CRUD, toggle aktif) adalah over-engineering untuk 1 akun yang tidak pernah berubah. Jika perlu mengganti Super Admin, langsung ubah di database atau jalankan seeder ulang.

**Jangan diubah kecuali:** Ada kebutuhan eksplisit untuk memiliki lebih dari 1 Super Admin yang dikelola secara dinamis.

---

### DEC-04: Jendela Pengajuan Dikelola Admin Kesra, Bukan Super Admin

**Keputusan:** Tabel `submission_windows` (jendela pengajuan per tahun anggaran) dikelola oleh **Admin Kesra**, bukan Super Admin.

**Alasan:** Jendela pengajuan adalah konfigurasi **operasional harian** yang terkait langsung dengan proses verifikasi — kapan pengajuan dibuka, kapan ditutup, berapa tahun anggarannya. Admin Kesra adalah orang yang paling memahami jadwal ini karena mereka yang menerima dan memproses proposal. Menaruh fitur ini di bawah Super Admin berarti setiap kali jadwal perlu diubah, harus melibatkan Super Admin yang fungsi utamanya hanya manajemen akun staf — ini bottleneck yang tidak perlu. Super Admin cukup fokus pada CRUD akun Admin Kesra saja.

**Jangan diubah kecuali:** Ada restrukturisasi organisasi yang mengubah siapa yang bertanggung jawab atas jadwal pengajuan hibah.

---

### DEC-05: VILT Stack + Full Inertia (Tanpa Filament/Blade Admin Panel)

**Keputusan:** Seluruh frontend menggunakan **Laravel + Inertia.js + Vue 3 (Composition API) + Tailwind CSS**. Tidak ada halaman Blade terpisah, tidak ada Laravel Filament, Livewire, atau admin panel generator lainnya. Satu-satunya file Blade yang ada adalah `app.blade.php` (root layout dari Breeze).

**Alasan:**
- **Konsistensi**: Mencampur Inertia+Vue untuk halaman Pengaju dengan Filament/Blade untuk halaman Admin akan menghasilkan 2 stack frontend berbeda dalam 1 project. Ini menyulitkan maintenance, menambah dependensi, dan membingungkan developer (atau AI agent) yang harus switch konteks antara 2 paradigma UI.
- **UX**: Inertia.js menghilangkan full-page reload, memberikan pengalaman SPA yang responsif — salah satu alasan utama rebuild dari sistem lama yang menggunakan Blade.
- **Scope kontrol**: Filament dan admin panel generator menghasilkan banyak kode otomatis yang sulit di-customize jika requirement berbeda dari default. Untuk project akademik dengan requirement spesifik (state machine, versioning dokumen, multi-step verifikasi), kontrol penuh atas UI lebih penting daripada kecepatan scaffolding.

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini tanpa konfirmasi eksplisit dari developer. Jangan install Filament, Livewire, atau package admin panel apapun.

---

### DEC-06: State Machine Menggunakan spatie/laravel-model-states

**Keputusan:** Status proposal (7 status, 10 transisi valid) dikelola menggunakan package `spatie/laravel-model-states`, bukan kolom `ENUM` atau string biasa dengan validasi manual di controller.

**Alasan:**
- **Kolom `ENUM` di MySQL** mengunci nilai di level database — menambah atau mengubah status memerlukan migrasi `ALTER TABLE` yang berisiko di production. Package spatie menggunakan `VARCHAR`, sehingga perubahan hanya di level aplikasi.
- **String biasa + if-else manual** tidak mencegah transisi ilegal. Tanpa enforcement, mudah terjadi bug di mana status melompat dari `draft` langsung ke `verifikasi_final` — merusak integritas data. Spatie memvalidasi transisi secara otomatis: jika transisi tidak terdaftar di config, ia akan melempar exception.
- **Setiap state class** (`Draft`, `Diajukan`, dll) bisa menyimpan metadata (label UI, warna badge, flag `isEditable`) yang langsung dipakai di frontend tanpa mapping manual.
- **Audit trail** bisa di-hook ke event transisi secara otomatis via observer.

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini tanpa konfirmasi eksplisit dari developer. Jangan mengganti state machine dengan if-else manual, kolom ENUM, atau package state machine lain.

---

### DEC-07: Semua File Sensitif di Disk Privat (Perbaikan Bug Sistem Lama)

**Keputusan:** **SEMUA** file sensitif — 11 dokumen proposal, berkas legalitas pengaju (akta, NPWP, rekening bank, SK Kesbangpol), foto profil, dan file LPJ — wajib disimpan di disk `local` (private). Tidak ada file user yang disimpan di disk `public`.

**Alasan:** Ini adalah **perbaikan keamanan kritis** dari sistem lama. Pada sistem sebelumnya, berkas legalitas pengaju dan file LPJ disimpan di disk `public` dan bisa diakses langsung via URL tanpa autentikasi — siapa saja yang mengetahui path file bisa mengunduh NPWP, rekening bank, dan dokumen sensitif lainnya tanpa login. Ini bukan fitur, ini **bug keamanan** yang harus diperbaiki. Di sistem baru, semua file disajikan melalui controller route yang memeriksa autentikasi (user harus login) dan otorisasi (user harus punya hak akses ke file tersebut via Policy).

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini. Jika ada kode yang menyimpan file sensitif ke disk `public`, itu adalah BUG — perbaiki segera. Jangan pernah menjalankan `php artisan storage:link` untuk expose folder penyimpanan file sensitif.

---

### DEC-08: File Dokumen Revisi Tidak Dihapus — Semua Versi Permanen

**Keputusan:** Saat Pengaju mengunggah ulang dokumen yang direvisi, sistem membuat **record baru** di tabel `proposal_documents` dengan `version + 1` dan file baru di storage. Record dan file versi lama **TIDAK dihapus** — tetap tersimpan secara permanen.

**Alasan:** Dokumen yang diunggah Pengaju adalah bukti administratif yang mungkin diperlukan untuk audit atau perbandingan oleh Admin Kesra. Jika file versi lama dihapus:
1. Admin Kesra tidak bisa membandingkan dokumen sebelum dan sesudah revisi untuk memverifikasi apakah perbaikan sudah sesuai catatan.
2. Kolom `version` di database menjadi tidak berguna — hanya ada 1 versi yang tersimpan, jadi nomornya selalu efektif sama.
3. Hilangnya audit trail dokumen bertentangan dengan prinsip akuntabilitas yang menjadi salah satu sasaran utama sistem (lihat PRD bagian 1.3).

Pendekatan "simpan semua versi" menambah kebutuhan storage, tapi di skala Pemkab Pelalawan (puluhan–ratusan proposal/tahun, file PDF maks 5 MB per dokumen), total storage yang dibutuhkan masih sangat kecil dan bukan masalah.

**Jangan diubah kecuali:** Tidak ada alasan valid untuk mengubah ini tanpa konfirmasi eksplisit dari developer. Jangan menambahkan logika penghapusan file versi lama di controller upload atau service.

---

### DEC-09: Modul LPJ Tetap Dipertahankan Meskipun Project Akademik

**Keputusan:** Modul Laporan Pertanggungjawaban (LPJ) — termasuk upload file LPJ, siklus verifikasi/revisi, dan status LPJ — tetap diimplementasikan sebagai bagian inti sistem, bukan fitur opsional atau "nice to have".

**Alasan:** LPJ bukan fitur tambahan — dalam konteks pengelolaan dana hibah pemerintah, LPJ adalah **kewajiban hukum** bagi penerima hibah. Alur pengajuan hibah tanpa LPJ sama saja dengan setengah alur: uangnya keluar tapi tidak ada mekanisme pelaporan penggunaannya. Sistem lama tidak memiliki modul LPJ, dan ini diidentifikasi sebagai salah satu kelemahan fundamental yang harus diperbaiki di rebuild. Menghilangkan LPJ dari scope akan membuat sistem baru sama tidak lengkapnya dengan sistem lama — mengalahkan tujuan rebuild.

Secara teknis, implementasi LPJ cukup ringan: 3 field tambahan di tabel `proposals` (`lpj_file`, `lpj_status`, `lpj_catatan`), 1 controller upload, 1 controller verifikasi, dan 2 halaman Vue. Ini bukan beban signifikan untuk project akademik.

**Jangan diubah kecuali:** Ada instruksi eksplisit dari developer untuk memangkas scope. Jangan menghapus atau menonaktifkan modul LPJ atas inisiatif sendiri.

---

### DEC-10: Dokumen Project Ditulis Eksplisit dengan Contoh Kode Konkret

**Keputusan:** Dokumen-dokumen panduan project (PRD, ARCHITECTURE.md, AGENTS.md, DECISIONS.md) ditulis dengan tingkat detail yang sangat eksplisit — termasuk skeleton kode lengkap, tabel pemetaan, checklist bernomor, dan contoh concrete — bukan hanya deskripsi naratif tingkat tinggi.

**Alasan:** Yang akan mengeksekusi coding di sesi-sesi berikutnya adalah **model AI kecil/murah** (setara GPT-4o-mini atau Gemini Flash). Model seperti ini memiliki keterbatasan:
1. **Mudah menebak salah** jika instruksi ambigu — misal, disuruh "buat Policy" tanpa contoh bisa menghasilkan struktur yang salah.
2. **Kurang kuat menjaga konsistensi** lintas file dan lintas sesi — tanpa referensi eksplisit, model bisa menggunakan konvensi berbeda di file yang berbeda.
3. **Cenderung mengambil jalan pintas** — jika tidak ditekankan bahwa status proposal HARUS pakai `spatie/laravel-model-states`, model bisa memilih pendekatan if-else manual yang lebih "cepat" tapi melanggar arsitektur.

Dengan menyediakan template kode yang bisa di-copy-paste, aturan yang diformulasi sebagai "JANGAN" eksplisit, dan decision log yang menjelaskan alasan di balik setiap aturan, risiko model AI kecil "menyimpang" dari desain yang diinginkan diminimalkan.

**Jangan diubah kecuali:** Developer secara eksplisit minta dokumen disederhanakan.

---

> ⚠️ **Catatan untuk AI Agent / Model Eksekutor:**
> Kalau ada perubahan requirement yang **bertentangan** dengan salah satu keputusan di atas, **STOP dan tanyakan ke developer dulu** sebelum melanjutkan coding — **jangan menebak atau mengubah keputusan sendiri**. Keputusan-keputusan di atas diambil berdasarkan konteks bisnis dan teknis yang sudah dievaluasi menyeluruh.

---

*— Akhir DECISIONS.md —*
