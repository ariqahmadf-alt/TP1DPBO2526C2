<?php
/**
 * ==========================================================
 *  TUGAS PRAKTIKUM 1 - DPBO
 *  Manajemen Data Bioskop menggunakan OOP - Versi PHP (Web)
 * ==========================================================
 *
 * File ini berperan ganda sebagai "controller" (menangani logika
 * tambah/update/hapus/cari) sekaligus "view" (menampilkan HTML).
 * Data disimpan sementara di $_SESSION (TIDAK memakai database),
 * berupa array of object Film - inilah "array/list of object"
 * yang diminta di soal, versi web-nya.
 */

// Class Film HARUS di-require SEBELUM session_start() dipanggil.
// Alasannya: saat session_start() dijalankan, PHP akan membaca data
// session dari request sebelumnya dan meng-unserialize-nya kembali
// menjadi object PHP. Supaya PHP tahu bentuk/struktur object Film
// (atribut & method apa saja yang dimiliki), class-nya harus sudah
// dikenal PHP lebih dulu. Kalau require ini diletakkan SETELAH
// session_start(), object Film di session bisa gagal ter-unserialize
// dengan benar (biasanya jadi __PHP_Incomplete_Class).
require_once __DIR__ . "/Film.php";
session_start();

// Inisialisasi data session hanya sekali (saat pertama kali diakses,
// misal pertama kali buka halaman ini di browser). Kalau session
// sudah pernah dipakai sebelumnya, data lama tetap dipertahankan.
if (!isset($_SESSION['daftar_film'])) {
    $_SESSION['daftar_film'] = []; // array kosong untuk menampung object Film
}
if (!isset($_SESSION['next_id'])) {
    $_SESSION['next_id'] = 1; // counter id auto-increment, mirip versi CLI
}

// $action menentukan operasi CRUD apa yang sedang dijalankan,
// diambil dari parameter URL ?action=... . Default-nya 'list'
// yaitu hanya menampilkan halaman & tabel data tanpa melakukan
// perubahan apa pun.
$action = $_GET['action'] ?? 'list';

// ================== FUNGSI UPLOAD GAMBAR ==================
/**
 * Menangani upload file gambar poster dari form HTML (<input
 * type="file">) dan menyimpannya sebagai FILE FISIK di folder
 * uploads/ pada server (bukan disimpan sebagai URL ke internet).
 *
 * @param string $fileInputName nama field <input type="file"> di form
 * @return string path lokal relatif (misal "uploads/1699999999_poster.jpg")
 *                 jika upload sukses, atau string kosong "" jika tidak
 *                 ada file yang diupload / gagal.
 */
function uploadGambar($fileInputName) {
    // $_FILES['gambar']['error'] === UPLOAD_ERR_OK berarti user benar-benar
    // memilih & mengupload file, dan prosesnya berjalan tanpa error di
    // sisi PHP (misal ukuran file tidak melebihi batas, dsb).
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $folderUpload = __DIR__ . "/uploads/";

        // Buat folder uploads/ otomatis kalau belum ada, supaya
        // move_uploaded_file() di bawah tidak gagal karena folder
        // tujuannya tidak ditemukan.
        if (!is_dir($folderUpload)) {
            mkdir($folderUpload, 0777, true);
        }

        // Nama file dibuat unik dengan menambahkan timestamp (time())
        // di depan nama file asli, supaya tidak ada file yang saling
        // menimpa (overwrite) kalau ada 2 film upload gambar dengan
        // nama file yang sama persis, misal "poster.jpg".
        $namaFile = time() . "_" . basename($_FILES[$fileInputName]['name']);
        $tujuan = $folderUpload . $namaFile;

        // move_uploaded_file() memindahkan file dari lokasi sementara
        // (tmp_name, dibuat otomatis oleh PHP saat upload) ke lokasi
        // tujuan permanen di folder uploads/.
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $tujuan)) {
            // Yang dikembalikan & disimpan ke atribut gambar adalah
            // PATH LOKAL relatif ("uploads/namafile.jpg"), BUKAN URL
            // eksternal - sesuai ketentuan soal.
            return "uploads/" . $namaFile;
        }
    }
    return ""; // tidak ada file diupload, atau upload gagal
}

// ================== TAMBAH DATA ==================
// Dipicu saat form "Tambah Data Film" di-submit (method POST ke
// index.php?action=tambah). Alurnya: ambil semua input dari form,
// upload gambar (jika ada), buat 1 object Film baru lewat
// constructor, lalu masukkan ke array $_SESSION['daftar_film'].
if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // (int) dan (float) di sini adalah type casting eksplisit,
    // mengubah string dari form (misal "148") menjadi angka asli
    // supaya konsisten dengan tipe atribut di class Film.
    $judul  = trim($_POST['judul'] ?? '');
    $genre  = trim($_POST['genre'] ?? '');
    $durasi = (int)($_POST['durasi'] ?? 0);
    $harga  = (float)($_POST['harga'] ?? 0);
    $gambar = uploadGambar('gambar'); // proses file upload poster

    $id = $_SESSION['next_id'];

    // Buat object Film baru lewat constructor.
    $film = new Film($id, $judul, $genre, $durasi, $harga, $gambar);

    // Masukkan object baru ke dalam array (list of object) yang
    // tersimpan di session.
    $_SESSION['daftar_film'][] = $film;
    $_SESSION['next_id']++; // siapkan id untuk data berikutnya

    // Redirect (Location header) kembali ke halaman utama setelah
    // proses tambah selesai. Pola ini disebut "Post/Redirect/Get":
    // mencegah data terkirim dobel kalau user menekan tombol
    // refresh di browser setelah submit form.
    header("Location: index.php?pesan=" . urlencode("Data berhasil ditambahkan"));
    exit; // hentikan eksekusi script setelah redirect
}

// ================== UPDATE DATA ==================
// Dipicu saat form "Update Data Film" di-submit (method POST ke
// index.php?action=update). Mirip fitur update di versi CLI:
// field yang dikosongkan (empty) tidak diubah, hanya field yang
// diisi ulang yang akan memanggil setter.
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    // Linear search: telusuri array daftar_film satu per satu
    // sampai ditemukan object dengan id yang cocok.
    foreach ($_SESSION['daftar_film'] as $film) {
        if ($film->getId() === $id) {
            // Catatan penting: di PHP, object selalu diakses lewat
            // "handle"/referensi (mirip pointer). Jadi walaupun
            // $film di sini adalah variabel loop biasa (bukan
            // "as &$film"), memanggil method setter pada $film
            // tetap langsung mengubah object ASLI yang ada di
            // dalam $_SESSION['daftar_film'] - bukan salinannya.
            if (!empty($_POST['judul'])) $film->setJudul(trim($_POST['judul']));
            if (!empty($_POST['genre'])) $film->setGenre(trim($_POST['genre']));
            if (!empty($_POST['durasi'])) $film->setDurasi((int)$_POST['durasi']);
            if (!empty($_POST['harga'])) $film->setHarga((float)$_POST['harga']);

            // Gambar hanya diganti kalau user benar-benar mengupload
            // file baru di form update (uploadGambar akan
            // mengembalikan string kosong kalau tidak ada file baru).
            $gambarBaru = uploadGambar('gambar');
            if (!empty($gambarBaru)) $film->setGambar($gambarBaru);

            break; // sudah ketemu & diupdate, tidak perlu lanjut loop
        }
    }
    header("Location: index.php?pesan=" . urlencode("Data berhasil diupdate"));
    exit;
}

// ================== HAPUS DATA ==================
// Dipicu saat tombol "Hapus" di tabel diklik (link GET ke
// index.php?action=hapus&id=X). Mencari object dengan id yang
// cocok, menghapusnya dari array, lalu merapikan ulang index array.
if ($action === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // $index => $film di sini butuh index array-nya juga (bukan
    // cuma object-nya), karena unset() perlu tahu posisi array
    // mana yang mau dihapus.
    foreach ($_SESSION['daftar_film'] as $index => $film) {
        if ($film->getId() === $id) {
            unset($_SESSION['daftar_film'][$index]); // hapus 1 elemen array
            break;
        }
    }

    // unset() pada array PHP meninggalkan "lubang" pada urutan key
    // (misal key 0,1,2 jadi 0,2 setelah key 1 dihapus). array_values()
    // dipakai untuk menyusun ulang key array dari 0 lagi secara
    // berurutan, supaya rapi dan konsisten.
    $_SESSION['daftar_film'] = array_values($_SESSION['daftar_film']);
    header("Location: index.php?pesan=" . urlencode("Data berhasil dihapus"));
    exit;
}

// ================== CARI DATA ==================
// Dipicu saat form pencarian di-submit (method GET ke
// index.php?action=cari&keyword=...). BERBEDA dengan tambah/update/
// hapus, fitur ini TIDAK mengubah data di session sama sekali -
// hanya membuat daftar hasil filter ($hasilCari) untuk ditampilkan.
$hasilCari = null;
if ($action === 'cari' && isset($_GET['keyword']) && trim($_GET['keyword']) !== '') {
    $keyword = strtolower(trim($_GET['keyword']));
    $hasilCari = [];

    // Telusuri seluruh data, cocokkan judul film (huruf kecil semua
    // biar pencarian tidak case-sensitive) dengan keyword yang
    // dicari menggunakan strpos(). strpos() mengembalikan posisi
    // ditemukannya substring, atau false kalau tidak ketemu sama
    // sekali - makanya dicek dengan "!== false".
    foreach ($_SESSION['daftar_film'] as $film) {
        if (strpos(strtolower($film->getJudul()), $keyword) !== false) {
            $hasilCari[] = $film;
        }
    }
}

// ================== AMBIL DATA UNTUK FORM EDIT ==================
// Dipicu saat tombol "Edit" di tabel diklik (link GET ke
// index.php?action=edit&id=X). Mencari 1 object berdasarkan id,
// lalu menyimpannya ke $filmEdit supaya form di bagian HTML bisa
// otomatis terisi dengan data lama (bukan form kosong).
$filmEdit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    foreach ($_SESSION['daftar_film'] as $film) {
        if ($film->getId() === $id) {
            $filmEdit = $film;
            break;
        }
    }
}

// $dataTampil adalah data yang AKAN dirender ke tabel HTML di bawah:
// - kalau user sedang melakukan pencarian (hasilCari tidak null),
//   maka yang ditampilkan adalah hasil filter pencarian saja;
// - kalau tidak, tampilkan semua data seperti biasa.
$dataTampil = $hasilCari !== null ? $hasilCari : $_SESSION['daftar_film'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Data Bioskop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; color: #222; }
        h1 { margin-bottom: 5px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: middle; }
        th { background: #FFC72C; }
        img { max-width: 80px; max-height: 80px; object-fit: cover; }
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; max-width: 500px; border-radius: 6px; }
        .form-box label { font-weight: bold; font-size: 14px; }
        .form-box input { display: block; margin: 4px 0 12px 0; padding: 6px; width: 100%; box-sizing: border-box; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; font-size: 13px; }
        .btn-tambah { background: #222; color: #FFC72C; }
        .btn-edit { background: #2196F3; }
        .btn-hapus { background: #f44336; }
        .btn-cari { background: #4CAF50; }
        .pesan { background: #e6ffe6; color: #256029; padding: 10px; border-radius: 4px; max-width: 500px; }
        .search-box { margin: 15px 0; }
        .search-box input[type=text] { padding: 6px; width: 250px; }
    </style>
</head>
<body>
    <h1>🎬 Manajemen Data Bioskop</h1>
    <p>Asprak 16 DPBO - Tugas Praktikum OOP &amp; Encapsulation</p>

    <?php if (isset($_GET['pesan'])): ?>
        <p class="pesan"><?= htmlspecialchars($_GET['pesan']) ?></p>
    <?php endif; ?>

    <div class="form-box">
        <h2><?= $filmEdit ? "Update Data Film" : "Tambah Data Film" ?></h2>
        <form action="index.php?action=<?= $filmEdit ? 'update' : 'tambah' ?>" method="POST" enctype="multipart/form-data">
            <?php if ($filmEdit): ?>
                <input type="hidden" name="id" value="<?= $filmEdit->getId() ?>">
            <?php endif; ?>

            <label>Judul Film</label>
            <input type="text" name="judul" value="<?= $filmEdit ? htmlspecialchars($filmEdit->getJudul()) : '' ?>" <?= $filmEdit ? '' : 'required' ?>>

            <label>Genre</label>
            <input type="text" name="genre" value="<?= $filmEdit ? htmlspecialchars($filmEdit->getGenre()) : '' ?>" <?= $filmEdit ? '' : 'required' ?>>

            <label>Durasi (menit)</label>
            <input type="number" name="durasi" value="<?= $filmEdit ? $filmEdit->getDurasi() : '' ?>" <?= $filmEdit ? '' : 'required' ?>>

            <label>Harga Tiket (Rp)</label>
            <input type="number" step="0.01" name="harga" value="<?= $filmEdit ? $filmEdit->getHarga() : '' ?>" <?= $filmEdit ? '' : 'required' ?>>

            <label>Gambar Poster (upload file lokal)<?= $filmEdit ? ' - kosongkan jika tidak diganti' : '' ?></label>
            <input type="file" name="gambar" accept="image/*" <?= $filmEdit ? '' : 'required' ?>>

            <button type="submit" class="btn btn-tambah"><?= $filmEdit ? "Update Data" : "Tambah Data" ?></button>
            <?php if ($filmEdit): ?>
                <a href="index.php">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="search-box">
        <form action="index.php" method="GET" style="display:inline;">
            <input type="hidden" name="action" value="cari">
            <input type="text" name="keyword" placeholder="Cari judul film..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <button type="submit" class="btn btn-cari">Cari</button>
            <a href="index.php">Reset</a>
        </form>
    </div>

    <h2>Daftar Film (<?= count($dataTampil) ?> data)</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Genre</th>
            <th>Durasi</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
        <?php if (empty($dataTampil)): ?>
            <tr><td colspan="7">Belum ada data film.</td></tr>
        <?php else: ?>
            <?php // Looping setiap object Film di $dataTampil untuk
                  // dirender menjadi 1 baris <tr> tabel. Ini adalah
                  // contoh "menampilkan seluruh list of object" versi
                  // web, ekuivalen dengan fungsi tampilkanData() di
                  // versi CLI. ?>
            <?php foreach ($dataTampil as $film): ?>
                <tr>
                    <td><?= $film->getId() ?></td>
                    <td>
                        <?php if ($film->getGambar() && file_exists(__DIR__ . "/" . $film->getGambar())): ?>
                            <img src="<?= htmlspecialchars($film->getGambar()) ?>" alt="poster">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($film->getJudul()) ?></td>
                    <td><?= htmlspecialchars($film->getGenre()) ?></td>
                    <td><?= $film->getDurasi() ?> menit</td>
                    <td>Rp<?= number_format($film->getHarga(), 0, ',', '.') ?></td>
                    <td>
                        <a class="btn btn-edit" href="index.php?action=edit&id=<?= $film->getId() ?>">Edit</a>
                        <a class="btn btn-hapus" href="index.php?action=hapus&id=<?= $film->getId() ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
