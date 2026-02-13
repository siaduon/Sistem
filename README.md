# Sistem - E-Learning PHP Native

Project ini berisi implementasi website E-Learning / Les Online berbasis **PHP Native + MySQL + PDO + Bootstrap 5**.

## Struktur Utama

```text
elearning/
├── config/
│   ├── database.php
│   └── functions.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── admin/
│   ├── dashboard.php
│   ├── course.php
│   ├── lesson.php
│   ├── mission.php
│   └── quiz.php
├── user/
│   ├── dashboard.php
│   ├── course_detail.php
│   ├── lesson.php
│   ├── mission.php
│   └── quiz.php
├── uploads/
├── index.php
└── database.sql
```

## Cara Install di XAMPP

1. Salin folder repo ini ke `htdocs`, contoh:
   - `C:/xampp/htdocs/Sistem`
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka phpMyAdmin (`http://localhost/phpmyadmin`).
4. Import file `elearning/database.sql`.
5. Pastikan konfigurasi DB di `elearning/config/database.php` sesuai:
   - host: `localhost`
   - dbname: `elearning_db`
   - username: `root`
   - password: ``
6. Akses aplikasi:
   - `http://localhost/Sistem/elearning`

## Dummy Account

- Admin
  - Email: `admin@elearning.test`
  - Password: `password123`
- User
  - Email: `user@elearning.test`
  - Password: `password123`
