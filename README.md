# Project Website Pengajuan Cuti V2

## Deskripsi

Project ini dibuat atas kebutuhan salah satu perusahaan untuk keperluan pengajuan cuti karyawan secara online atau melalui website

## Fitur Utama

- Manajemen data karyawan
- Manajemen jabatan
- Manajemen divisi
- Manajemen pengajuan cuti

## 🛠️ Techstack

![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white) ![Livewire](https://img.shields.io/badge/livewire-%234e56a6.svg?style=for-the-badge&logo=livewire&logoColor=white) ![Alpine.js](https://img.shields.io/badge/alpinejs-white.svg?style=for-the-badge&logo=alpinedotjs&logoColor=%238BC0D0) ![Filament](https://img.shields.io/badge/Filament-FFAA00?style=for-the-badge&logoColor=%23000000) ![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white) ![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=for-the-badge&logo=vite&logoColor=white) ![NPM](https://img.shields.io/badge/NPM-%23CB3837.svg?style=for-the-badge&logo=npm&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

## 🛠️ Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini secara lokal:

1. **Clone repository**

```bash
git clone https://github.com/Karungg/website-pengajuan-cuti-v2.git
cd website-pengajuan-cuti-v2
```

2. **Install dependensi menggunakan composer**

```
composer install
```

3. **Copy .env**

```
cp .env.example .env
```

4. **Ubah konfigurasi pada file .env sesuai kebutuhan**

```
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

5. **Buat key aplikasi**
```
php artisan key:generate
```

6. **Jalankan migrasi dan seeder**

```
php artisan migrate
php artisan db:seed
```

7. **Jalankan aplikasi dan Queue**

```
php artisan serve
php artisan queue:work
```

Buka aplikasi di browser <a href="http://localhost:8000">http://localhost:8000</a>

👨‍💻 Developer
Created with ❤️ by <a href="https://github.com/Karungg">Karungg</a>

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](<a href="https://github.com/Karungg/website-pengajuan-cuti-v2?tab=MIT-1-ov-file">LICENSE</a>).
