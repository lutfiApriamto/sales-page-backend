# SalesGen.ai — Backend

> REST API untuk AI-powered Sales Page Generator. Dibangun dengan Laravel + Sanctum, terintegrasi dengan Gemini AI dan Supabase PostgreSQL.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Supabase-4169E1?style=flat-square&logo=postgresql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

---

## 📌 Tentang Project

SalesGen.ai adalah aplikasi web fullstack yang memungkinkan pengguna untuk menghasilkan **sales page / landing page profesional** secara otomatis menggunakan kecerdasan buatan (Gemini AI). Repository ini merupakan bagian **Backend API** dari aplikasi tersebut.

**Repository Frontend:** [sales-page-frontend](https://github.com/lutfiApriamto/sales-page-frontend)  
**Live API:** `https://sales-page-backend.onrender.com/api`

---

## ✨ Fitur Utama

- **Autentikasi via Sanctum** — Register, Login, Logout dengan token-based authentication
- **Pemulihan Sandi** — Forgot Password & Reset Password via email (Gmail SMTP)
- **Custom Email Template** — Template HTML email yang selaras dengan design system frontend
- **AI Generator** — Integrasi Gemini API untuk generate copywriting sales page
- **Sistem Credit** — Setiap akun mendapat 35 credit generate untuk mencegah abuse API
- **CRUD Sales Page** — Simpan, lihat, dan hapus riwayat sales page per user
- **Database Supabase** — PostgreSQL managed via Supabase

---

## 🛠️ Tech Stack

| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| Laravel | 12 | PHP Framework |
| PHP | 8.2 | Runtime |
| Laravel Sanctum | latest | Token Authentication |
| PostgreSQL | 15 | Database (via Supabase) |
| Gemini API | v1beta | AI Content Generation |
| Gmail SMTP | - | Email Service |
| Docker | - | Containerization untuk deployment |

---

## 📁 Struktur Folder

```
app/
├── Http/
│   └── Controllers/
│       ├── AuthController.php        # Register, Login, Logout, Forgot/Reset Password, Profile
│       └── SalesPageController.php   # CRUD Sales Page + Gemini AI Integration
├── Models/
│   ├── User.php                      # Model user dengan sistem credit
│   └── SalesPage.php                 # Model sales page
└── Notifications/
    └── ResetPasswordNotification.php # Custom email notification reset password

database/
└── migrations/
    ├── create_users_table.php
    ├── create_personal_access_tokens_table.php
    ├── create_sales_pages_table.php
    └── add_credits_to_users_table.php

resources/
└── views/
    └── emails/
        └── reset-password.blade.php  # Custom HTML email template

routes/
└── api.php                           # Definisi semua API endpoint
```

---

## 🔗 API Endpoints

### Public (tanpa autentikasi)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/register` | Registrasi akun baru |
| `POST` | `/api/login` | Login & mendapatkan token |
| `POST` | `/api/forgot-password` | Request link reset password |
| `POST` | `/api/reset-password` | Reset password dengan token |

### Private (wajib Bearer Token)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/user` | Data user yang sedang login |
| `GET` | `/api/profile` | Profil lengkap user + sisa credit |
| `POST` | `/api/logout` | Logout & hapus token |
| `GET` | `/api/sales-pages` | Ambil semua riwayat sales page |
| `POST` | `/api/sales-pages` | Generate & simpan sales page baru |
| `GET` | `/api/sales-pages/{id}` | Detail satu sales page |
| `DELETE` | `/api/sales-pages/{id}` | Hapus sales page |

---

## 🚀 Cara Menjalankan Secara Lokal

### Prasyarat

- PHP >= 8.2
- Composer
- PostgreSQL atau akun Supabase
- Gemini API Key ([dapatkan di sini](https://aistudio.google.com))
- Gmail App Password ([cara membuat](https://support.google.com/accounts/answer/185833))

### Instalasi

**1. Clone repository**
```bash
git clone https://github.com/lutfiApriamto/sales-page-backend.git
cd sales-page-backend
```

**2. Install dependencies**
```bash
composer install
```

**3. Buat file `.env`**
```bash
cp .env.example .env
```

**4. Generate APP_KEY**
```bash
php artisan key:generate
```

**5. Isi konfigurasi `.env`**
```env
APP_NAME=SalesGenAI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=your-supabase-host
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=your-username
DB_PASSWORD=your-password

GEMINI_API_KEY=your-gemini-api-key

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SalesGen AI"

FRONTEND_URL=http://localhost:5173
```

**6. Jalankan migration**
```bash
php artisan migrate
```

**7. Jalankan development server**
```bash
php artisan serve
```

API akan berjalan di `http://127.0.0.1:8000`

---

## 🌐 Environment Variables

| Variable | Keterangan |
|----------|------------|
| `APP_KEY` | Application key (generate otomatis) |
| `DB_HOST` | Host database PostgreSQL / Supabase |
| `DB_PORT` | Port database (Supabase: 6543) |
| `DB_DATABASE` | Nama database |
| `DB_USERNAME` | Username database |
| `DB_PASSWORD` | Password database |
| `GEMINI_API_KEY` | API key Google Gemini |
| `MAIL_USERNAME` | Email Gmail untuk kirim notifikasi |
| `MAIL_PASSWORD` | App Password Gmail |
| `FRONTEND_URL` | URL frontend untuk link reset password di email |

---

## 💳 Sistem Credit

Setiap akun baru mendapatkan **35 credit** secara otomatis saat registrasi. Setiap kali user berhasil generate sales page, 1 credit akan dikurangi. Jika credit habis, endpoint generate akan mengembalikan response `403 Forbidden`.

```json
{
  "status": "error",
  "message": "Batas penggunaan API (35/35) telah habis untuk akun demo ini."
}
```

---

## 📧 Email Reset Password

Backend mengirimkan email reset password dengan template HTML custom yang selaras dengan design system frontend. Link reset password diarahkan ke frontend:

```
{FRONTEND_URL}/reset-password?token=xxx&email=xxx
```

---

## ☁️ Deployment

Backend di-deploy menggunakan **Docker** di platform cloud. Pastikan semua environment variable sudah dikonfigurasi di platform hosting.

### Build Docker
```bash
docker build -t sales-page-backend .
docker run -p 8000:80 sales-page-backend
```

---

## 👤 Author

**Baginda Lutfi Apriamto**
- GitHub: [@lutfiApriamto](https://github.com/lutfiApriamto)

---

## 📝 Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).