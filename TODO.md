# TODO.md — Breakdown Task Development

# Portal e-Hibah Kesra

> **Instruksi untuk developer:**
> - Kerjakan **1 task per sesi coding** agar mudah direview.
> - Setelah sebuah task selesai dikerjakan, ubah status jadi **✅ Selesai**.
> - Kerjakan task **sesuai urutan fase** — jangan loncat fase karena ada dependensi teknis.
> - Dalam 1 fase, task boleh dikerjakan urut atau paralel kecuali ada kolom "Dependensi" yang melarang.
> - Setiap task merujuk ke User Story (PRD) dan/atau Decision (DECISIONS.md) yang relevan — pastikan model eksekutor membaca rujukan tersebut sebelum mulai coding.

---

## Fase 0 — Setup Project & Fondasi

---

### T-001: Inisialisasi Project Laravel + Breeze (Inertia + Vue)Inisialisasi Project Laravel + Breeze (Inertia + Vue)

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** Tidak ada
**Rujukan:** DEC-05 (VILT Stack, tanpa Filament/Blade)

**Yang harus dikerjakan:**
1. Buat project Laravel baru (jika belum ada `composer.json`): `composer create-project laravel/laravel .`
2. Install Laravel Breeze: `composer require laravel/breeze --dev`
3. Install Breeze dengan variant **Vue + Inertia**: `php artisan breeze:install vue`
4. Install npm dependencies: `npm install`
5. Pastikan `npm run dev` dan `php artisan serve` berjalan tanpa error.

**File yang terlibat:**
- `composer.json`
- `package.json`
- `vite.config.js`
- `resources/js/app.js`
- `resources/views/app.blade.php`

**Kriteria Selesai:**
- [ ] `php artisan serve` berjalan tanpa error
- [ ] `npm run dev` berjalan tanpa error
- [ ] Halaman login Breeze muncul di browser (`http://localhost:8000/login`)

**Status:** ✅ Selesai

---

### T-002: Install Package Tambahan (spatie/laravel-model-states)

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-001
**Rujukan:** DEC-06 (State machine wajib pakai spatie)

**Yang harus dikerjakan:**
1. Install package: `composer require spatie/laravel-model-states`
2. Verifikasi package terdaftar di `composer.json`

**File yang terlibat:**
- `composer.json`
- `composer.lock`

**Kriteria Selesai:**
- [ ] `composer require spatie/laravel-model-states` berhasil tanpa error
- [ ] Package terdaftar di `composer.json` → `require`

**Status:** ✅ Selesai

---

### T-003: Konfigurasi .env dan Disk Privat

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-001
**Rujukan:** DEC-07 (Semua file sensitif di disk privat), AGENTS.md bagian 9

**Yang harus dikerjakan:**
1. Copy `.env.example` ke `.env` (jika belum ada)
2. Set `DB_DATABASE=ehibah_kesra`, `DB_USERNAME=root`, `DB_PASSWORD=` (kosong untuk Laragon)
3. Set `FILESYSTEM_DISK=local` — **WAJIB `local`, JANGAN `public`**
4. Set `QUEUE_CONNECTION=database`
5. Set `SESSION_DRIVER=database`
6. Set `APP_NAME="Portal e-Hibah Kesra"`
7. Generate key: `php artisan key:generate`
8. Buat database `ehibah_kesra` di MySQL (via phpMyAdmin atau CLI)

**File yang terlibat:**
- `.env`
- `.env.example`

**Kriteria Selesai:**
- [ ] `.env` berisi konfigurasi yang benar
- [ ] `FILESYSTEM_DISK=local` (bukan `public`)
- [ ] Database `ehibah_kesra` sudah dibuat
- [ ] `php artisan key:generate` berhasil

**Status:** ⬜ Belum Dikerjakan

---

### T-004: Migration — Tabel `roles`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-003
**Rujukan:** DEC-01 (Hanya 3 role), ARCHITECTURE.md bagian 3.2 tabel `roles`

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_roles_table`
2. Kolom: `id` (bigIncrements), `name` (string), `slug` (string, unique), timestamps
3. Jalankan migration: `php artisan migrate`

**File yang terlibat:**
- `database/migrations/xxxx_create_roles_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `roles` ada di database dengan kolom yang benar

**Status:** ⬜ Belum Dikerjakan

---

### T-005: Seeder — `RoleSeeder` (3 Role Saja)

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-004
**Rujukan:** DEC-01 (Hanya 3 role)

**Yang harus dikerjakan:**
1. Buat seeder: `php artisan make:seeder RoleSeeder`
2. Isi dengan TEPAT 3 role: `{id:1, name:'Pengaju', slug:'pengaju'}`, `{id:2, name:'Admin Kesra', slug:'admin-kesra'}`, `{id:3, name:'Super Admin', slug:'super_admin'}`
3. Daftarkan di `DatabaseSeeder.php`
4. Jalankan: `php artisan db:seed --class=RoleSeeder`

**File yang terlibat:**
- `database/seeders/RoleSeeder.php`
- `database/seeders/DatabaseSeeder.php`

**Kriteria Selesai:**
- [ ] `php artisan db:seed --class=RoleSeeder` berhasil
- [ ] Tabel `roles` berisi TEPAT 3 baris dengan slug `pengaju`, `admin-kesra`, `super_admin`

**Status:** ⬜ Belum Dikerjakan

---

### T-006: Migration — Modifikasi Tabel `users` (Tambah Field Institusional)

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-004
**Rujukan:** ARCHITECTURE.md bagian 3.2 tabel `users`

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration add_custom_fields_to_users_table`
2. Tambah kolom: `role_id` (foreignId → roles, NOT NULL), `created_by` (foreignId → users, NULLABLE), `is_active` (boolean, default true), `nama_ketua` (string, nullable), `no_wa` (string(30), nullable), `alamat` (text, nullable), `foto_profil` (string, nullable), `file_akta` (string, nullable), `file_kesbangpol` (string, nullable), `rekening_lembaga` (string, nullable), `npwp_lembaga` (string, nullable)
3. Tambah index pada `role_id` dan `is_active`
4. Jalankan: `php artisan migrate`

**File yang terlibat:**
- `database/migrations/xxxx_add_custom_fields_to_users_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `users` memiliki semua kolom baru

**Status:** ⬜ Belum Dikerjakan

---

### T-007: Migration — Tabel `organization_profiles`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-006
**Rujukan:** ARCHITECTURE.md bagian 3.2 tabel `organization_profiles`

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_organization_profiles_table`
2. Kolom: `id`, `user_id` (foreignId → users, UNIQUE, ON DELETE CASCADE), `organization_name` (string), `address` (text), `district` (string, nullable), `village` (string, nullable), `field_of_activity` (string, nullable), `chairman_name` (string, nullable), `secretary_name` (string, nullable), `treasurer_name` (string, nullable), `organization_phone` (string(30), nullable), `organization_email` (string, nullable), timestamps

**File yang terlibat:**
- `database/migrations/xxxx_create_organization_profiles_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `organization_profiles` ada dengan `user_id` UNIQUE

**Status:** ⬜ Belum Dikerjakan

---

### T-008: Migration — Tabel `submission_windows`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-003
**Rujukan:** ARCHITECTURE.md bagian 3.2 tabel `submission_windows`

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_submission_windows_table`
2. Kolom: `id`, `year` (smallInteger unsigned, UNIQUE), `open_date` (date), `close_date` (date), `is_active` (boolean, default true), timestamps

**File yang terlibat:**
- `database/migrations/xxxx_create_submission_windows_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `submission_windows` ada di database

**Status:** ⬜ Belum Dikerjakan

---

### T-009: Migration — Tabel `proposals`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-006, T-008
**Rujukan:** DEC-06 (status VARCHAR, bukan ENUM), ARCHITECTURE.md bagian 3.2 tabel `proposals`

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_proposals_table`
2. Kolom `status` harus bertipe `VARCHAR(255)` dengan default `'draft'` — **BUKAN ENUM** (requirement spatie/laravel-model-states)
3. Semua kolom sesuai ARCHITECTURE.md: `id`, `proposal_number` (unique), `user_id` (FK), `submission_window_id` (FK), `activity_title`, `activity_description` (nullable), `total_budget` (decimal 15,2), `execution_start_date` (nullable), `execution_end_date` (nullable), `status` (string, default 'draft'), `verified_by` (FK nullable), `submitted_at` (nullable), `verified_online_at` (nullable), `physical_docs_received_at` (nullable), `final_verified_at` (nullable), `rejected_at` (nullable), `lpj_file` (nullable), `lpj_status` (nullable), `lpj_catatan` (nullable), timestamps
4. Index pada `status`, composite `(user_id, submission_window_id)`

**File yang terlibat:**
- `database/migrations/xxxx_create_proposals_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Kolom `status` bertipe VARCHAR, BUKAN ENUM
- [ ] Foreign key ke `users` dan `submission_windows` terbuat

**Status:** ⬜ Belum Dikerjakan

---

### T-010: Migration — Tabel `document_types` + Seeder

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-003
**Rujukan:** PRD Lampiran A (11 dokumen wajib)

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_document_types_table`
2. Kolom: `id`, `name` (string), `slug` (string, unique), `description` (text, nullable), `sort_order` (integer, default 0), `is_required` (boolean, default true), timestamps
3. Buat seeder: `php artisan make:seeder DocumentTypeSeeder`
4. Seed 11 dokumen wajib sesuai PRD Lampiran A (dengan slug: `surat-permohonan`, `bukti-legalitas`, `akta-pendirian`, `rekening-bank`, `fotokopi-ktp`, `rab`, `npwp`, `sptjp`, `sptj-penggunaan`, `pakta-integritas`, `sk-domisili`)
5. Daftarkan di `DatabaseSeeder.php`

**File yang terlibat:**
- `database/migrations/xxxx_create_document_types_table.php`
- `database/seeders/DocumentTypeSeeder.php`
- `database/seeders/DatabaseSeeder.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil
- [ ] `php artisan db:seed --class=DocumentTypeSeeder` berhasil
- [ ] Tabel `document_types` berisi TEPAT 11 baris

**Status:** ⬜ Belum Dikerjakan

---

### T-011: Migration — Tabel `proposal_documents`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-009, T-010
**Rujukan:** DEC-08 (Semua versi disimpan permanen), ARCHITECTURE.md bagian 3.2

**Yang harus dikerjakan:**
1. Buat migration: `php artisan make:migration create_proposal_documents_table`
2. Kolom: `id`, `proposal_id` (FK → proposals, ON DELETE CASCADE), `document_type_id` (FK → document_types, ON DELETE RESTRICT), `file_path` (string), `original_filename` (string), `mime_type` (string(50)), `file_size` (unsignedInteger), `version` (unsignedSmallInteger, default 1), timestamps
3. Tambah UNIQUE constraint pada `(proposal_id, document_type_id, version)`
4. Index pada `proposal_id`

**File yang terlibat:**
- `database/migrations/xxxx_create_proposal_documents_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil
- [ ] UNIQUE constraint `(proposal_id, document_type_id, version)` terbuat

**Status:** ⬜ Belum Dikerjakan

---

### T-012: Migration — Tabel `document_verifications`, `revision_notes`, `proposal_status_logs`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-009, T-011
**Rujukan:** DEC-05 (Audit trail), ARCHITECTURE.md bagian 3.2

**Yang harus dikerjakan:**
1. Buat 3 migration:
   - `create_document_verifications_table`: `id`, `proposal_document_id` (FK), `verified_by` (FK → users), `verification_type` (string(20)), `status` (string(20)), `notes` (text, nullable), timestamps. Index pada `(proposal_document_id, verification_type)`.
   - `create_revision_notes_table`: `id`, `proposal_id` (FK, CASCADE), `created_by` (FK → users, RESTRICT), `notes` (text), `revision_type` (string(20)), timestamps. Index pada `proposal_id`.
   - `create_proposal_status_logs_table`: `id`, `proposal_id` (FK, CASCADE), `changed_by` (FK → users, SET NULL, nullable), `from_status` (string, nullable), `to_status` (string), `notes` (text, nullable), timestamps. Index pada `proposal_id`, `changed_by`.

**File yang terlibat:**
- `database/migrations/xxxx_create_document_verifications_table.php`
- `database/migrations/xxxx_create_revision_notes_table.php`
- `database/migrations/xxxx_create_proposal_status_logs_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil (seluruh migration dari T-004 s/d T-012)
- [ ] Ketiga tabel ada di database dengan FK yang benar

**Status:** ⬜ Belum Dikerjakan

---

### T-013: Buat Semua Model Eloquent + Relasi Dasar

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-012
**Rujukan:** ARCHITECTURE.md bagian 2.1 (daftar model), bagian 5.3 (ringkasan relasi)

**Yang harus dikerjakan:**
1. Buat model (jika belum ada): `Role`, `OrganizationProfile`, `Proposal`, `SubmissionWindow`, `DocumentType`, `ProposalDocument`, `DocumentVerification`, `RevisionNote`, `ProposalStatusLog`
2. Update model `User.php` yang sudah ada dari Breeze: tambah relasi `role()`, `organizationProfile()`, `proposals()`, `createdBy()`, `createdUsers()`. Tambah `$fillable` untuk field baru.
3. Definisikan relasi di setiap model sesuai ARCHITECTURE.md bagian 5.3.
4. **JANGAN** tambahkan cast `status` ke state class di `Proposal.php` di task ini — itu dilakukan di Fase 2 (T-017).

**File yang terlibat:**
- `app/Models/Role.php`
- `app/Models/User.php` (modify)
- `app/Models/OrganizationProfile.php`
- `app/Models/Proposal.php`
- `app/Models/SubmissionWindow.php`
- `app/Models/DocumentType.php`
- `app/Models/ProposalDocument.php`
- `app/Models/DocumentVerification.php`
- `app/Models/RevisionNote.php`
- `app/Models/ProposalStatusLog.php`

**Kriteria Selesai:**
- [ ] Semua model bisa di-instantiate tanpa error (`php artisan tinker` → `new App\Models\Role`)
- [ ] Relasi terdefinisi (misal `User::first()->role` tidak error)

**Status:** ⬜ Belum Dikerjakan

---

### T-014: Migration — Tabel `notifications` dan `sessions`

**Fase:** 0 — Setup Project & Fondasi
**Dependensi:** T-003
**Rujukan:** ARCHITECTURE.md bagian 3.2 tabel `notifications`

**Yang harus dikerjakan:**
1. Jalankan: `php artisan notifications:table` (buat migration notifications)
2. Jalankan: `php artisan session:table` (buat migration sessions, karena `SESSION_DRIVER=database`)
3. Jalankan: `php artisan migrate`

**File yang terlibat:**
- `database/migrations/xxxx_create_notifications_table.php`
- `database/migrations/xxxx_create_sessions_table.php`

**Kriteria Selesai:**
- [ ] `php artisan migrate` berhasil
- [ ] Tabel `notifications` dan `sessions` ada di database

**Status:** ⬜ Belum Dikerjakan

---

## Fase 1 — Autentikasi & Role

---

### T-015: Customisasi Registrasi Breeze (Field Institusional Pengaju)

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-006, T-005
**Rujukan:** US-AUTH-01 (Registrasi Pengaju), US-AUTH-07 (Auto-assign role pengaju)

**Yang harus dikerjakan:**
1. Edit `RegisteredUserController.php`: tambah field `nama_ketua`, `no_wa`, `alamat` ke validasi dan proses pembuatan user.
2. Otomatis assign `role_id = 1` (pengaju) dan `created_by = null` (self-registered).
3. Edit halaman Vue `Register.vue`: tambah input field untuk Nama Lembaga, Email, Nama Ketua, No. WhatsApp, Alamat, Password.
4. Buat `RegisterRequest.php` (Form Request) untuk validasi registrasi.

**File yang terlibat:**
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Requests/Auth/RegisterRequest.php` (baru)
- `resources/js/Pages/Auth/Register.vue`

**Kriteria Selesai:**
- [ ] Registrasi via form berhasil membuat user dengan `role_id = 1`
- [ ] Field `nama_ketua`, `no_wa`, `alamat` tersimpan di database
- [ ] Email verifikasi terkirim (atau tercatat di log jika `MAIL_MAILER=log`)

**Status:** ⬜ Belum Dikerjakan

---

### T-016: Buat Middleware CheckRole + Registrasi

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-013
**Rujukan:** AGENTS.md bagian 4.5 (skeleton CheckRole)

**Yang harus dikerjakan:**
1. Buat file `app/Http/Middleware/CheckRole.php` — gunakan skeleton dari AGENTS.md bagian 4.5 (copy persis).
2. Daftarkan middleware alias `role` di `bootstrap/app.php`.
3. Test manual: akses route dengan middleware `role:pengaju` sebagai user non-pengaju → harus 403.

**File yang terlibat:**
- `app/Http/Middleware/CheckRole.php` (baru)
- `bootstrap/app.php` (modify)

**Kriteria Selesai:**
- [ ] Middleware `role:pengaju` memblokir akses user non-pengaju (return 403)
- [ ] Middleware `role:admin-kesra` memblokir akses user non-admin-kesra

**Status:** ⬜ Belum Dikerjakan

---

### T-017: Konfigurasi Redirect Post-Login per Role

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-016
**Rujukan:** US-AUTH-03 (Login), US-AUTH-06 (Root redirect), ARCHITECTURE.md bagian 5.5

**Yang harus dikerjakan:**
1. Edit `AuthenticatedSessionController.php` (atau buat middleware): setelah login, redirect ke dashboard sesuai role (`/pengaju/dashboard`, `/admin-kesra/dashboard`, `/super-admin/dashboard`).
2. Tambah route di `web.php`: `Route::get('/', fn () => redirect()->route('login'))` — root redirect ke login.
3. Pastikan user yang belum login diarahkan ke `/login`.

**File yang terlibat:**
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (modify)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Login sebagai Pengaju → redirect ke `/pengaju/dashboard`
- [ ] Login sebagai Admin Kesra → redirect ke `/admin-kesra/dashboard`
- [ ] Akses `/` tanpa login → redirect ke `/login`

**Status:** ⬜ Belum Dikerjakan

---

### T-018: Seeder — SuperAdmin + SubmissionWindow Awal

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-006, T-005, T-008
**Rujukan:** DEC-03 (Super Admin hanya via seeder), US-SW-01

**Yang harus dikerjakan:**
1. Buat `SuperAdminSeeder.php`: buat 1 user dengan `role_id = 3`, email `admin@ehibah-kesra.test`, password `password`.
2. Buat `SubmissionWindowSeeder.php`: buat 1 entri jendela pengajuan tahun 2026, `open_date = 2026-01-01`, `close_date = 2026-05-31`, `is_active = true`.
3. Daftarkan kedua seeder di `DatabaseSeeder.php`.

**File yang terlibat:**
- `database/seeders/SuperAdminSeeder.php` (baru)
- `database/seeders/SubmissionWindowSeeder.php` (baru)
- `database/seeders/DatabaseSeeder.php` (modify)

**Kriteria Selesai:**
- [ ] `php artisan db:seed` berhasil tanpa error
- [ ] User Super Admin bisa login
- [ ] Tabel `submission_windows` berisi 1 entri tahun 2026

**Status:** ⬜ Belum Dikerjakan

---

### T-019: Buat Route Groups per Role + Layout Vue per Role

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-016, T-017
**Rujukan:** AGENTS.md bagian 10 (Quick Reference Route), ARCHITECTURE.md bagian 2.2

**Yang harus dikerjakan:**
1. Buat route groups di `routes/web.php` untuk 3 role: Pengaju (`/pengaju`), Admin Kesra (`/admin-kesra`), Super Admin (`/super-admin`). Masing-masing dengan middleware `role:` yang sesuai. Untuk sementara, buat hanya route dashboard per role.
2. Buat 3 layout Vue: `PengajuLayout.vue`, `AdminKesraLayout.vue`, `SuperAdminLayout.vue` — masing-masing extends `AuthenticatedLayout.vue` dengan sidebar menu berbeda.
3. Buat 3 halaman dashboard kosong: `Pages/Pengaju/Dashboard.vue`, `Pages/AdminKesra/Dashboard.vue`, `Pages/SuperAdmin/Dashboard.vue`.
4. Buat 3 controller dashboard minimal yang return `Inertia::render(...)`.

**File yang terlibat:**
- `routes/web.php` (modify)
- `resources/js/Layouts/PengajuLayout.vue` (baru)
- `resources/js/Layouts/AdminKesraLayout.vue` (baru)
- `resources/js/Layouts/SuperAdminLayout.vue` (baru)
- `resources/js/Pages/Pengaju/Dashboard.vue` (baru)
- `resources/js/Pages/AdminKesra/Dashboard.vue` (baru)
- `resources/js/Pages/SuperAdmin/Dashboard.vue` (baru)
- `app/Http/Controllers/Pengaju/DashboardController.php` (baru)
- `app/Http/Controllers/AdminKesra/DashboardController.php` (baru)
- `app/Http/Controllers/SuperAdmin/DashboardController.php` (baru)

**Kriteria Selesai:**
- [ ] Login Pengaju → lihat dashboard Pengaju (dengan layout Pengaju)
- [ ] Login Admin Kesra → lihat dashboard Admin Kesra
- [ ] Pengaju tidak bisa akses `/admin-kesra/*` (403)
- [ ] Admin Kesra tidak bisa akses `/pengaju/*` (403)

**Status:** ⬜ Belum Dikerjakan

---

### T-020: Profil Pengaju — Halaman Edit Profil Organisasi

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-019, T-007
**Rujukan:** US-PROF-01, US-PROF-02

**Yang harus dikerjakan:**
1. Buat `Pengaju\ProfileController.php` dengan method `edit` dan `update`.
2. Buat `UpdateProfileRequest.php` untuk validasi.
3. Buat halaman Vue `Pages/Pengaju/Profile/Edit.vue` — form edit profil organisasi (nama lembaga, alamat, bidang kegiatan, nama pengurus) + upload berkas legalitas (foto_profil, file_akta, file_kesbangpol, rekening_lembaga, npwp_lembaga).
4. Upload file legalitas ke disk `local` (private) di folder `profiles/{user_id}/`.
5. Tambah route di group Pengaju.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/ProfileController.php` (baru)
- `app/Http/Requests/Pengaju/UpdateProfileRequest.php` (baru)
- `resources/js/Pages/Pengaju/Profile/Edit.vue` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Pengaju bisa update profil organisasi (data tersimpan di `organization_profiles`)
- [ ] Pengaju bisa upload berkas legalitas (file tersimpan di `storage/app/private/profiles/{user_id}/`)
- [ ] File TIDAK tersimpan di disk `public`

**Status:** ⬜ Belum Dikerjakan

---

### T-021: Verifikasi & Konfigurasi Verifikasi Email

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-015
**Rujukan:** US-AUTH-02 (Verifikasi email + aktivasi akun)

**Yang harus dikerjakan:**
1. Pastikan model `User` implement interface `MustVerifyEmail` (`implements MustVerifyEmail` di class declaration).
2. Pastikan middleware `verified` diterapkan ke **semua** route group yang butuh akun aktif — yaitu route group Pengaju (`role:pengaju`), Admin Kesra (`role:admin-kesra`), dan Super Admin (`role:super_admin`) di `routes/web.php`. Cek bahwa middleware ditulis `['auth', 'verified']`, bukan hanya `['auth']`.
3. Pastikan halaman `VerifyEmail.vue` bawaan Breeze berfungsi: user baru yang belum verifikasi harus diarahkan ke halaman ini saat mencoba akses route terproteksi.
4. Pastikan link verifikasi di email (atau di log jika `MAIL_MAILER=log`) bisa diklik dan mengisi `email_verified_at` di database.
5. Pastikan user yang sudah verifikasi bisa akses dashboard tanpa hambatan.

**File yang terlibat:**
- `app/Models/User.php` (modify — pastikan `implements MustVerifyEmail`)
- `routes/web.php` (modify — pastikan `'verified'` ada di middleware group)
- `resources/js/Pages/Auth/VerifyEmail.vue` (verifikasi tampil dengan benar)

**Kriteria Selesai:**
- [ ] User baru daftar → diarahkan ke halaman "Verifikasi Email" jika belum verifikasi
- [ ] User yang belum verifikasi TIDAK BISA akses halaman dashboard (redirect ke halaman verifikasi)
- [ ] Setelah klik link verifikasi (via email log atau Mailtrap) → `email_verified_at` terisi di database
- [ ] User yang sudah verifikasi → bisa akses dashboard tanpa masalah

**Status:** ⬜ Belum Dikerjakan

---

### T-022: Test & Verifikasi Alur Reset Password

**Fase:** 1 — Autentikasi & Role
**Dependensi:** T-015, T-006
**Rujukan:** US-AUTH-04 (Reset password)

**Yang harus dikerjakan:**
1. Pastikan route `forgot-password` dan `reset-password` bawaan Breeze masih berfungsi setelah model `User` dimodifikasi dengan field custom (`role_id`, `nama_ketua`, dll). Buka halaman `ForgotPassword.vue` dan `ResetPassword.vue` — pastikan tidak error karena perubahan skema `users`.
2. Test manual: isi form "Lupa Password" dengan email valid → link reset terkirim (cek di log jika `MAIL_MAILER=log`).
3. Buat 1 Feature Test: `tests/Feature/Auth/PasswordResetTest.php` dengan minimal 3 test case:
   - Test kirim link reset password ke email valid → response sukses, notification terkirim.
   - Test reset password dengan token valid → password berubah, user bisa login dengan password baru.
   - Test reset password dengan token invalid/expired → ditolak, password tidak berubah.
4. Gunakan `RefreshDatabase` trait dan seed roles di `setUp()`.

**File yang terlibat:**
- `tests/Feature/Auth/PasswordResetTest.php` (baru)
- `resources/js/Pages/Auth/ForgotPassword.vue` (verifikasi tidak error)
- `resources/js/Pages/Auth/ResetPassword.vue` (verifikasi tidak error)

**Kriteria Selesai:**
- [ ] Form "Lupa Password" berhasil mengirim email/link reset (tercatat di log jika `MAIL_MAILER=log`)
- [ ] Reset password dengan token valid berhasil mengubah password di database
- [ ] Reset password dengan token invalid/expired → ditolak
- [ ] `php artisan test --filter=PasswordResetTest` — semua lulus

**Status:** ⬜ Belum Dikerjakan

---

## Fase 2 — State Machine Proposal

---

### T-023: Buat Base State Class + 7 Concrete State Classes

**Fase:** 2 — State Machine Proposal
**Dependensi:** T-002, T-013
**Rujukan:** DEC-06 (State machine spatie), AGENTS.md bagian 4.3 (skeleton lengkap)

**Yang harus dikerjakan:**
1. Buat folder `app/States/ProposalStatus/`
2. Buat `ProposalStatusState.php` (abstract base class) — **copy dari skeleton AGENTS.md bagian 4.3** persis.
3. Buat 7 concrete state class: `Draft`, `Diajukan`, `VerifikasiOnline`, `PerluRevisi`, `MenungguBerkasFisik`, `VerifikasiFinal`, `Ditolak` — sesuai tabel AGENTS.md bagian 4.3.
4. Pastikan setiap class punya `$name`, `label()`, `badgeColor()`, `isEditable()`, `isTerminal()`.

**File yang terlibat:**
- `app/States/ProposalStatus/ProposalStatusState.php` (baru)
- `app/States/ProposalStatus/Draft.php` (baru)
- `app/States/ProposalStatus/Diajukan.php` (baru)
- `app/States/ProposalStatus/VerifikasiOnline.php` (baru)
- `app/States/ProposalStatus/PerluRevisi.php` (baru)
- `app/States/ProposalStatus/MenungguBerkasFisik.php` (baru)
- `app/States/ProposalStatus/VerifikasiFinal.php` (baru)
- `app/States/ProposalStatus/Ditolak.php` (baru)

**Kriteria Selesai:**
- [ ] Semua 8 file terbuat tanpa syntax error
- [ ] Config transisi di base class mendefinisikan TEPAT 10 transisi valid

**Status:** ⬜ Belum Dikerjakan

---

### T-024: Konfigurasi Model Proposal dengan HasStates

**Fase:** 2 — State Machine Proposal
**Dependensi:** T-023
**Rujukan:** DEC-06, AGENTS.md bagian 4.3

**Yang harus dikerjakan:**
1. Edit `app/Models/Proposal.php`: tambah `use HasStates` trait.
2. Tambah cast: `'status' => ProposalStatusState::class`.
3. Test di tinker: `$p = new Proposal; $p->status` harus return instance `Draft`.

**File yang terlibat:**
- `app/Models/Proposal.php` (modify)

**Kriteria Selesai:**
- [ ] `Proposal::create([...])` otomatis set status ke `Draft`
- [ ] `$proposal->status->canTransitionTo(Diajukan::class)` return `true`
- [ ] `$proposal->status->canTransitionTo(VerifikasiFinal::class)` return `false` (transisi ilegal)

**Status:** ⬜ Belum Dikerjakan

---

### T-025: Buat ProposalObserver untuk Audit Trail Otomatis

**Fase:** 2 — State Machine Proposal
**Dependensi:** T-024
**Rujukan:** DEC-05 (Audit trail), ARCHITECTURE.md bagian 4.3

**Yang harus dikerjakan:**
1. Buat `app/Observers/ProposalObserver.php`.
2. Di method `updating()`: jika kolom `status` berubah (dirty), buat record baru di `proposal_status_logs` dengan `proposal_id`, `changed_by` (auth user), `from_status`, `to_status`.
3. Daftarkan observer di `AppServiceProvider::boot()` atau via attribute `#[ObservedBy]`.
4. Test: ubah status proposal → cek `proposal_status_logs` terisi.

**File yang terlibat:**
- `app/Observers/ProposalObserver.php` (baru)
- `app/Providers/AppServiceProvider.php` (modify)

**Kriteria Selesai:**
- [ ] Transisi status proposal otomatis membuat record di `proposal_status_logs`
- [ ] Record log berisi `from_status`, `to_status`, `changed_by`, `proposal_id` yang benar

**Status:** ⬜ Belum Dikerjakan

---

### T-026: Buat Vue Composable `useProposalStatus`

**Fase:** 2 — State Machine Proposal
**Dependensi:** T-023
**Rujukan:** ARCHITECTURE.md bagian 2.2 (Composables)

**Yang harus dikerjakan:**
1. Buat `resources/js/Composables/useProposalStatus.js`: export fungsi yang menerima status string dan return `{ label, badgeColor, isEditable, isTerminal }`.
2. Mapping 7 status ke label dan warna Tailwind CSS.
3. Buat komponen `resources/js/Components/proposal/StatusBadge.vue` yang menggunakan composable ini.

**File yang terlibat:**
- `resources/js/Composables/useProposalStatus.js` (baru)
- `resources/js/Components/proposal/StatusBadge.vue` (baru)

**Kriteria Selesai:**
- [ ] `useProposalStatus('draft')` return `{ label: 'Draft', badgeColor: 'bg-gray-100 text-gray-700', ... }`
- [ ] `StatusBadge` component render badge dengan warna yang benar

**Status:** ⬜ Belum Dikerjakan

---

## Fase 3 — Manajemen Proposal (Pengaju)

---

### T-027: Buat ProposalPolicy

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-024
**Rujukan:** AGENTS.md bagian 4.2 (skeleton Policy), ARCHITECTURE.md bagian 5.6

**Yang harus dikerjakan:**
1. Buat `app/Policies/ProposalPolicy.php` dengan method: `viewAny`, `view`, `create`, `update`, `submit`.
2. Otorisasi sesuai ARCHITECTURE.md bagian 5.6 (Pengaju lihat milik sendiri, Admin Kesra lihat semua, create hanya Pengaju, update hanya draft/revisi).
3. Method `create`: cek jendela pengajuan aktif + maks 1 proposal aktif per tahun.

**File yang terlibat:**
- `app/Policies/ProposalPolicy.php` (baru)

**Kriteria Selesai:**
- [ ] Pengaju bisa create proposal hanya jika jendela aktif dan belum punya proposal aktif tahun ini
- [ ] Pengaju hanya bisa update proposal berstatus editable (draft/perlu_revisi)
- [ ] Admin Kesra bisa view semua proposal

**Status:** ⬜ Belum Dikerjakan

---

### T-028: Buat ProposalService (Generate Nomor + Cek Jendela)

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-024, T-008
**Rujukan:** US-PROP-09, US-PROP-10, US-PROP-11

**Yang harus dikerjakan:**
1. Buat `app/Services/ProposalService.php`.
2. Method `generateProposalNumber(int $year): string` — format `HIBAH-{YEAR}-{UNIX_TIMESTAMP}`.
3. Method `getActiveWindow(): ?SubmissionWindow` — return jendela pengajuan aktif saat ini (cek `is_active`, `open_date`, `close_date`, tanggal hari ini).
4. Method `hasActiveProposalThisYear(User $user, int $year): bool` — cek apakah user sudah punya proposal aktif (non-terminal) di tahun ini.

**File yang terlibat:**
- `app/Services/ProposalService.php` (baru)

**Kriteria Selesai:**
- [ ] `generateProposalNumber(2026)` return string format `HIBAH-2026-{timestamp}`
- [ ] `getActiveWindow()` return SubmissionWindow jika dalam periode aktif, null jika di luar
- [ ] `hasActiveProposalThisYear()` return true jika sudah ada proposal aktif

**Status:** ⬜ Belum Dikerjakan

---

### T-029: Buat ProposalController — Index + Create + Store

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-027, T-028, T-019
**Rujukan:** US-PROP-01, US-PROP-02, US-PROP-09, US-PROP-10

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Pengaju/ProposalController.php`.
2. Method `index()`: tampilkan daftar proposal milik user yang login.
3. Method `create()`: tampilkan form buat proposal (cek policy `create`).
4. Method `store()`: validasi via `StoreProposalRequest`, buat proposal dengan status `draft`, generate nomor via `ProposalService`.
5. Buat `StoreProposalRequest.php` (Form Request).
6. Tambah route resource di group Pengaju.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/ProposalController.php` (baru)
- `app/Http/Requests/Pengaju/StoreProposalRequest.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Pengaju bisa membuat proposal baru (tersimpan di DB dengan status `draft`)
- [ ] Nomor proposal tergenerate otomatis
- [ ] Tidak bisa buat proposal di luar jendela pengajuan

**Status:** ⬜ Belum Dikerjakan

---

### T-030: Buat Halaman Vue — Proposal Index + Create

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-029, T-026
**Rujukan:** US-PROP-01, US-PROP-07

**Yang harus dikerjakan:**
1. Buat `Pages/Pengaju/Proposals/Index.vue`: tabel daftar proposal sendiri dengan kolom: nomor, judul, status (badge), tanggal, aksi.
2. Buat `Pages/Pengaju/Proposals/Create.vue`: form input: judul kegiatan, deskripsi, total anggaran, tanggal mulai/selesai pelaksanaan.
3. Gunakan `StatusBadge` component dari T-026.

**File yang terlibat:**
- `resources/js/Pages/Pengaju/Proposals/Index.vue` (baru)
- `resources/js/Pages/Pengaju/Proposals/Create.vue` (baru)

**Kriteria Selesai:**
- [ ] Halaman Index menampilkan daftar proposal milik user
- [ ] Form Create bisa disubmit dan redirect ke Index setelah berhasil
- [ ] Status ditampilkan sebagai badge berwarna

**Status:** ⬜ Belum Dikerjakan

---

### T-031: Buat ProposalController — Show + Edit + Update

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-029
**Rujukan:** US-PROP-02, US-PROP-04, US-PROP-06

**Yang harus dikerjakan:**
1. Tambah method `show()` di ProposalController: tampilkan detail proposal + daftar dokumen + checklist kelengkapan.
2. Tambah method `edit()` dan `update()`: hanya bisa saat status editable (cek policy).
3. Buat `UpdateProposalRequest.php`.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/ProposalController.php` (modify)
- `app/Http/Requests/Pengaju/UpdateProposalRequest.php` (baru)

**Kriteria Selesai:**
- [ ] Halaman Show menampilkan detail proposal + checklist dokumen (✅/❌)
- [ ] Edit hanya bisa dilakukan saat status draft/perlu_revisi
- [ ] Edit saat status selain draft/perlu_revisi → 403

**Status:** ⬜ Belum Dikerjakan

---

### T-032: Buat Halaman Vue — Proposal Show + Edit

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-031
**Rujukan:** US-PROP-04, US-PROP-06

**Yang harus dikerjakan:**
1. Buat `Pages/Pengaju/Proposals/Show.vue`: detail proposal, checklist dokumen (11 item ✅/❌), tombol upload dokumen, tombol submit (jika semua dokumen lengkap).
2. Buat `Pages/Pengaju/Proposals/Edit.vue`: form edit (hanya saat editable).
3. Buat `Components/proposal/DocumentChecklist.vue`: komponen checklist 11 dokumen.

**File yang terlibat:**
- `resources/js/Pages/Pengaju/Proposals/Show.vue` (baru)
- `resources/js/Pages/Pengaju/Proposals/Edit.vue` (baru)
- `resources/js/Components/proposal/DocumentChecklist.vue` (baru)

**Kriteria Selesai:**
- [ ] Halaman Show menampilkan checklist 11 dokumen dengan status (✅ uploaded / ❌ belum)
- [ ] Tombol Submit hanya muncul jika semua 11 dokumen sudah ada

**Status:** ⬜ Belum Dikerjakan

---

### T-033: Buat FileController (Serve File Privat)

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-013
**Rujukan:** DEC-07 (Disk privat), AGENTS.md bagian 4.6, ARCHITECTURE.md bagian 6.4

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/FileController.php` dengan 3 method: `showProposalDocument()`, `showProfileFile()`, `showLpj()`.
2. Setiap method: cek auth + policy → serve file dari disk `local` menggunakan `Storage::disk('local')->download()` atau `Storage::disk('local')->response()`.
3. Buat `ProposalDocumentPolicy.php` dengan method `download`.
4. Tambah route di group shared.

**File yang terlibat:**
- `app/Http/Controllers/FileController.php` (baru)
- `app/Policies/ProposalDocumentPolicy.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Pengaju bisa download dokumen milik sendiri
- [ ] Admin Kesra bisa download semua dokumen proposal
- [ ] User lain (bukan pemilik/admin) → 403
- [ ] File di-serve dari disk `local`, BUKAN disk `public`

**Status:** ⬜ Belum Dikerjakan

---

### T-034: Buat DocumentUploadController + Upload Dokumen Proposal

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-033, T-011
**Rujukan:** US-PROP-03, DEC-07 (Disk privat), DEC-08 (Versioning permanen)

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Pengaju/DocumentUploadController.php` dengan method `store()`.
2. Buat `UploadDocumentRequest.php`: validasi `document_type_id` (harus valid), `document` (file, mimes: pdf,jpg,jpeg,png, max: 5120 KB).
3. Logic upload: simpan file ke `storage/app/private/proposals/{year}/{user_id}/{slug}_v{version}_{random}.{ext}`.
4. Versioning: cari versi terakhir dokumen ini untuk proposal ini, buat record baru dengan `version + 1`. **JANGAN hapus versi lama.**
5. Tambah route di group Pengaju.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/DocumentUploadController.php` (baru)
- `app/Http/Requests/Pengaju/UploadDocumentRequest.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Upload PDF/JPG/PNG berhasil tersimpan di `storage/app/private/proposals/...`
- [ ] File > 5 MB ditolak
- [ ] Upload ulang dokumen yang sama → record baru dengan `version + 1`, file lama TETAP ADA
- [ ] File TIDAK tersimpan di `storage/app/public/`

**Status:** ⬜ Belum Dikerjakan

---

### T-035: Buat Komponen Vue FileUpload + Integrasi di Show Proposal

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-034, T-032
**Rujukan:** US-PROP-03, US-PROP-04

**Yang harus dikerjakan:**
1. Buat `Components/ui/FileUpload.vue`: komponen upload file reusable (drag & drop atau click to browse), tampilkan nama file yang sudah diupload, progress indicator.
2. Integrasikan di `Pages/Pengaju/Proposals/Show.vue`: per document type, tampilkan FileUpload + status upload + link download.

**File yang terlibat:**
- `resources/js/Components/ui/FileUpload.vue` (baru)
- `resources/js/Pages/Pengaju/Proposals/Show.vue` (modify)

**Kriteria Selesai:**
- [ ] Pengaju bisa upload dokumen per jenis dari halaman Show
- [ ] Setelah upload berhasil, checklist diupdate (✅)
- [ ] Dokumen yang sudah diupload bisa didownload via link

**Status:** ⬜ Belum Dikerjakan

---

### T-036: Implementasi Submit Proposal (Draft → Diajukan)

**Fase:** 3 — Manajemen Proposal
**Dependensi:** T-034, T-024
**Rujukan:** US-PROP-05 (Submit hanya jika 11 dokumen lengkap), US-VER-08 (Resubmit)

**Yang harus dikerjakan:**
1. Tambah method `submit()` di `ProposalController`: cek semua 11 document types sudah ada (versi terbaru), lalu transisi status `Draft → Diajukan` via `$proposal->status->transitionTo(Diajukan::class)`.
2. Set `submitted_at` ke `now()` saat submit pertama kali.
3. Tambah method `resubmit()` untuk transisi `PerluRevisi → Diajukan`.
4. Tambah route `POST proposals/{proposal}/submit` dan `POST proposals/{proposal}/resubmit`.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/ProposalController.php` (modify)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Submit hanya berhasil jika 11 dokumen lengkap
- [ ] Submit tanpa 11 dokumen lengkap → error/redirect back dengan pesan
- [ ] Status berubah ke `diajukan`, `submitted_at` terisi
- [ ] Record baru muncul di `proposal_status_logs`

**Status:** ⬜ Belum Dikerjakan

---

## Fase 4 — Manajemen Jendela Pengajuan (Admin Kesra)

---

### T-037: Buat SubmissionWindowController + CRUD

**Fase:** 4 — Manajemen Jendela Pengajuan
**Dependensi:** T-019
**Rujukan:** US-SW-01, US-SW-02, US-SW-03, DEC-04

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/AdminKesra/SubmissionWindowController.php` dengan method: `index`, `create`, `store`, `edit`, `update`.
2. Buat `StoreSubmissionWindowRequest.php`: validasi `year` (required, integer, unique), `open_date` (required, date), `close_date` (required, date, after: open_date).
3. Tambah route resource di group Admin Kesra (except: show, destroy).

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/SubmissionWindowController.php` (baru)
- `app/Http/Requests/AdminKesra/StoreSubmissionWindowRequest.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Admin Kesra bisa buat, edit, aktifkan/nonaktifkan jendela pengajuan
- [ ] Validasi: tidak bisa buat 2 jendela untuk tahun yang sama
- [ ] Pengaju TIDAK bisa akses route ini (403)

**Status:** ⬜ Belum Dikerjakan

---

### T-038: Buat Halaman Vue — SubmissionWindow Index + Create + Edit

**Fase:** 4 — Manajemen Jendela Pengajuan
**Dependensi:** T-037
**Rujukan:** US-SW-01, US-SW-02, US-SW-03

**Yang harus dikerjakan:**
1. Buat `Pages/AdminKesra/SubmissionWindows/Index.vue`: tabel daftar jendela pengajuan (tahun, tanggal buka, tanggal tutup, status aktif, aksi edit).
2. Buat `Pages/AdminKesra/SubmissionWindows/Create.vue`: form buat jendela baru.
3. Buat `Pages/AdminKesra/SubmissionWindows/Edit.vue`: form edit + toggle aktif/nonaktif.

**File yang terlibat:**
- `resources/js/Pages/AdminKesra/SubmissionWindows/Index.vue` (baru)
- `resources/js/Pages/AdminKesra/SubmissionWindows/Create.vue` (baru)
- `resources/js/Pages/AdminKesra/SubmissionWindows/Edit.vue` (baru)

**Kriteria Selesai:**
- [ ] Admin Kesra bisa lihat daftar, buat baru, dan edit jendela pengajuan
- [ ] Toggle aktif/nonaktif berfungsi

**Status:** ⬜ Belum Dikerjakan

---

## Fase 5 — Verifikasi Proposal (Admin Kesra)

---

### T-039: Buat VerificationController — Index (Daftar Proposal Diajukan)

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-019, T-024
**Rujukan:** US-VER-01

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/AdminKesra/VerificationController.php`.
2. Method `index()`: query proposal berstatus `diajukan` dan `verifikasi_online`, tampilkan dengan Inertia.
3. Tambah route di group Admin Kesra.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/VerificationController.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Halaman menampilkan daftar proposal berstatus `diajukan` dan `verifikasi_online`

**Status:** ⬜ Belum Dikerjakan

---

### T-040: Buat Halaman Vue — Verification Index

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-039
**Rujukan:** US-VER-01

**Yang harus dikerjakan:**
1. Buat `Pages/AdminKesra/Verification/Index.vue`: tabel daftar proposal (nomor, judul, pengaju, status, tanggal submit, aksi lihat detail).
2. Gunakan `StatusBadge` component.

**File yang terlibat:**
- `resources/js/Pages/AdminKesra/Verification/Index.vue` (baru)

**Kriteria Selesai:**
- [ ] Tabel menampilkan proposal yang perlu diverifikasi
- [ ] Klik "Lihat Detail" navigasi ke halaman show

**Status:** ⬜ Belum Dikerjakan

---

### T-041: Buat VerificationController — Show + Auto-Transition Diajukan→VerifikasiOnline

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-039
**Rujukan:** US-VER-02 (Auto-transition saat buka detail)

**Yang harus dikerjakan:**
1. Method `show()` di VerificationController: load proposal + dokumen + verifikasi.
2. Jika status proposal `diajukan`, otomatis transisi ke `verifikasi_online` saat Admin Kesra membuka halaman ini.
3. Kirim data proposal, daftar dokumen (versi terbaru per type), dan status verifikasi per dokumen ke Vue.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/VerificationController.php` (modify)

**Kriteria Selesai:**
- [ ] Membuka detail proposal berstatus `diajukan` → status otomatis berubah ke `verifikasi_online`
- [ ] Transisi tercatat di `proposal_status_logs`
- [ ] Data dokumen dan status verifikasi dikirim ke frontend

**Status:** ⬜ Belum Dikerjakan

---

### T-042: Buat Halaman Vue — Verification Show + Verifikasi Per-Dokumen

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-041
**Rujukan:** US-VER-03, US-VER-04, US-VER-05, US-VER-06

**Yang harus dikerjakan:**
1. Buat `Pages/AdminKesra/Verification/Show.vue`: detail proposal + daftar 11 dokumen.
2. Per dokumen: preview/download link, tombol "Valid" dan "Tidak Valid" + input catatan.
3. Buat `Components/proposal/DocumentVerifyCard.vue`: card per dokumen dengan aksi verifikasi.
4. Tombol aksi di bawah: "Loloskan ke Berkas Fisik" (semua valid), "Minta Revisi" (ada tidak valid), "Tolak Proposal".

**File yang terlibat:**
- `resources/js/Pages/AdminKesra/Verification/Show.vue` (baru)
- `resources/js/Components/proposal/DocumentVerifyCard.vue` (baru)

**Kriteria Selesai:**
- [ ] Admin bisa klik Valid/Tidak Valid per dokumen
- [ ] Tombol "Loloskan" hanya aktif jika semua 11 dokumen valid
- [ ] Tombol "Minta Revisi" muncul jika ada dokumen tidak valid

**Status:** ⬜ Belum Dikerjakan

---

### T-043: Implementasi Backend Verifikasi Per-Dokumen + Keputusan Akhir

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-041
**Rujukan:** US-VER-03, US-VER-04, US-VER-05, US-VER-06

**Yang harus dikerjakan:**
1. Method `verifyDocument()`: simpan ke `document_verifications` (proposal_document_id, verified_by, verification_type='online', status, notes).
2. Buat `VerifyDocumentRequest.php`.
3. Method `complete()`: cek semua 11 dokumen valid → transisi `VerifikasiOnline → MenungguBerkasFisik`. Jika ada yang tidak valid → transisi `VerifikasiOnline → PerluRevisi` + buat `revision_notes`.
4. Method `reject()`: transisi `VerifikasiOnline → Ditolak` + simpan alasan. Buat `RejectProposalRequest.php`.
5. Set `verified_by`, `verified_online_at`, `rejected_at` sesuai keputusan.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/VerificationController.php` (modify)
- `app/Http/Requests/AdminKesra/VerifyDocumentRequest.php` (baru)
- `app/Http/Requests/AdminKesra/RejectProposalRequest.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Verifikasi dokumen tersimpan di `document_verifications`
- [ ] Complete (semua valid) → status `menunggu_berkas_fisik`
- [ ] Complete (ada invalid) → status `perlu_revisi` + record di `revision_notes`
- [ ] Reject → status `ditolak` + `rejected_at` terisi
- [ ] Semua transisi tercatat di `proposal_status_logs`

**Status:** ⬜ Belum Dikerjakan

---

### T-044: Implementasi Revisi Dokumen oleh Pengaju (Upload Ulang Versi Lama)

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-043, T-034
**Rujukan:** US-VER-07, US-VER-08, DEC-08 (Versi lama tidak dihapus)

**Yang harus dikerjakan:**
1. Di halaman Show Proposal (Pengaju), jika status `perlu_revisi`: tampilkan catatan revisi dari Admin + daftar dokumen yang "Tidak Valid".
2. Hanya dokumen "Tidak Valid" yang bisa di-upload ulang (dokumen "Valid" tetap terlihat tapi tidak perlu upload ulang).
3. Upload ulang → membuat record baru di `proposal_documents` dengan `version + 1`, file lama TETAP ADA.
4. Setelah semua dokumen tidak valid sudah di-upload ulang, tampilkan tombol "Ajukan Ulang" (resubmit).

**File yang terlibat:**
- `resources/js/Pages/Pengaju/Proposals/Show.vue` (modify)
- `app/Http/Controllers/Pengaju/DocumentUploadController.php` (modify jika perlu)
- `app/Http/Controllers/Pengaju/ProposalController.php` (modify — resubmit)

**Kriteria Selesai:**
- [ ] Pengaju hanya bisa upload ulang dokumen yang "Tidak Valid"
- [ ] Upload ulang membuat record versi baru, versi lama TETAP ADA di DB dan storage
- [ ] Resubmit → status kembali ke `diajukan`

**Status:** ⬜ Belum Dikerjakan

---

### T-045: Buat PhysicalArchiveController + Halaman Arsip Berkas Fisik

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-043
**Rujukan:** US-VER-09, US-VER-10, US-VER-11

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/AdminKesra/PhysicalArchiveController.php`.
2. Method `index()`: tampilkan proposal berstatus `menunggu_berkas_fisik` dan `verifikasi_final` dengan pencarian.
3. Method `verify()`: terima berkas fisik → transisi `MenungguBerkasFisik → VerifikasiFinal`, atau tolak/minta revisi.
4. Set `physical_docs_received_at`, `final_verified_at` sesuai keputusan.
5. Buat halaman Vue `Pages/AdminKesra/PhysicalArchive/Index.vue`.
6. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/PhysicalArchiveController.php` (baru)
- `app/Http/Requests/AdminKesra/VerifyPhysicalRequest.php` (baru)
- `resources/js/Pages/AdminKesra/PhysicalArchive/Index.vue` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Halaman menampilkan proposal berstatus `menunggu_berkas_fisik` dengan fitur pencarian
- [ ] Terima berkas fisik → status `verifikasi_final`, `final_verified_at` terisi
- [ ] Tolak → status `ditolak`; Minta revisi → status `perlu_revisi`

**Status:** ⬜ Belum Dikerjakan

---

### T-046: Buat PengajuListController + Halaman Daftar & Profil Pengaju

**Fase:** 5 — Verifikasi Proposal
**Dependensi:** T-019
**Rujukan:** US-VER-12, US-VER-13

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/AdminKesra/PengajuListController.php`.
2. Method `index()`: daftar semua user dengan role `pengaju`, fitur pencarian.
3. Method `show()`: profil pengaju (data lembaga, berkas legalitas, tabel riwayat proposal diurutkan terbaru).
4. Buat halaman Vue: `Pages/AdminKesra/Pengaju/Index.vue`, `Pages/AdminKesra/Pengaju/Profile.vue`.
5. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/PengajuListController.php` (baru)
- `resources/js/Pages/AdminKesra/Pengaju/Index.vue` (baru)
- `resources/js/Pages/AdminKesra/Pengaju/Profile.vue` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Admin Kesra bisa lihat daftar semua pengaju dengan pencarian
- [ ] Profil pengaju menampilkan data lembaga + berkas legalitas + riwayat proposal

**Status:** ⬜ Belum Dikerjakan

---

## Fase 6 — Modul LPJ

---

### T-047: Buat LpjController (Pengaju) + Upload LPJ

**Fase:** 6 — Modul LPJ
**Dependensi:** T-045 (butuh status `verifikasi_final`)
**Rujukan:** US-LPJ-01, US-LPJ-02, US-LPJ-03, DEC-09 (LPJ wajib ada)

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Pengaju/LpjController.php`.
2. Method `store()`: upload file LPJ (PDF, maks 10 MB) ke disk privat `storage/app/private/lpj/{proposal_id}/`. Update kolom `lpj_file`, set `lpj_status = 'menunggu'`.
3. Buat `UploadLpjRequest.php`: validasi file (pdf, max 10240 KB).
4. Buat `LpjPolicy.php`: Pengaju hanya bisa upload jika proposal miliknya berstatus `verifikasi_final`.
5. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/Pengaju/LpjController.php` (baru)
- `app/Http/Requests/Pengaju/UploadLpjRequest.php` (baru)
- `app/Policies/LpjPolicy.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Pengaju bisa upload LPJ hanya untuk proposal berstatus `verifikasi_final`
- [ ] File tersimpan di `storage/app/private/lpj/{proposal_id}/`
- [ ] `lpj_status` berubah ke `menunggu`

**Status:** ⬜ Belum Dikerjakan

---

### T-048: Buat Halaman Vue — LPJ Upload (Pengaju)

**Fase:** 6 — Modul LPJ
**Dependensi:** T-047
**Rujukan:** US-LPJ-01, US-LPJ-02, US-LPJ-03

**Yang harus dikerjakan:**
1. Buat `Pages/Pengaju/Proposals/Lpj.vue`: tampilkan status LPJ, catatan revisi (jika ada), form upload file LPJ.
2. Jika `lpj_status = revisi`, tampilkan catatan dari Admin Kesra + form upload ulang.
3. Buat composable `Composables/useLpjStatus.js`.

**File yang terlibat:**
- `resources/js/Pages/Pengaju/Proposals/Lpj.vue` (baru)
- `resources/js/Composables/useLpjStatus.js` (baru)

**Kriteria Selesai:**
- [ ] Halaman menampilkan status LPJ dan form upload
- [ ] Catatan revisi ditampilkan jika `lpj_status = revisi`

**Status:** ⬜ Belum Dikerjakan

---

### T-049: Buat LpjVerificationController (Admin Kesra) + Halaman

**Fase:** 6 — Modul LPJ
**Dependensi:** T-047
**Rujukan:** US-LPJ-04, US-LPJ-05, US-LPJ-06

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/AdminKesra/LpjVerificationController.php`.
2. Method `index()`: daftar proposal dengan `lpj_status = 'menunggu'`.
3. Method `show()`: detail LPJ + download link.
4. Method `verify()`: terima (`lpj_status = 'diterima'`) atau kembalikan (`lpj_status = 'revisi'` + `lpj_catatan`).
5. Buat `VerifyLpjRequest.php`.
6. Buat halaman Vue: `Pages/AdminKesra/Lpj/Index.vue`, `Pages/AdminKesra/Lpj/Show.vue`.
7. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/AdminKesra/LpjVerificationController.php` (baru)
- `app/Http/Requests/AdminKesra/VerifyLpjRequest.php` (baru)
- `resources/js/Pages/AdminKesra/Lpj/Index.vue` (baru)
- `resources/js/Pages/AdminKesra/Lpj/Show.vue` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Admin Kesra bisa lihat daftar LPJ pending
- [ ] Bisa menerima atau mengembalikan LPJ dengan catatan
- [ ] Siklus revisi berulang: `menunggu → revisi → menunggu → ... → diterima`

**Status:** ⬜ Belum Dikerjakan

---

## Fase 7 — Dashboard & Notifikasi

---

### T-050: Buat Dashboard Pengaju (Stepper + Alert)

**Fase:** 7 — Dashboard & Notifikasi
**Dependensi:** T-036, T-048
**Rujukan:** US-DASH-01, US-NOTIF-03, US-PROP-07

**Yang harus dikerjakan:**
1. Update `Pages/Pengaju/Dashboard.vue`: tampilkan stepper visual status proposal aktif, status LPJ (jika ada), riwayat proposal tahun sebelumnya.
2. Buat `Components/ui/Stepper.vue`: stepper visual 7 status.
3. Tampilkan alert kontekstual: banner peringatan jika status `perlu_revisi`, banner sukses jika `verifikasi_final`.
4. Update `DashboardController` Pengaju: kirim data proposal aktif + LPJ + riwayat.

**File yang terlibat:**
- `resources/js/Pages/Pengaju/Dashboard.vue` (modify)
- `resources/js/Components/ui/Stepper.vue` (baru)
- `resources/js/Components/ui/Alert.vue` (baru)
- `app/Http/Controllers/Pengaju/DashboardController.php` (modify)

**Kriteria Selesai:**
- [ ] Dashboard menampilkan stepper status proposal aktif
- [ ] Alert peringatan muncul saat `perlu_revisi`
- [ ] Alert sukses muncul saat `verifikasi_final`
- [ ] Riwayat proposal tahun sebelumnya ditampilkan (read-only)

**Status:** ⬜ Belum Dikerjakan

---

### T-051: Buat Dashboard Admin Kesra (Statistik + Tabel)

**Fase:** 7 — Dashboard & Notifikasi
**Dependensi:** T-043, T-049
**Rujukan:** US-DASH-02

**Yang harus dikerjakan:**
1. Update `Pages/AdminKesra/Dashboard.vue`: 4 kartu statistik (Total Diajukan, Verifikasi Online, Menunggu Berkas Fisik, Disetujui Final), tabel pengajuan terbaru, ringkasan LPJ pending.
2. Buat `Components/ui/Card.vue` (stat card reusable).
3. Update `DashboardController` Admin Kesra: hitung statistik, query proposal terbaru + LPJ pending.

**File yang terlibat:**
- `resources/js/Pages/AdminKesra/Dashboard.vue` (modify)
- `resources/js/Components/ui/Card.vue` (baru)
- `app/Http/Controllers/AdminKesra/DashboardController.php` (modify)

**Kriteria Selesai:**
- [ ] 4 kartu statistik menampilkan angka yang benar
- [ ] Tabel proposal terbaru ditampilkan
- [ ] Ringkasan LPJ pending ditampilkan

**Status:** ⬜ Belum Dikerjakan

---

### T-052: Buat Dashboard Super Admin

**Fase:** 7 — Dashboard & Notifikasi
**Dependensi:** T-019
**Rujukan:** — (overview sederhana akun staf)

**Yang harus dikerjakan:**
1. Update `Pages/SuperAdmin/Dashboard.vue`: tampilkan jumlah Admin Kesra aktif/nonaktif, link ke halaman manajemen akun.
2. Update `DashboardController` Super Admin.

**File yang terlibat:**
- `resources/js/Pages/SuperAdmin/Dashboard.vue` (modify)
- `app/Http/Controllers/SuperAdmin/DashboardController.php` (modify)

**Kriteria Selesai:**
- [ ] Dashboard menampilkan statistik akun Admin Kesra

**Status:** ⬜ Belum Dikerjakan

---

### T-053: Buat Notification Classes (In-App)

**Fase:** 7 — Dashboard & Notifikasi
**Dependensi:** T-014
**Rujukan:** US-NOTIF-01, US-NOTIF-02

**Yang harus dikerjakan:**
1. Buat `app/Notifications/ProposalSubmittedNotification.php`: dikirim ke **semua** Admin Kesra saat proposal baru diajukan. Channel: `database`.
2. Buat `app/Notifications/LpjVerifiedNotification.php`: dikirim ke Pengaju saat LPJ diterima.
3. Buat `app/Notifications/LpjRevisionRequestedNotification.php`: dikirim ke Pengaju saat LPJ dikembalikan.
4. Payload `data`: `{ title, message, url }`.
5. Dispatch notifikasi dari controller yang relevan (submit proposal → notif ke Admin Kesra; verifikasi LPJ → notif ke Pengaju).

**File yang terlibat:**
- `app/Notifications/ProposalSubmittedNotification.php` (baru)
- `app/Notifications/LpjVerifiedNotification.php` (baru)
- `app/Notifications/LpjRevisionRequestedNotification.php` (baru)
- Controller terkait (modify)

**Kriteria Selesai:**
- [ ] Submit proposal → record notification muncul di tabel `notifications` untuk semua Admin Kesra
- [ ] Verifikasi/revisi LPJ → record notification untuk Pengaju terkait
- [ ] Payload berisi `title`, `message`, `url`

**Status:** ⬜ Belum Dikerjakan

---

### T-054: Buat NotificationController + Dropdown UI

**Fase:** 7 — Dashboard & Notifikasi
**Dependensi:** T-053
**Rujukan:** US-NOTIF-01, US-NOTIF-02

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/NotificationController.php`: method `index` (daftar notifikasi), `markAsRead` (tandai dibaca).
2. Buat `Components/layout/NotificationDropdown.vue`: dropdown di header layout, tampilkan notifikasi belum dibaca, klik → tandai dibaca + navigasi ke URL terkait.
3. Buat `Composables/useNotifications.js`: fetch notifikasi saat page load.
4. Integrasikan dropdown ke `AuthenticatedLayout.vue`.
5. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/NotificationController.php` (baru)
- `resources/js/Components/layout/NotificationDropdown.vue` (baru)
- `resources/js/Composables/useNotifications.js` (baru)
- `resources/js/Layouts/AuthenticatedLayout.vue` (modify)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Dropdown menampilkan daftar notifikasi belum dibaca
- [ ] Klik notifikasi → tandai dibaca + navigasi ke halaman terkait
- [ ] Badge angka jumlah notifikasi belum dibaca di header

**Status:** ⬜ Belum Dikerjakan

---

## Fase 8 — Manajemen Akun Staf (Super Admin)

---

### T-055: Buat AdminKesraManagementController + CRUD

**Fase:** 8 — Manajemen Akun Staf
**Dependensi:** T-019
**Rujukan:** US-STAFF-01, US-STAFF-02, US-STAFF-03, US-STAFF-04, DEC-03

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/SuperAdmin/AdminKesraManagementController.php`.
2. Method `index`: daftar semua user dengan role `admin-kesra`.
3. Method `create` + `store`: buat akun Admin Kesra baru (nama, email, password). Set `role_id = 2`, `created_by = auth()->id()`, `is_active = true`. Tidak perlu verifikasi email.
4. Method `edit` + `update`: edit nama, email.
5. Method `toggleActive`: toggle `is_active`.
6. Buat `StoreAdminKesraRequest.php`.
7. Buat `UserPolicy.php` (hanya Super Admin yang bisa CRUD Admin Kesra).
8. Tambah route.

**File yang terlibat:**
- `app/Http/Controllers/SuperAdmin/AdminKesraManagementController.php` (baru)
- `app/Http/Requests/SuperAdmin/StoreAdminKesraRequest.php` (baru)
- `app/Policies/UserPolicy.php` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Super Admin bisa buat akun Admin Kesra baru
- [ ] Super Admin bisa edit nama/email Admin Kesra
- [ ] Super Admin bisa toggle aktif/nonaktif
- [ ] Non-Super Admin TIDAK bisa akses route ini (403)

**Status:** ⬜ Belum Dikerjakan

---

### T-056: Buat Halaman Vue — Manajemen Admin Kesra

**Fase:** 8 — Manajemen Akun Staf
**Dependensi:** T-055
**Rujukan:** US-STAFF-01, US-STAFF-02, US-STAFF-03, US-STAFF-04

**Yang harus dikerjakan:**
1. Buat `Pages/SuperAdmin/AdminKesra/Index.vue`: tabel daftar Admin Kesra (nama, email, status aktif, aksi).
2. Buat `Pages/SuperAdmin/AdminKesra/Create.vue`: form buat akun baru.
3. Buat `Pages/SuperAdmin/AdminKesra/Edit.vue`: form edit + tombol toggle aktif.

**File yang terlibat:**
- `resources/js/Pages/SuperAdmin/AdminKesra/Index.vue` (baru)
- `resources/js/Pages/SuperAdmin/AdminKesra/Create.vue` (baru)
- `resources/js/Pages/SuperAdmin/AdminKesra/Edit.vue` (baru)

**Kriteria Selesai:**
- [ ] Super Admin bisa mengelola akun Admin Kesra dari UI
- [ ] Status aktif/nonaktif terlihat jelas di tabel

**Status:** ⬜ Belum Dikerjakan

---

### T-057: Profil Edit untuk Admin Kesra dan Super Admin

**Fase:** 8 — Manajemen Akun Staf
**Dependensi:** T-019
**Rujukan:** US-PROF-03, US-AUTH-05

**Yang harus dikerjakan:**
1. Buat halaman profil edit untuk Admin Kesra: `Pages/AdminKesra/Profile/Edit.vue` (nama, email, ubah password).
2. Buat halaman profil edit untuk Super Admin: `Pages/SuperAdmin/Profile/Edit.vue`.
3. Reuse Breeze profile update logic (atau buat controller terpisah).
4. Tambah route.

**File yang terlibat:**
- `resources/js/Pages/AdminKesra/Profile/Edit.vue` (baru)
- `resources/js/Pages/SuperAdmin/Profile/Edit.vue` (baru)
- `routes/web.php` (modify)

**Kriteria Selesai:**
- [ ] Admin Kesra bisa edit nama, email, password sendiri
- [ ] Super Admin bisa edit nama, email, password sendiri

**Status:** ⬜ Belum Dikerjakan

---

## Fase 9 — API Mobile

---

### T-058: Buat API Login Endpoint (Sanctum)

**Fase:** 9 — API Mobile
**Dependensi:** T-001
**Rujukan:** US-API-01, ARCHITECTURE.md bagian 7

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Api/AuthController.php`.
2. Method `login()`: terima email + password, validasi, buat token Sanctum (`$user->createToken('MobileAppToken')`), return JSON `{ success, message, token, user }`.
3. Tambah route di `routes/api.php` dengan throttle.

**File yang terlibat:**
- `app/Http/Controllers/Api/AuthController.php` (baru)
- `routes/api.php` (modify)

**Kriteria Selesai:**
- [ ] `POST /api/login` dengan credential valid → return token + user data
- [ ] `POST /api/login` dengan credential invalid → return error 401
- [ ] Response format sesuai ARCHITECTURE.md bagian 7.2

**Status:** ⬜ Belum Dikerjakan

---

### T-059: Buat API Proposals Endpoint (Publik)

**Fase:** 9 — API Mobile
**Dependensi:** T-009
**Rujukan:** US-API-02

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Api/ProposalController.php`.
2. Method `index()`: return proposal berstatus `verifikasi_final` (publik, tanpa auth). Field: id, proposal_number, activity_title, total_budget, status, created_at.
3. Tambah route di `routes/api.php`.

**File yang terlibat:**
- `app/Http/Controllers/Api/ProposalController.php` (baru)
- `routes/api.php` (modify)

**Kriteria Selesai:**
- [ ] `GET /api/proposals` tanpa auth → return JSON daftar proposal `verifikasi_final`
- [ ] Proposal berstatus selain `verifikasi_final` TIDAK muncul

**Status:** ⬜ Belum Dikerjakan

---

### T-060: Buat API User Endpoint (Auth Required)

**Fase:** 9 — API Mobile
**Dependensi:** T-058
**Rujukan:** US-API-03

**Yang harus dikerjakan:**
1. Buat `app/Http/Controllers/Api/UserController.php`.
2. Method `show()`: return data user yang terautentikasi via Sanctum token.
3. Tambah route di `routes/api.php` dalam group `auth:sanctum`.

**File yang terlibat:**
- `app/Http/Controllers/Api/UserController.php` (baru)
- `routes/api.php` (modify)

**Kriteria Selesai:**
- [ ] `GET /api/user` dengan Bearer token valid → return user data
- [ ] `GET /api/user` tanpa token → 401

**Status:** ⬜ Belum Dikerjakan

---

## Fase 10 — Testing & Polish

---

### T-061: Feature Test — State Transition Proposal

**Fase:** 10 — Testing
**Dependensi:** T-036 (submit), T-043 (verifikasi), T-045 (arsip fisik)
**Rujukan:** ARCHITECTURE.md bagian 9.3

**Yang harus dikerjakan:**
1. Buat `tests/Feature/Proposal/StateTransitionTest.php`.
2. Test semua 10 transisi valid (draft→diajukan, diajukan→verifikasi_online, dll).
3. Test minimal 3 transisi INVALID (misal draft→verifikasi_final, ditolak→diajukan, dll) → harus throw exception.
4. Test bahwa setiap transisi membuat record di `proposal_status_logs`.

**File yang terlibat:**
- `tests/Feature/Proposal/StateTransitionTest.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan test --filter=StateTransitionTest` — semua lulus

**Status:** ⬜ Belum Dikerjakan

---

### T-062: Feature Test — Autentikasi & Role Access

**Fase:** 10 — Testing
**Dependensi:** T-019
**Rujukan:** ARCHITECTURE.md bagian 9.3

**Yang harus dikerjakan:**
1. Buat `tests/Feature/Auth/RegistrationTest.php`: test registrasi Pengaju (auto role, field institusional).
2. Buat `tests/Feature/Auth/LoginTest.php`: test login per role + redirect yang benar.
3. Buat `tests/Feature/Authorization/RoleAccessTest.php`: test Pengaju tidak bisa akses route Admin Kesra (403), dan sebaliknya.

**File yang terlibat:**
- `tests/Feature/Auth/RegistrationTest.php` (baru)
- `tests/Feature/Auth/LoginTest.php` (baru)
- `tests/Feature/Authorization/RoleAccessTest.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan test --filter=RegistrationTest` — lulus
- [ ] `php artisan test --filter=LoginTest` — lulus
- [ ] `php artisan test --filter=RoleAccessTest` — lulus

**Status:** ⬜ Belum Dikerjakan

---

### T-063: Feature Test — Upload Dokumen + Versioning

**Fase:** 10 — Testing
**Dependensi:** T-034, T-044
**Rujukan:** DEC-08 (Versi lama tidak dihapus), ARCHITECTURE.md bagian 9.3

**Yang harus dikerjakan:**
1. Buat `tests/Feature/Document/DocumentUploadTest.php`.
2. Test: upload PDF berhasil, upload file > 5MB ditolak, upload ulang membuat versi baru (version + 1), versi lama tetap ada di DB.
3. Test: file tersimpan di disk `local`, BUKAN disk `public`.

**File yang terlibat:**
- `tests/Feature/Document/DocumentUploadTest.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan test --filter=DocumentUploadTest` — semua lulus

**Status:** ⬜ Belum Dikerjakan

---

### T-064: Feature Test — Verifikasi Dokumen + LPJ Workflow

**Fase:** 10 — Testing
**Dependensi:** T-043, T-049
**Rujukan:** ARCHITECTURE.md bagian 9.3

**Yang harus dikerjakan:**
1. Buat `tests/Feature/Verification/OnlineVerificationTest.php`: test auto-transition diajukan→verifikasi_online, verifikasi per dokumen, keputusan akhir.
2. Buat `tests/Feature/Lpj/LpjWorkflowTest.php`: test upload LPJ, verifikasi (terima/revisi), siklus revisi.

**File yang terlibat:**
- `tests/Feature/Verification/OnlineVerificationTest.php` (baru)
- `tests/Feature/Lpj/LpjWorkflowTest.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan test --filter=OnlineVerificationTest` — lulus
- [ ] `php artisan test --filter=LpjWorkflowTest` — lulus

**Status:** ⬜ Belum Dikerjakan

---

### T-065: Feature Test — API Mobile

**Fase:** 10 — Testing
**Dependensi:** T-058, T-059, T-060
**Rujukan:** ARCHITECTURE.md bagian 9.3

**Yang harus dikerjakan:**
1. Buat `tests/Feature/Api/ApiTest.php`.
2. Test: login valid → return token, login invalid → 401.
3. Test: GET /api/proposals → hanya return proposal `verifikasi_final`.
4. Test: GET /api/user dengan token → return user, tanpa token → 401.

**File yang terlibat:**
- `tests/Feature/Api/ApiTest.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan test --filter=ApiTest` — semua lulus

**Status:** ⬜ Belum Dikerjakan

---

### T-066: Buat DemoSeeder (Data Dummy untuk Presentasi)

**Fase:** 10 — Testing & Polish
**Dependensi:** T-036
**Rujukan:** PRD bagian 8.1 (Open Question #5)

**Yang harus dikerjakan:**
1. Buat `database/seeders/DemoSeeder.php`: buat beberapa user Pengaju + Admin Kesra, beberapa proposal di berbagai status, beberapa dokumen upload.
2. Buat User Factory (`database/factories/UserFactory.php` — modify) + Proposal Factory.
3. Daftarkan DemoSeeder di `DatabaseSeeder.php` (dijalankan conditional, misal via `php artisan db:seed --class=DemoSeeder`).

**File yang terlibat:**
- `database/seeders/DemoSeeder.php` (baru)
- `database/factories/UserFactory.php` (modify)
- `database/factories/ProposalFactory.php` (baru)

**Kriteria Selesai:**
- [ ] `php artisan db:seed --class=DemoSeeder` berhasil
- [ ] Dashboard menampilkan data realistis untuk demo/presentasi

**Status:** ⬜ Belum Dikerjakan

---

### T-067: Polish UI — Sidebar, Styling, Responsive

**Fase:** 10 — Testing & Polish
**Dependensi:** Semua task Vue sebelumnya
**Rujukan:** PRD NFR-UX-02 (Responsivitas desktop + tablet)

**Yang harus dikerjakan:**
1. Review dan perbaiki styling semua halaman: konsistensi warna, spacing, tipografi.
2. Pastikan sidebar navigation berfungsi baik di desktop dan tablet.
3. Pastikan badge status konsisten di seluruh aplikasi.
4. Perbaiki bug UI yang ditemukan saat testing.

**File yang terlibat:**
- Semua file Vue di `resources/js/` (review & polish)

**Kriteria Selesai:**
- [ ] Aplikasi terlihat rapi dan konsisten di desktop
- [ ] Sidebar dan layout tidak rusak di resolusi tablet
- [ ] Badge status warna konsisten di semua halaman

**Status:** ⬜ Belum Dikerjakan

---

### T-068: Jalankan Laravel Pint + Final Review

**Fase:** 10 — Testing & Polish
**Dependensi:** Semua task PHP sebelumnya
**Rujukan:** AGENTS.md bagian 7 (Definition of Done)

**Yang harus dikerjakan:**
1. Jalankan `./vendor/bin/pint` untuk format seluruh kode PHP.
2. Jalankan `php artisan test` — pastikan SEMUA test lulus.
3. Cek ulang: tidak ada file sensitif di disk `public`.
4. Cek ulang: semua controller web menggunakan `Inertia::render(...)`, bukan `view(...)`.
5. Cek ulang: semua komponen Vue menggunakan `<script setup>`.
6. Commit final.

**File yang terlibat:**
- Seluruh codebase (review)

**Kriteria Selesai:**
- [ ] `./vendor/bin/pint` — no changes (sudah bersih)
- [ ] `php artisan test` — semua lulus (hijau)
- [ ] `grep -r "disk('public')" app/` — tidak ada hasil (tidak ada upload ke disk public)
- [ ] `grep -r "view(" app/Http/Controllers/` — hanya ada di AuthController bawaan Breeze (jika ada)

**Status:** ⬜ Belum Dikerjakan

---

## Ringkasan Statistik

| Fase | Jumlah Task | Status |
|---|:---:|---|
| Fase 0 — Setup & Fondasi | 14 | ⬜ |
| Fase 1 — Autentikasi & Role | 8 | ⬜ |
| Fase 2 — State Machine | 4 | ⬜ |
| Fase 3 — Manajemen Proposal | 10 | ⬜ |
| Fase 4 — Jendela Pengajuan | 2 | ⬜ |
| Fase 5 — Verifikasi Proposal | 8 | ⬜ |
| Fase 6 — Modul LPJ | 3 | ⬜ |
| Fase 7 — Dashboard & Notifikasi | 5 | ⬜ |
| Fase 8 — Manajemen Staf | 3 | ⬜ |
| Fase 9 — API Mobile | 3 | ⬜ |
| Fase 10 — Testing & Polish | 8 | ⬜ |
| **Total** | **68** | |

---

*— Akhir TODO.md —*
