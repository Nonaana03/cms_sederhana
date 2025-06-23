# cms_sederhana

Struktur folder MVC yang direkomendasikan:

cms_sederhana/
  app/
    controllers/
      PostController.php
      UserController.php
      AuthController.php
    models/
      Post.php
      User.php
    views/
      posts/
        index.php
        form.php
      users/
        index.php
        form.php
      auth/
        login.php
  config/
    database.php
  public/
    index.php
    assets/
  .htaccess
  README.md

Penjelasan:
- app/controllers: Tempat file controller (logika request)
- app/models: Tempat file model (logika database)
- app/views: Tempat file view (tampilan)
- config: Konfigurasi (misal database)
- public: File yang bisa diakses publik (index.php, asset gambar/css/js)
- .htaccess: Untuk routing ke public/index.php

Silakan buat folder dan file sesuai struktur di atas untuk memulai migrasi ke pola MVC.