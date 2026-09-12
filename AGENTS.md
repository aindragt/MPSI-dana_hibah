# AGENTS.md — Portal e-Hibah Kesra

> **File ini dibaca otomatis oleh AI coding agent di awal setiap sesi.**
> Semua instruksi di bawah ini WAJIB diikuti. Jangan asumsikan aturan sendiri.

---

## 1. Project Setup Commands

Jalankan urutan ini untuk setup project dari nol di environment baru:

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Jalankan migrasi database + seeder
php artisan migrate --seed

# 6. Jalankan dev server (2 terminal terpisah)
# Terminal 1 — Laravel backend:
php artisan serve
# Terminal 2 — Vite frontend:
npm run dev
```

Jika project belum di-init (belum ada `composer.json`), lihat bagian init di ARCHITECTURE.md.

---

## 2. Referensi Dokumen Lain

| File | Isi | Kapan Dirujuk |
|---|---|---|
| `PRD.md` | Requirement bisnis lengkap: user stories, hak akses per role, alur status proposal, daftar 11 dokumen wajib, aturan jendela pengajuan. | Saat butuh tahu **apa** yang harus dibangun dan **mengapa**. |
| `ARCHITECTURE.md` | Skema database lengkap (semua kolom + tipe + constraint), struktur folder, state machine, konvensi penamaan, testing strategy. | Saat butuh tahu **bagaimana** membangun sesuatu secara teknis. |

**AGENTS.md ini** hanya berisi **aturan kerja harian**. Jangan duplikasi isi PRD/ARCHITECTURE di sini.

---

## 3. Code Style & Konvensi

### 3.1 PHP — PSR-12 + Laravel Pint

- Format kode sebelum commit:
  ```bash
  ./vendor/bin/pint
  ```
- Gunakan **type hints** di semua parameter dan return type.
- Gunakan **Backed Enum** (bukan string literal) untuk nilai yang terbatas (status, tipe verifikasi).

### 3.2 Vue — Composition API + `<script setup>`

- **SELALU** gunakan `<script setup>` syntax. **JANGAN** gunakan Options API (`data()`, `methods`, `computed`).
- Urutan blok dalam file `.vue`: `<script setup>` → `<template>` → `<style>` (jika ada).

Contoh **benar**:
```vue
<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    proposal: Object,
})

const isEditable = computed(() => props.proposal.status === 'draft')
</script>

<template>
    <div>{{ proposal.activity_title }}</div>
</template>
```

Contoh **salah** (JANGAN lakukan ini):
```vue
<script>
export default {
    data() { return { ... } },
    methods: { ... },
}
</script>
```

### 3.3 Penamaan (Ringkasan)

| Item | Format | Contoh |
|---|---|---|
| Tabel database | `snake_case`, plural | `proposal_documents` |
| Model PHP | `PascalCase`, singular | `ProposalDocument` |
| Controller PHP | `PascalCase` + `Controller` | `VerificationController` |
| Form Request PHP | `PascalCase` + `Request` | `StoreProposalRequest` |
| Policy PHP | `PascalCase` + `Policy` | `ProposalPolicy` |
| State class PHP | `PascalCase`, tanpa suffix | `VerifikasiOnline` |
| Vue Page/Component | `PascalCase.vue` | `Dashboard.vue` |
| Vue Composable | `camelCase`, prefix `use` | `useProposalStatus.js` |
| Route name | `dot.notation` | `pengaju.proposals.store` |
| Route URL | `/kebab-case` | `/admin-kesra/verification` |

Referensi lengkap: ARCHITECTURE.md bagian 8.

---

## 4. Skeleton Kode — Template Wajib

Saat membuat file baru dari tipe-tipe di bawah ini, gunakan skeleton berikut sebagai starting point. **Jangan menebak struktur sendiri.**

### 4.1 Form Request

**Apa ini**: Class Laravel untuk validasi input HTTP. Dipakai di controller alih-alih validasi manual di controller body.

**Lokasi**: `app/Http/Requests/{RoleFolder}/{NamaRequest}.php`

```php
<?php

namespace App\Http\Requests\Pengaju;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    /**
     * Siapa yang boleh mengirim request ini.
     * Return true jika otorisasi ditangani oleh middleware/policy di tempat lain.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi dilakukan di Policy, bukan di sini
    }

    /**
     * Aturan validasi untuk setiap field input.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'activity_title'       => ['required', 'string', 'max:255'],
            'activity_description' => ['nullable', 'string'],
            'total_budget'         => ['required', 'numeric', 'min:0'],
            'execution_start_date' => ['nullable', 'date'],
            'execution_end_date'   => ['nullable', 'date', 'after_or_equal:execution_start_date'],
        ];
    }
}
```

**Di controller, pakai begini** (bukan `$request->validate([...])` manual):
```php
public function store(StoreProposalRequest $request)
{
    // $request sudah tervalidasi di sini
    $validated = $request->validated();
    // ...
}
```

### 4.2 Policy

**Apa ini**: Class Laravel yang menentukan apakah user tertentu BOLEH melakukan aksi tertentu terhadap resource tertentu. Dipakai untuk otorisasi per-role.

**Lokasi**: `app/Policies/{NamaPolicy}.php`

```php
<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Apakah user boleh melihat proposal ini?
     * - Pengaju: hanya milik sendiri
     * - Admin Kesra: semua proposal
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $user->role->slug === 'admin-kesra'
            || $user->id === $proposal->user_id;
    }

    /**
     * Apakah user boleh membuat proposal baru?
     * - Hanya Pengaju
     * - Harus dalam jendela pengajuan aktif
     * - Maks 1 proposal aktif per tahun
     */
    public function create(User $user): bool
    {
        return $user->role->slug === 'pengaju';
    }

    /**
     * Apakah user boleh mengedit proposal ini?
     * - Hanya pemilik
     * - Hanya saat status draft atau perlu_revisi
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return $user->id === $proposal->user_id
            && $proposal->status->isEditable();
    }
}
```

**Di controller, pakai begini**:
```php
public function edit(Proposal $proposal)
{
    $this->authorize('update', $proposal);  // Lempar 403 jika tidak boleh
    // ...
}
```

**Register Policy** di `AppServiceProvider` atau via auto-discovery (Laravel otomatis jika nama sesuai konvensi `{Model}Policy`).

### 4.3 State Class (spatie/laravel-model-states)

**Apa ini**: Class yang merepresentasikan 1 status proposal. Package `spatie/laravel-model-states` memastikan hanya transisi yang valid yang bisa terjadi.

**Lokasi**: `app/States/ProposalStatus/{NamaStatus}.php`

#### Base abstract class — JANGAN DIUBAH tanpa konfirmasi:

```php
<?php

namespace App\States\ProposalStatus;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProposalStatusState extends State
{
    abstract public function label(): string;      // Label untuk UI (e.g., "Perlu Revisi")
    abstract public function badgeColor(): string;  // Tailwind CSS class (e.g., "bg-yellow-100 text-yellow-800")
    abstract public function isEditable(): bool;    // Apakah Pengaju bisa edit di status ini
    abstract public function isTerminal(): bool;    // Apakah status ini final (tidak bisa transisi lagi)

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Diajukan::class)
            ->allowTransition(Diajukan::class, VerifikasiOnline::class)
            ->allowTransition(VerifikasiOnline::class, PerluRevisi::class)
            ->allowTransition(VerifikasiOnline::class, MenungguBerkasFisik::class)
            ->allowTransition(VerifikasiOnline::class, Ditolak::class)
            ->allowTransition(PerluRevisi::class, Diajukan::class)
            ->allowTransition(MenungguBerkasFisik::class, VerifikasiFinal::class)
            ->allowTransition(MenungguBerkasFisik::class, PerluRevisi::class)
            ->allowTransition(MenungguBerkasFisik::class, Ditolak::class);
    }
}
```

#### Contoh concrete state:

```php
<?php

namespace App\States\ProposalStatus;

class Draft extends ProposalStatusState
{
    public static $name = 'draft';

    public function label(): string { return 'Draft'; }
    public function badgeColor(): string { return 'bg-gray-100 text-gray-700'; }
    public function isEditable(): bool { return true; }
    public function isTerminal(): bool { return false; }
}
```

#### Daftar 7 state class yang harus ada:

| Class | `$name` (DB value) | `label()` | `isEditable` | `isTerminal` |
|---|---|---|:---:|:---:|
| `Draft` | `draft` | Draft | ✅ | ❌ |
| `Diajukan` | `diajukan` | Diajukan | ❌ | ❌ |
| `VerifikasiOnline` | `verifikasi_online` | Verifikasi Berkas Online | ❌ | ❌ |
| `PerluRevisi` | `perlu_revisi` | Perlu Revisi | ✅ | ❌ |
| `MenungguBerkasFisik` | `menunggu_berkas_fisik` | Menunggu Berkas Fisik | ❌ | ❌ |
| `VerifikasiFinal` | `verifikasi_final` | Verifikasi Final | ❌ | ✅ |
| `Ditolak` | `ditolak` | Ditolak | ❌ | ✅ |

#### Di Model Proposal:

```php
use Spatie\ModelStates\HasStates;
use App\States\ProposalStatus\ProposalStatusState;

class Proposal extends Model
{
    use HasStates;

    protected $casts = [
        'status' => ProposalStatusState::class,
    ];
}
```

#### Cara transisi status di controller:

```php
use App\States\ProposalStatus\Diajukan;

// Cek dulu, lalu transisi
if ($proposal->status->canTransitionTo(Diajukan::class)) {
    $proposal->status->transitionTo(Diajukan::class);
}
```

### 4.4 Inertia Controller Response

**Apa ini**: Cara controller Laravel mengembalikan halaman Vue via Inertia (bukan Blade view, bukan JSON).

```php
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Pengaju/Dashboard', [
            // Props yang dikirim ke komponen Vue
            'proposal'   => $proposal,
            'statistics' => $stats,
        ]);
    }
}
```

**JANGAN** pernah mengembalikan `view(...)` (Blade) atau `response()->json(...)` dari controller web. Selalu gunakan `Inertia::render(...)`.

### 4.5 Middleware CheckRole

**Apa ini**: Middleware custom yang memeriksa apakah user yang login memiliki role yang diizinkan untuk mengakses route tertentu.

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * @param string ...$slugs  Satu atau lebih role slug yang diizinkan (e.g., 'pengaju', 'admin-kesra')
     */
    public function handle(Request $request, Closure $next, string ...$slugs)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role->slug, $slugs)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
```

**Cara pakai di route**:
```php
Route::middleware('role:pengaju')->group(function () { ... });
Route::middleware('role:admin-kesra')->group(function () { ... });
Route::middleware('role:super_admin')->group(function () { ... });
```

### 4.6 File Upload ke Disk Privat

**Cara upload file ke disk privat** (BUKAN disk public):

```php
// Di controller — upload dokumen proposal
$path = $request->file('document')->storeAs(
    "proposals/{$year}/{$userId}",   // Folder tujuan (relatif terhadap root disk)
    "{$slug}_v{$version}_{$random}.{$ext}", // Nama file
    'local'                           // WAJIB disk 'local' (private)
);

// Simpan $path ke kolom file_path di database
```

**JANGAN** pernah menggunakan:
- `'public'` sebagai disk untuk file sensitif
- `Storage::disk('public')->put(...)` untuk dokumen proposal/legalitas/LPJ
- `php artisan storage:link` untuk expose file sensitif

**Cara serve file privat ke user yang berhak**:

```php
// Di FileController — download dengan auth check
public function download(ProposalDocument $document)
{
    $this->authorize('download', $document);

    return Storage::disk('local')->download(
        $document->file_path,
        $document->original_filename
    );
}
```

---

## 5. CRITICAL RULES — Aturan yang TIDAK BOLEH Dilanggar

> ⚠️ **BACA BAGIAN INI DENGAN SEKSAMA. Pelanggaran terhadap aturan-aturan ini akan merusak sistem.**

### RULE-01: Hanya 3 Role

Sistem hanya punya 3 role. **JANGAN** pernah menambah role baru (termasuk `tim-kesra`, `tapd`, `tim-tapd`, `reviewer`, `approver`, atau nama lain apapun) tanpa konfirmasi eksplisit dari user.

| id | name | slug |
|:---:|---|---|
| 1 | Pengaju | `pengaju` |
| 2 | Admin Kesra | `admin-kesra` |
| 3 | Super Admin | `super_admin` |

### RULE-02: State Machine Wajib Pakai spatie/laravel-model-states

Status proposal **WAJIB** dikelola oleh `spatie/laravel-model-states`. **JANGAN**:
- Membuat kolom `status` bertipe `ENUM` di migration (harus `VARCHAR`)
- Mengubah status dengan assignment langsung (`$proposal->status = 'diajukan'`) tanpa melalui `transitionTo()`
- Membuat state management manual (switch-case, if-else chain untuk validasi transisi)

### RULE-03: Semua File Sensitif di Disk Privat

**SEMUA** file berikut WAJIB disimpan di disk `local` (private), **BUKAN** di disk `public`:
- 11 dokumen proposal (surat permohonan, RAB, NPWP, dll)
- Berkas legalitas pengaju (akta, SK Kesbangpol, rekening bank, NPWP lembaga)
- Foto profil pengaju
- File LPJ

Jika kamu menemukan kode yang menyimpan file-file di atas ke disk `public`, itu adalah **BUG** — perbaiki segera.

### RULE-04: File Versi Lama Tidak Boleh Dihapus

Saat Pengaju mengunggah ulang dokumen yang direvisi:
1. Buat **record baru** di tabel `proposal_documents` dengan `version + 1`
2. Simpan file baru dengan nama berbeda (e.g., `rab_v2_xyz.pdf`)
3. **JANGAN** hapus record atau file versi lama — tetap simpan untuk audit

### RULE-05: Audit Trail Otomatis

**Setiap** perubahan status proposal HARUS otomatis membuat record baru di tabel `proposal_status_logs` yang mencatat:
- `proposal_id` — proposal yang berubah
- `changed_by` — ID user yang melakukan perubahan
- `from_status` — status sebelumnya
- `to_status` — status baru
- `notes` — catatan (jika ada)

Ini ditangani oleh Observer (`ProposalObserver`) — pastikan observer ini terdaftar dan berjalan.

### RULE-06: Admin Kesra = Satu-Satunya Verifikator

Admin Kesra menangani **semua** proses verifikasi — baik online (berkas digital) maupun offline (berkas fisik). **JANGAN** membuat:
- Role terpisah untuk verifikasi fisik
- Alur approval multi-level/berjenjang di dalam sistem
- Fitur delegasi verifikasi ke role lain

### RULE-07: Inertia Saja, Bukan Blade

Semua halaman web **WAJIB** menggunakan Inertia.js + Vue. **JANGAN**:
- Membuat file `.blade.php` baru (kecuali `app.blade.php` root layout dari Breeze)
- Mengembalikan `view(...)` dari controller web
- Menginstall Laravel Filament, Livewire, atau admin panel Blade lainnya

### RULE-08: Composition API Saja

Semua komponen Vue **WAJIB** menggunakan `<script setup>` (Composition API). **JANGAN** menggunakan Options API (`export default { data(), methods, computed }`).

---

## 6. Testing

### 6.1 Command

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=StateTransitionTest

# Jalankan dengan coverage (opsional)
php artisan test --coverage
```

### 6.2 Aturan Testing

1. Setiap fitur terkait **alur kritis** berikut WAJIB punya minimal 1 feature test:
   - Transisi status proposal (7 status, 10 transisi valid)
   - Upload dokumen (validasi tipe file, ukuran, versioning)
   - Autentikasi per role (login, redirect, akses ditolak)
   - Verifikasi dokumen (valid/tidak valid → perubahan status)
2. Test ditulis di folder `tests/Feature/`.
3. Gunakan `RefreshDatabase` trait di setiap test class.
4. Seed roles dan document_types di `setUp()` sebelum tiap test.

### 6.3 Contoh Struktur Test

```php
<?php

namespace Tests\Feature\Proposal;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Proposal;
use App\States\ProposalStatus\Draft;
use App\States\ProposalStatus\Diajukan;

class StateTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\DocumentTypeSeeder::class);
    }

    public function test_pengaju_can_submit_draft_proposal(): void
    {
        $pengaju = User::factory()->create(['role_id' => 1]);
        $proposal = Proposal::factory()->create([
            'user_id' => $pengaju->id,
            'status'  => Draft::class,
        ]);

        // ... tambahkan 11 dokumen ...

        $this->actingAs($pengaju)
            ->post(route('pengaju.proposals.submit', $proposal))
            ->assertRedirect();

        $this->assertInstanceOf(Diajukan::class, $proposal->fresh()->status);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        // Draft tidak bisa langsung ke VerifikasiFinal
        // ...
    }
}
```

---

## 7. Definition of Done

Sebelum menyatakan sebuah fitur/task "selesai", pastikan **semua** checklist ini terpenuhi:

- [ ] `php artisan migrate` jalan tanpa error
- [ ] `php artisan test` — semua test lulus (hijau)
- [ ] Tidak ada file sensitif (proposal/legalitas/LPJ) yang disimpan di disk `public`
- [ ] Kode PHP sudah di-format: `./vendor/bin/pint`
- [ ] Setiap transisi status proposal tercatat di `proposal_status_logs` (cek secara manual atau via test)
- [ ] Halaman baru menggunakan Inertia (`Inertia::render(...)`) bukan Blade view
- [ ] Komponen Vue menggunakan `<script setup>`, bukan Options API
- [ ] Form validation menggunakan Form Request class, bukan validasi inline di controller
- [ ] Otorisasi menggunakan Policy (`$this->authorize(...)`) di controller

---

## 8. Git Commit Convention

Format: `{type}: {deskripsi singkat dalam bahasa Indonesia}`

| Type | Kapan Dipakai | Contoh |
|---|---|---|
| `feat` | Fitur baru | `feat: tambah upload dokumen proposal` |
| `fix` | Perbaikan bug | `fix: perbaiki validasi ukuran file LPJ` |
| `refactor` | Refactor tanpa ubah fungsionalitas | `refactor: pindahkan logika verifikasi ke service` |
| `test` | Tambah/ubah test | `test: tambah test transisi status proposal` |
| `docs` | Perubahan dokumentasi | `docs: update ARCHITECTURE.md skema tabel` |
| `style` | Formatting/whitespace | `style: jalankan Laravel Pint` |
| `chore` | Maintenance (deps, config) | `chore: update composer dependencies` |

Aturan:
- Huruf kecil semua setelah tipe
- Tidak perlu body/footer (solo project)
- 1 commit per 1 unit perubahan logis

---

## 9. Environment Variables Penting

File `.env` harus berisi variable-variable ini agar sistem berfungsi:

| Variable | Keterangan | Contoh Nilai |
|---|---|---|
| `APP_NAME` | Nama aplikasi | `"Portal e-Hibah Kesra"` |
| `APP_ENV` | Environment | `local` |
| `APP_DEBUG` | Mode debug | `true` |
| `APP_URL` | URL base | `http://localhost:8000` |
| `DB_CONNECTION` | Driver database | `mysql` |
| `DB_HOST` | Host database | `127.0.0.1` |
| `DB_PORT` | Port database | `3306` |
| `DB_DATABASE` | Nama database | `ehibah_kesra` |
| `DB_USERNAME` | User database | `root` |
| `DB_PASSWORD` | Password database | (kosong di Laragon) |
| `FILESYSTEM_DISK` | Disk default storage | `local` ← **WAJIB `local`, JANGAN `public`** |
| `MAIL_MAILER` | Driver email | `smtp` atau `log` (dev) |
| `MAIL_HOST` | Host SMTP | `sandbox.smtp.mailtrap.io` (dev) |
| `MAIL_PORT` | Port SMTP | `2525` |
| `MAIL_USERNAME` | Username SMTP | (dari Mailtrap) |
| `MAIL_PASSWORD` | Password SMTP | (dari Mailtrap) |
| `MAIL_FROM_ADDRESS` | Alamat pengirim | `noreply@ehibah-kesra.test` |
| `QUEUE_CONNECTION` | Driver queue | `database` |
| `SESSION_DRIVER` | Driver session | `database` |

> **PENTING**: `FILESYSTEM_DISK` harus `local` (private). Jika berubah ke `public`, file sensitif akan terekspos.

---

## 10. Quick Reference — Struktur Route

```php
// routes/web.php — Ringkasan struktur routing

// Root → redirect ke login
Route::get('/', fn () => redirect()->route('login'));

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// Routes yang butuh login + email verified
Route::middleware(['auth', 'verified'])->group(function () {

    // === PENGAJU ===
    Route::middleware('role:pengaju')
        ->prefix('pengaju')
        ->name('pengaju.')
        ->group(function () {
            Route::get('/dashboard', [Pengaju\DashboardController::class, 'index'])->name('dashboard');
            Route::resource('proposals', Pengaju\ProposalController::class);
            Route::post('proposals/{proposal}/submit', [Pengaju\ProposalController::class, 'submit'])->name('proposals.submit');
            Route::post('proposals/{proposal}/documents', [Pengaju\DocumentUploadController::class, 'store'])->name('proposals.documents.store');
            Route::post('proposals/{proposal}/lpj', [Pengaju\LpjController::class, 'store'])->name('proposals.lpj.store');
            Route::get('profile', [Pengaju\ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [Pengaju\ProfileController::class, 'update'])->name('profile.update');
        });

    // === ADMIN KESRA ===
    Route::middleware('role:admin-kesra')
        ->prefix('admin-kesra')
        ->name('admin-kesra.')
        ->group(function () {
            Route::get('/dashboard', [AdminKesra\DashboardController::class, 'index'])->name('dashboard');
            Route::get('verification', [AdminKesra\VerificationController::class, 'index'])->name('verification.index');
            Route::get('verification/{proposal}', [AdminKesra\VerificationController::class, 'show'])->name('verification.show');
            Route::post('verification/{proposal}/verify-document', [AdminKesra\VerificationController::class, 'verifyDocument'])->name('verification.verify-document');
            Route::post('verification/{proposal}/complete', [AdminKesra\VerificationController::class, 'complete'])->name('verification.complete');
            Route::post('verification/{proposal}/reject', [AdminKesra\VerificationController::class, 'reject'])->name('verification.reject');
            Route::get('physical-archive', [AdminKesra\PhysicalArchiveController::class, 'index'])->name('physical-archive.index');
            Route::post('physical-archive/{proposal}/verify', [AdminKesra\PhysicalArchiveController::class, 'verify'])->name('physical-archive.verify');
            Route::resource('submission-windows', AdminKesra\SubmissionWindowController::class)->except(['show', 'destroy']);
            Route::get('lpj', [AdminKesra\LpjVerificationController::class, 'index'])->name('lpj.index');
            Route::get('lpj/{proposal}', [AdminKesra\LpjVerificationController::class, 'show'])->name('lpj.show');
            Route::post('lpj/{proposal}/verify', [AdminKesra\LpjVerificationController::class, 'verify'])->name('lpj.verify');
            Route::get('pengaju', [AdminKesra\PengajuListController::class, 'index'])->name('pengaju.index');
            Route::get('pengaju/{user}', [AdminKesra\PengajuListController::class, 'show'])->name('pengaju.show');
        });

    // === SUPER ADMIN ===
    Route::middleware('role:super_admin')
        ->prefix('super-admin')
        ->name('super-admin.')
        ->group(function () {
            Route::get('/dashboard', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
            Route::resource('admin-kesra', SuperAdmin\AdminKesraManagementController::class)->except(['show', 'destroy']);
            Route::patch('admin-kesra/{user}/toggle-active', [SuperAdmin\AdminKesraManagementController::class, 'toggleActive'])->name('admin-kesra.toggle-active');
        });

    // === SHARED ===
    Route::get('files/proposal-document/{document}', [FileController::class, 'showProposalDocument'])->name('files.proposal-document');
    Route::get('files/profile/{user}/{field}', [FileController::class, 'showProfileFile'])->name('files.profile');
    Route::get('files/lpj/{proposal}', [FileController::class, 'showLpj'])->name('files.lpj');
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// routes/api.php — API Mobile
Route::post('/login', [Api\AuthController::class, 'login'])->middleware('throttle:auth');
Route::get('/proposals', [Api\ProposalController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [Api\UserController::class, 'show']);
});
```

---

*— Akhir AGENTS.md —*
