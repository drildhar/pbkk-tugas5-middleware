# Secure Academic Portal ITS

**Tugas PBKK Pertemuan 5 — Middleware, Autentikasi (Laravel Breeze), & Otorisasi (Gates & Policies)**

Portal pengelolaan **rencana proposal proyek kelompok AI** dengan autentikasi modern dan
otorisasi berlapis untuk tiga peran: **Dosen**, **Asisten Dosen (Asdos)**, dan **Mahasiswa**.

Dibangun dengan **Laravel 11.x** + **Laravel Breeze (Blade + Tailwind CSS)**, dilengkapi
**custom middleware**, **Gate**, **Policy**, **rate limiting**, dan **quick login** untuk pengujian.

---

## 1. Identitas Mahasiswa

| Item | Keterangan |
| --- | --- |
| Nama | **Adriel Mahira Dharma** |
| NRP | **5025241097** |
| Kelas | **PBKK B** |
| Departemen | Teknik Informatika, Institut Teknologi Sepuluh Nopember (ITS) |
| Topik | Middleware, Autentikasi (Laravel Breeze), & Otorisasi (Gates & Policies) |

---

## 2. Deskripsi Aplikasi

Aplikasi ini mensimulasikan portal akademik dengan **tiga area akses**:

| Area | URL | Pengakses | Isi |
| --- | --- | --- | --- |
| **Area Publik** | `/` | Siapa saja (guest) | Landing page & informasi umum tiga peran |
| **Area Mahasiswa** | `/dashboard`, `/proposals/*` | User login (terautentikasi) | Mengajukan, melihat, mengubah, dan menghapus proposal AI miliknya sendiri |
| **Area Dosen & Asisten** | `/admin/*` | Hanya `dosen` & `asdos` | Meninjau seluruh proposal, memberi status, nilai, dan catatan revisi |

Alur bisnis singkat:

1. Mahasiswa mengajukan proposal → status awal `pending`.
2. Dosen/Asdos meninjau di `/admin` → menetapkan `approved` + `nilai`, atau `rejected` + `catatan_revisi`.
3. Mahasiswa melihat hasil peninjauan pada halaman detail proposal.

---

## 3. Pemetaan Fitur terhadap Spesifikasi Tugas

| # | Ketentuan Tugas | Implementasi |
| --- | --- | --- |
| A | Laravel Breeze (Blade + Tailwind) | `laravel/breeze` v2.4, stack `blade`; login, register, profil, reset password, logout (form POST + CSRF) |
| B1 | Kolom `role` via migration terpisah | `database/migrations/2026_09_29_034412_add_role_to_users_table.php` (enum, default `mahasiswa`) |
| B2 | `$fillable` model `User` | `app/Models/User.php` (+ konstanta peran & helper `isDosen()`, `isAsdos()`, `isPengelola()`) |
| B3 | Model & migration `Proposal` | `app/Models/Proposal.php`, `database/migrations/2026_09_29_034413_create_proposals_table.php` |
| C | Seeder akun dummy | `database/seeders/DatabaseSeeder.php` (4 akun + 4 contoh proposal) |
| D | Custom middleware `CekPeran` | `app/Http/Middleware/CekPeran.php`, didaftarkan sebagai alias `cek.peran` di `bootstrap/app.php` |
| E | Gate global `kelola-nilai` | `app/Providers/AppServiceProvider.php` |
| F | Policy `ProposalPolicy` | `app/Policies/ProposalPolicy.php` (+ `@can` pada seluruh Blade view) |
| **+1** | Rate limiting kustom 3x/menit | `RateLimiter::for('submit-proposal')` di `AppServiceProvider`, dipasang pada route `POST /proposals` |
| **+2** | Autentikasi bypass lokal | Route `/login/as/{role}` (hanya `APP_ENV=local`) + tombol *Quick Login* di halaman login |

---

## 4. Akun Demo

Semua akun memakai kata sandi: **`password`**

| Peran | Email | Keterangan |
| --- | --- | --- |
| `dosen` | `dosen@its.ac.id` | Bisa meninjau & menilai semua proposal, boleh mengedit proposal siapa pun |
| `asdos` | `asdos@its.ac.id` | Bisa meninjau & menilai semua proposal (tidak boleh mengedit proposal mahasiswa) |
| `mahasiswa` | `mhs1@its.ac.id` | **Pemilik** 3 contoh proposal (untuk uji edit & delete) |
| `mahasiswa` | `mhs2@its.ac.id` | Mahasiswa lain — 1 contoh proposal (untuk uji penolakan akses `403`) |

---

## 5. Langkah Instalasi & Menjalankan Proyek

### Prasyarat

- PHP **8.2+** (diuji pada PHP 8.5) dengan ekstensi `pdo_sqlite`
- Composer 2.x
- Node.js 18+ & npm

### Perintah

```bash
# 1. Clone repositori
git clone https://github.com/drildhar/pbkk-tugas5-middleware.git
cd pbkk-tugas5-middleware

# 2. Pasang dependensi PHP
composer install          # sekaligus membuat database/database.sqlite bila belum ada

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Pastikan file SQLite tersedia (biasanya sudah dibuat langkah 2)
touch database/database.sqlite

# 5. Pasang & kompilasi aset frontend
npm install
npm run build             # hasil ada di public/build (sudah ikut ter-commit)

# 6. Migrasi + data demo
php artisan migrate --seed

# 7. Jalankan
php artisan serve
```

Buka **<http://localhost:8000>**.

> **Catatan:** aplikasi memakai SQLite sebagai default (`DB_CONNECTION=sqlite`) agar penguji tidak
> perlu menyiapkan server database. Untuk beralih ke MySQL, ubah variabel `DB_*` pada `.env`
> (konfigurasi MySQL/MariaDB juga sudah disiapkan di `config/database.php`).

### Menjalankan seluruh perintah sekaligus (opsional)

```bash
composer install && cp .env.example .env && php artisan key:generate \
  && npm install && npm run build && php artisan migrate --seed
php artisan serve
```

---

## 6. Skenario Demo

### 6.1 Quick Login tanpa mengetik kredensial (mode lokal)

Halaman login (`/login`) menampilkan panel **Quick Login** dengan tiga tombol:

| Tombol | Akun yang dipakai | Hasil |
| --- | --- | --- |
| **Dosen** | `dosen@its.ac.id` | Masuk & dapat membuka `/admin` |
| **Asdos** | `asdos@its.ac.id` | Masuk & dapat membuka `/admin` |
| **Mahasiswa** | `mhs1@its.ac.id` | Masuk sebagai pemilik contoh proposal |

Tombol tersebut hanya dirender saat `APP_ENV=local`. URL-nya juga bisa dipanggil langsung:

```
http://localhost:8000/login/as/dosen
http://localhost:8000/login/as/asdos
http://localhost:8000/login/as/mahasiswa
```

Di luar lingkungan lokal, route `login.as` **tidak didaftarkan sama sekali** sehingga
endpoint tersebut mengembalikan `404` (tidak menjadi celah keamanan di production).

### 6.2 Alur mahasiswa

1. Klik **Quick Login → Mahasiswa**.
2. Buka **Proposal Saya** → terlihat 3 proposal milik `mhs1` saja.
3. Klik **Ajukan Proposal** → isi judul & deskripsi → simpan (status otomatis `pending`).
4. Buka detail proposal → tombol **Edit** dan **Hapus** muncul karena user adalah pemiliknya.

### 6.3 Alur dosen menilai

1. Klik **Quick Login → Dosen**, lalu buka **Tinjau Proposal**.
2. Gunakan filter status / pencarian judul.
3. Klik **Tinjau** pada salah satu proposal → ubah status menjadi *Disetujui*, isi nilai
   (mis. `92`), lalu **Simpan Penilaian**.
4. Kembali ke akun mahasiswa → status dan nilai sudah tampil pada halaman detail.

### 6.4 Menguji penolakan akses (403)

| Uji | Cara | Hasil |
| --- | --- | --- |
| Mahasiswa membuka `/admin` | Login `mhs1` → akses `/admin/proposals` | `403 — Anda tidak memiliki hak akses!` |
| Mahasiswa lain mengedit proposal | Login `mhs2` → buka `/proposals/{id milik mhs1}` | `403` (tombol Edit/Hapus juga tidak dirender) |
| Dosen menghapus proposal mahasiswa | Login `dosen` → halaman detail proposal | `403`, tombol **Hapus** disembunyikan |
| Asdos mengedit proposal mahasiswa | Login `asdos` → `/proposals/{id}/edit` | `403` (asdos hanya boleh menilai via Gate `kelola-nilai`) |

### 6.5 Menguji rate limiting proposal

Login sebagai mahasiswa lalu kirim proposal **4 kali berturut-turut** (misal dengan tombol
*Ajukan Proposal* yang sama). Pengiriman ke-4 akan ditolak dengan halaman **HTTP 429
"Terlalu banyak permintaan"**, dan hanya 3 proposal yang tercatat. Batas ini dihitung
**per user per menit**.

---

## 7. Arsitektur Otorisasi

Otorisasi pada aplikasi ini memakai **tiga lapis pertahanan** (*defense in depth*):
middleware peran → Gate global → Policy per model, ditambah penyembunyian tombol di UI.

### 7.1 Custom Middleware — `CekPeran`

**Berkas:** `app/Http/Middleware/CekPeran.php`

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();

    if ($user === null) {
        abort(403, 'Anda tidak memiliki hak akses!');
    }

    $allowedRoles = array_map('trim', $roles);

    if (! $user->hasRole(...$allowedRoles)) {
        abort(403, 'Anda tidak memiliki hak akses!');
    }

    return $next($request);
}
```

Middleware ini menerima daftar peran secara **variadic**, sehingga satu alias bisa dipakai
untuk banyak kombinasi peran.

**Pendaftaran alias (khusus Laravel 11, tanpa `app/Http/Kernel.php`)** — `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'cek.peran' => CekPeran::class,
    ]);
})
```

**Penerapan pada route** — `routes/web.php`:

```php
Route::middleware(['auth', 'cek.peran:dosen,asdos'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [ProposalReviewController::class, 'index'])->name('dashboard');
        Route::get('/proposals', [ProposalReviewController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/{proposal}', [ProposalReviewController::class, 'show'])->name('proposals.show');
        Route::put('/proposals/{proposal}', [ProposalReviewController::class, 'update'])->name('proposals.update');
    });
```

### 7.2 Gate Global — `kelola-nilai`

**Berkas:** `app/Providers/AppServiceProvider.php`

```php
Gate::define('kelola-nilai', static function (User $user): bool {
    return $user->isPengelola();   // role dosen atau asdos
});
```

**Penerapan di controller** (`App\Http\Controllers\Admin\ProposalReviewController`):

```php
public function update(GradeProposalRequest $request, Proposal $proposal): RedirectResponse
{
    Gate::authorize('kelola-nilai');   // lempar 403 bila tidak berhak
    $proposal->update($request->validated());
    // ...
}
```

**Penerapan di Blade** — `@can('kelola-nilai')` menyembunyikan form penilaian:

```blade
@can('kelola-nilai')
    <form method="POST" action="{{ route('admin.proposals.update', $proposal) }}">
        {{-- input status, nilai, catatan revisi --}}
    </form>
@endcan
```

### 7.3 Policy — `ProposalPolicy`

**Berkas:** `app/Policies/ProposalPolicy.php` (ditemukan otomatis oleh konvensi Laravel)

| Method | Aturan bisnis | Implementasi |
| --- | --- | --- |
| `viewAny` | Semua user login boleh membuka daftar (isi sudah dibatasi controller) | `return true;` |
| `view` | Pemilik, dosen, dan asdos (peninjau) | `$user->id === $proposal->user_id \|\| $user->isPengelola()` |
| `create` | Semua user login | `return true;` |
| **`update`** | **Pemilik sendiri, atau dosen (boleh siapa saja)** | `$user->isDosen() \|\| $user->id === $proposal->user_id` |
| **`delete`** | **Hanya pemilik proposal** | `$user->id === $proposal->user_id` |

**Penerapan di controller** (`App\Http\Controllers\ProposalController`):

```php
public function edit(Proposal $proposal): View
{
    Gate::authorize('update', $proposal);
    return view('proposals.edit', ['proposal' => $proposal]);
}

public function destroy(Proposal $proposal): RedirectResponse
{
    Gate::authorize('delete', $proposal);
    $proposal->delete();
    // ...
}
```

**Penerapan di Blade:**

```blade
@can('update', $proposal)
    <a href="{{ route('proposals.edit', $proposal) }}">Edit</a>
@endcan

@can('delete', $proposal)
    <form method="POST" action="{{ route('proposals.destroy', $proposal) }}">
        @csrf @method('DELETE')
        <button type="submit">Hapus</button>
    </form>
@endcan
```

### 7.4 Matriks Hak Akses

| Aksi | Guest | Mahasiswa (pemilik) | Mahasiswa lain | Asdos | Dosen |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Landing page `/` | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dashboard | ❌ | ✅ | ✅ | ✅ | ✅ |
| Lihat detail proposal sendiri | ❌ | ✅ | ✅ | ✅ | ✅ |
| Lihat detail proposal mahasiswa lain | ❌ | ❌ | ❌ | ✅ | ✅ |
| Buat proposal | ❌ | ✅ | ✅ | ✅ | ✅ |
| **Edit** proposal sendiri | ❌ | ✅ | ❌ | ❌ | ✅ |
| **Edit** proposal mahasiswa lain | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Hapus** proposal sendiri | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Hapus** proposal mahasiswa lain | ❌ | ❌ | ❌ | ❌ | ❌ |
| Buka `/admin` | ❌ | ❌ | ❌ | ✅ | ✅ |
| **Input nilai** (Gate `kelola-nilai`) | ❌ | ❌ | ❌ | ✅ | ✅ |

### 7.5 Rate Limiting Kustom — `submit-proposal`

**Berkas:** `app/Providers/AppServiceProvider.php`

```php
RateLimiter::for('submit-proposal', static function (Request $request): Limit {
    $key = $request->user()?->getAuthIdentifier() ?? $request->ip();

    return Limit::perMinute(3)->by((string) $key);   // ekuivalen throttle:3,1
});
```

**Penerapan** — `routes/web.php`:

```php
Route::post('/proposals', [ProposalController::class, 'store'])
    ->middleware('throttle:submit-proposal')
    ->name('proposals.store');
```

Halaman `429` kustom tersedia di `resources/views/errors/429.blade.php`.

### 7.6 Isolasi mass assignment

`status`, `nilai`, dan `catatan_revisi` **tidak** masuk ke `StoreProposalRequest` /
`UpdateProposalRequest`. Mahasiswa hanya dapat mengirim `judul` dan `deskripsi`, sehingga
tidak bisa menaikkan status atau nilainya sendiri. Sebaliknya, penilaian hanya bisa
dilakukan lewat `GradeProposalRequest` yang diproteksi Gate `kelola-nilai`.
Perilaku ini diuji pada `tests/Feature/ProposalManagementTest.php`.

---

## 8. Skema Database

**`users`** (migration bawaan + kolom `role`)

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint PK | |
| `name`, `email`, `password` | string | bawaan Breeze |
| **`role`** | enum(`mahasiswa`,`dosen`,`asdos`), default `mahasiswa`, indexed | ditambahkan migration `..._add_role_to_users_table` |
| `email_verified_at`, `remember_token`, `timestamps` | | bawaan Breeze |

**`proposals`**

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| `id` | bigint PK | |
| `user_id` | foreignId → `users.id` | `constrained()` + `cascadeOnDelete()` |
| `judul` | string | divalidasi maks. 150 karakter |
| `deskripsi` | text | |
| `status` | enum(`pending`,`approved`,`rejected`), default `pending` | |
| `nilai` | integer, nullable | 0–100, divalidasi di `GradeProposalRequest` |
| `catatan_revisi` | text, nullable | wajib bila status `rejected` |
| `timestamps` | | |

---

## 9. Daftar Route Penting

| Method | URI | Name | Middleware | Keterangan |
| --- | --- | --- | --- | --- |
| GET | `/` | `home` | — | Landing page publik |
| GET | `/dashboard` | `dashboard` | `auth` | Dashboard adaptif per peran |
| GET | `/proposals` | `proposals.index` | `auth` | Daftar proposal milik sendiri |
| GET | `/proposals/create` | `proposals.create` | `auth` | Form pengajuan |
| POST | `/proposals` | `proposals.store` | `auth`, **`throttle:submit-proposal`** | Simpan proposal (maks. 3x/menit) |
| GET | `/proposals/{proposal}` | `proposals.show` | `auth` + Policy `view` | Detail proposal |
| GET | `/proposals/{proposal}/edit` | `proposals.edit` | `auth` + Policy `update` | Form edit |
| PUT | `/proposals/{proposal}` | `proposals.update` | `auth` + Policy `update` | Simpan perubahan |
| DELETE | `/proposals/{proposal}` | `proposals.destroy` | `auth` + Policy `delete` | Hapus proposal |
| GET | `/admin` | `admin.dashboard` | `auth`, **`cek.peran:dosen,asdos`** | Daftar tinjauan |
| GET | `/admin/proposals` | `admin.proposals.index` | `auth`, `cek.peran:dosen,asdos` | Daftar + filter |
| GET | `/admin/proposals/{proposal}` | `admin.proposals.show` | `auth`, `cek.peran:dosen,asdos` | Detail + form nilai |
| PUT | `/admin/proposals/{proposal}` | `admin.proposals.update` | `auth`, `cek.peran:dosen,asdos` + Gate `kelola-nilai` | Simpan penilaian |
| GET | `/login/as/{role}` | `login.as` | hanya `APP_ENV=local` | Quick login (bypass lokal) |

Lihat daftar lengkap dengan `php artisan route:list --except-vendor`.

---

## 10. Pengujian

Suite pengujian memakai PHPUnit dengan database SQLite `:memory:` (lihat `phpunit.xml`),
sehingga tidak menyentuh database pengembangan.

```bash
# Cara 1 — output paling bersih (baseline PHPUnit diterapkan)
composer test
# atau
./vendor/bin/phpunit

# Cara 2 — pembungkus artisan
php artisan test
```

Cakupan pengujian (**78 test, 207 assertion**):

| Berkas | Yang diuji |
| --- | --- |
| `tests/Feature/Middleware/CekPeranMiddlewareTest.php` | Guest diarahkan ke login; mahasiswa `403`; dosen & asdos `200`; pesan `403` kustom |
| `tests/Feature/Authorization/ProposalPolicyTest.php` | Matriks `update`/`delete`/`view`/`create` + auto-discovery policy |
| `tests/Feature/Authorization/KelolaNilaiGateTest.php` | Gate `kelola-nilai` untuk dosen/asdos/mahasiswa/guest |
| `tests/Feature/ProposalManagementTest.php` | CRUD proposal, isolasi data antar mahasiswa, proteksi mass assignment, validasi |
| `tests/Feature/AdminProposalReviewTest.php` | Peninjauan & penilaian, filter/pencarian, aturan validasi nilai |
| `tests/Feature/SubmitProposalRateLimitTest.php` | Batas 3x/menit, halaman `429`, penghitungan per user |
| `tests/Feature/Auth/QuickLoginTest.php` | Quick login dosen/asdos/mahasiswa di lingkungan lokal |
| `tests/Feature/Auth/QuickLoginDisabledTest.php` | Route quick login tidak tersedia di luar lokal |
| `tests/Feature/UserRoleTest.php` | Default `role`, eskalasi peran saat registrasi ditolak, helper peran, relasi & cascade delete |

---

## 11. Catatan Lingkungan

- **PHP 8.5 & Laravel 11.** Berkas konfigurasi bawaan kerangka kerja
  (`vendor/laravel/framework/config/database.php`) masih memakai konstanta
  `PDO::MYSQL_ATTR_SSL_CA` yang diusang-kan pada PHP 8.5. Aplikasi ini **tidak** memicunya
  (konfigurasi milik aplikasi sudah memakai `Pdo\Mysql::ATTR_SSL_CA` untuk PHP ≥ 8.4).
  Untuk membuat keluaran console bersih, `bootstrap/app.php` menurunkan `error_reporting`
  hanya selama jendela bootstrap, dan `baseline.xml` mencatat deprecation tersebut agar
  laporan PHPUnit bersih. Pada PHP 8.2–8.4 deprecation ini tidak muncul sama sekali.
- **Aset frontend** hasil `npm run build` disertakan pada repositori (`public/build`) agar
  aplikasi langsung dapat dijalankan tanpa langkah kompilasi aset.
- **Locale** aplikasi diset ke `id` (Bahasa Indonesia) dengan berkas terjemahan di `lang/id/`.

### Advisory keamanan pada `laravel/framework` 11.x

`composer audit` melaporkan beberapa advisory pada `laravel/framework` (antara lain
**CVE-2026-48019** — CRLF injection pada rule validasi `email`, dan *temporary signed URL path
confusion*). Perbaikannya hanya dirilis untuk Laravel 12.60+/12.61+, sedangkan tugas ini
mensyaratkan **Laravel 11.x**; `v11.56.1` (rilis 11.x terakhir) sudah tidak menerima patch.

Dampaknya pada aplikasi ini terbatas karena:

- aplikasi **tidak** memakai `URL::temporarySignedRoute()`; satu-satunya route bertanda tangan
  adalah endpoint verifikasi email bawaan Breeze (`routes/auth.php`), dan model `User` memang
  tidak mengimplementasikan `MustVerifyEmail` sehingga endpoint itu tidak pernah dipakai;
- input email hanya berasal dari form registrasi Breeze yang divalidasi.

Jalur perbaikan yang disarankan bila proyek dilanjutkan:

```bash
composer require laravel/framework:^12.0 --with-all-dependencies
```

---

## 12. Struktur Direktori (bagian penting)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/ProposalReviewController.php   # area daftar & penilaian (/admin)
│   │   ├── Auth/…                               # scaffolding Breeze
│   │   ├── DashboardController.php              # dashboard adaptif per peran
│   │   ├── HomeController.php                   # landing page publik
│   │   └── ProposalController.php               # CRUD proposal mahasiswa
│   ├── Middleware/CekPeran.php                  # ★ custom middleware peran
│   └── Requests/
│       ├── StoreProposalRequest.php             # hanya judul & deskripsi
│       ├── UpdateProposalRequest.php
│       └── GradeProposalRequest.php             # status, nilai, catatan_revisi
├── Models/
│   ├── Proposal.php
│   └── User.php                                 # konstanta & helper peran
├── Policies/ProposalPolicy.php                  # ★ aturan update/delete/view
└── Providers/AppServiceProvider.php             # ★ Gate kelola-nilai + RateLimiter

bootstrap/app.php                                # ★ alias middleware (Laravel 11)
routes/web.php                                   # ★ proteksi route per area + quick login
resources/views/
├── welcome.blade.php                            # area publik
├── dashboard.blade.php
├── proposals/{index,create,edit,show}.blade.php
├── admin/proposals/{index,show}.blade.php
├── errors/429.blade.php                         # halaman rate limit
└── components/{alert,role-badge,status-badge,stat-card,proposal-form}.blade.php

database/
├── migrations/…_add_role_to_users_table.php
├── migrations/…_create_proposals_table.php
├── factories/{UserFactory,ProposalFactory}.php
└── seeders/DatabaseSeeder.php

tests/Feature/…                                   # 78 test otorisasi & CRUD
```

---

## 13. Standar Kualitas

- **PSR-12**: seluruh berkas PHP memakai *type hints*, *return types*, dan *docblock* yang konsisten.
- **Arsitektur**: aturan otorisasi berada di Policy/Gate, validasi di Form Request, dan
  controller tetap tipis (*thin controller*).
- **Keamanan**: BCrypt password hashing, CSRF pada seluruh form POST, perlindungan mass
  assignment, session regeneration pada login, serta route bypass yang mati di luar `local`.
- **Git**: `.gitignore` mengecualikan `vendor/`, `node_modules/`, `.env`, dan berkas SQLite.
