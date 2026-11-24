## Praktikum Project 6 PHP MVC Pemrograman Berbasis Web Lanjutan FST UIN Sumatera Utara        
**Project 6 Pemrograman Berbasis Web Lanjutan**  
Riski Amanda NIM : 0702243150                                                        
Dosen: Suendri, M.Kom · Semester Ganjil 2026/2027                                                 
UIN Sumatera Utara Medan, Fakultas Sains dan Teknologi
## Buat database
* database.sql

## Buat Folder app serta empat folder didalamnya
* controllers
* core
* models
* views

## Buat Folder inc serta file didalamnya
* config.php

## Buat Folder layouts serta folder dan file didalamnya
* assets - (1) css; (2) images
* dashboard.php
* guest.php

## Buat File index.php
## Inisialisasi Composer
## Set PSR-4
## Install filp/whoops untuk memudahkan informasi error
```
composer require filp/woops
```

## Buat File .htaccess
## Bangun teknik MVC mulai dari
* app/core/Bootstrap.php
* app/core/Controller.php
* app/core/Model.php
* app/core/Error.php

## Setting Model untuk terhubung dengan Database
```
app/models/Namaclass.php
```

## Setting Controller
```
app/controllers/Namaclass.php
```

## Setting View
```
app/view/Namafolder/Namafile.php
```

## Testing
```
http://localhost/php-mvc/
http://localhost/php-mvc/index/index
http://localhost/php-mvc/index/show
http://localhost/php-mvc/index/detail/1
http://localhost/php-mvc/dashboard
```
