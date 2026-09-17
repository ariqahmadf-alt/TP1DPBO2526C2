# TP1 DPBO - Manajemen Data Bioskop

Nama  : Ariq Ahmad Fathir
NIM   : 2506752
Kelas : C2

## Janji

Saya Ariq Ahmad Fathir dengan NIM 2506752 mengerjakan TP 1 DPBO 2026 C2 dalam mata kuliah 
Desain dan Pemrograman Berorientasi Objek untuk keberkahan-Nya, maka saya tidak melakukan 
kecurangan seperti yang telah dispesifikasikan. Aamiin.

## Tentang programnya

Jadi disini aku bikin program buat ngatur data film di bioskop, pake OOP
(class, object, constructor, sama encapsulation). Classnya cuma 1, namanya
`Film`, dibuat di 4 bahasa: C++, Java, Python (CLI/menu di terminal), sama
PHP (web).

Atribut yang ada di class Film:
- id (buat identifier tiap film, auto increment, gabisa diubah)
- judul
- genre
- durasi (menit)
- harga (harga tiket)
- gambar (path file poster-nya, lokal, bukan link internet)

Semua atributnya private, jadi kalo mau baca/ubah harus lewat
getter/setter. Semua object Film yang dibuat disimpen di array/list (vector
di C++, ArrayList di Java, list di Python, array di session buat PHP).

Fitur yang wajib ada udah semua: tambah data, tampilin semua data, update
(cari berdasarkan id dulu baru diedit), hapus (juga berdasarkan id), sama
cari data.

## Cara jalaninnya

C++:
```
cd CPP
g++ -std=c++17 -o bioskop Bioskop.cpp
./bioskop
```

Java:
```
cd Java
javac Main.java
java Main
```

Python:
```
cd Python
python3 main.py
```

PHP (harus lewat server, ga bisa dibuka langsung dari file):
```
cd PHP
php -S localhost:8000
```
terus buka `http://localhost:8000/index.php` di browser.

## Flow programnya 

Buat yang CLI (C++/Java/Python) semuanya mirip, ada menu looping terus
sampe user pilih keluar:
1. Tambah -> user input judul/genre/durasi/harga/path gambar, terus
   dibikinin object Film baru pake constructor, id-nya otomatis nambah
   sendiri, masuk ke array/list.
2. Tampilin -> looping semua isi array, panggil method buat print
   datanya satu-satu.
3. Update -> user masukin id, dicari dulu ada apa engga (linear search),
   kalo ada baru bisa diganti field-nya satu-satu (kalo dikosongin berarti
   skip, ga diubah).
4. Hapus -> sama, dicari dulu berdasarkan id, kalo ketemu baru dihapus
   dari array-nya.
5. Cari -> masukin id, ditampilin kalo ketemu.

Buat versi PHP-nya agak beda karena web, jadi:
- Film.php isi class-nya doang (constructor + getter setter)
- index.php itu yang jadi "otaknya", ngatur mau tambah/update/hapus/cari,
  sekalian nampilin HTML-nya juga di file yang sama
- Data disimpen di $_SESSION, bukan database (sesuai suruhan di soal). Jadi
  kalo session-nya abis/browser ditutup ya datanya ilang, itu emang
  sengaja karena ga boleh pake database
- Yang agak tricky itu class Film harus di-require SEBELUM session_start(),
  soalnya kalo kebalik php bakal gagal pas mau baca ulang object Film yang
  udah kesimpen di session sebelumnya (jadi error / datanya rusak)
- Buat gambar, dipake $_FILES + move_uploaded_file() buat beneran upload
  filenya ke folder uploads/, terus path lokalnya (bukan url) yang disimpen
  ke atribut gambar
- Tambah/update lewat form HTML biasa, hapus & cari lewat link doang
  (action=hapus&id=... / action=cari&keyword=...)

## Dokumentasi

Screenshot/screenrecord bukti tiap bahasa jalan ada di folder
`Dokumentasi/`.
