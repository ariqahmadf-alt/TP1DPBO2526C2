<?php
/**
 * ==========================================================
 *  TUGAS PRAKTIKUM 1 - DPBO
 *  Class Film - digunakan oleh versi Web (index.php)
 * ==========================================================
 *
 * Class Film merepresentasikan satu buah film yang diputar di
 * bioskop. Seluruh atribut dibuat 'private' (encapsulation) agar
 * tidak bisa diubah sembarangan dari luar class; perubahan hanya
 * boleh lewat method setter yang disediakan.
 *
 * PENTING: file ini harus di-require SEBELUM session_start()
 * dipanggil di index.php, supaya PHP tahu bentuk/struktur class
 * Film saat meng-unserialize object yang tersimpan di $_SESSION
 * dari request sebelumnya.
 */
class Film {
    // ---- Atribut (private) ----
    private $id;      // identifier unik tiap film (tidak diubah setelah dibuat)
    private $judul;    // judul film, misal "Inception"
    private $genre;    // genre film, misal "Sci-Fi"
    private $durasi;   // durasi tayang dalam satuan menit
    private $harga;    // harga tiket film tersebut (dalam Rupiah)
    private $gambar;   // path file lokal poster film (relatif terhadap folder PHP, bukan URL)

    /**
     * Constructor: dipanggil otomatis setiap kali kita membuat
     * object baru dengan "new Film(...)". Dipakai saat fitur
     * "Tambah Data" di index.php.
     */
    public function __construct($id, $judul, $genre, $durasi, $harga, $gambar) {
        $this->id = $id;
        $this->judul = $judul;
        $this->genre = $genre;
        $this->durasi = $durasi;
        $this->harga = $harga;
        $this->gambar = $gambar;
    }

    // ---------- Getter ----------
    // Getter dipakai untuk MEMBACA nilai atribut private dari luar
    // class (misal untuk ditampilkan ke tabel HTML di index.php).
    public function getId() { return $this->id; }
    public function getJudul() { return $this->judul; }
    public function getGenre() { return $this->genre; }
    public function getDurasi() { return $this->durasi; }
    public function getHarga() { return $this->harga; }
    public function getGambar() { return $this->gambar; }

    // ---------- Setter ----------
    // Setter dipakai untuk MENGUBAH nilai atribut private dari luar
    // class secara terkontrol, dipakai saat fitur "Update Data".
    // Tidak ada setId() karena id bersifat tetap (identifier) dan
    // tidak boleh diubah setelah object dibuat.
    public function setJudul($judul) { $this->judul = $judul; }
    public function setGenre($genre) { $this->genre = $genre; }
    public function setDurasi($durasi) { $this->durasi = $durasi; }
    public function setHarga($harga) { $this->harga = $harga; }
    public function setGambar($gambar) { $this->gambar = $gambar; }
}
