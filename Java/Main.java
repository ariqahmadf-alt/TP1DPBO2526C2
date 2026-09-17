/*
 * ==========================================================
 *  TUGAS PRAKTIKUM 1 - DPBO
 *  Manajemen Data Bioskop menggunakan OOP (Class, Object,
 *  Constructor, Encapsulation) - Versi Java (CLI)
 * ==========================================================
 *
 * Program ini mengelola sekumpulan object Film (disimpan dalam
 * ArrayList<Film>) melalui menu di terminal. Fitur yang tersedia:
 * Tambah, Tampilkan, Update, Hapus, dan Cari data film.
 */

import java.util.ArrayList;
import java.util.Scanner;

// ================== CLASS FILM ==================
/*
 * Class Film merepresentasikan satu buah film yang diputar di
 * bioskop. Seluruh atribut dibuat 'private' (encapsulation) agar
 * tidak bisa diubah sembarangan dari luar class; perubahan hanya
 * boleh lewat method setter yang disediakan.
 *
 * Class ini sengaja TIDAK dideklarasikan 'public' karena dalam satu
 * file Java hanya boleh ada 1 class public, dan yang public di sini
 * adalah class Main (berisi method main sebagai entry point).
 */
class Film {
    // ---- Atribut (private) ----
    private int id;        // identifier unik tiap film (tidak diubah setelah dibuat)
    private String judul;  // judul film, misal "Inception"
    private String genre;  // genre film, misal "Sci-Fi"
    private int durasi;    // durasi tayang dalam satuan menit
    private double harga;  // harga tiket film tersebut (dalam Rupiah)
    private String gambar; // path file lokal untuk poster film (bukan URL internet)

    // ---------- Constructor ----------

    // Constructor kosong (default). Dipanggil kalau kita ingin
    // membuat object Film tanpa langsung mengisi datanya.
    public Film() {
    }

    // Constructor berparameter. Dipanggil saat kita ingin langsung
    // membuat object Film lengkap dengan datanya (dipakai saat
    // fitur "Tambah Data").
    // this.namaAtribut dipakai untuk membedakan atribut class
    // dengan parameter yang kebetulan punya nama sama.
    public Film(int id, String judul, String genre, int durasi, double harga, String gambar) {
        this.id = id;
        this.judul = judul;
        this.genre = genre;
        this.durasi = durasi;
        this.harga = harga;
        this.gambar = gambar;
    }

    // ---------- Getter ----------
    // Getter dipakai untuk MEMBACA nilai atribut private dari luar
    // class, karena atribut private tidak bisa diakses langsung.
    public int getId() { return id; }
    public String getJudul() { return judul; }
    public String getGenre() { return genre; }
    public int getDurasi() { return durasi; }
    public double getHarga() { return harga; }
    public String getGambar() { return gambar; }

    // ---------- Setter ----------
    // Setter dipakai untuk MENGUBAH nilai atribut private dari luar
    // class secara terkontrol. Tidak ada setId() karena id bersifat
    // tetap (identifier) dan tidak boleh diubah setelah object dibuat.
    public void setJudul(String judul) { this.judul = judul; }
    public void setGenre(String genre) { this.genre = genre; }
    public void setDurasi(int durasi) { this.durasi = durasi; }
    public void setHarga(double harga) { this.harga = harga; }
    public void setGambar(String gambar) { this.gambar = gambar; }

    // Method untuk menampilkan seluruh data 1 object Film ke layar
    // dalam format yang rapi. Dipanggil dari fitur Tampilkan & Cari.
    public void tampilkan() {
        System.out.println("ID       : " + id);
        System.out.println("Judul    : " + judul);
        System.out.println("Genre    : " + genre);
        System.out.println("Durasi   : " + durasi + " menit");
        System.out.println("Harga    : Rp" + harga);
        System.out.println("Gambar   : " + gambar);
        System.out.println("----------------------------");
    }

    // Catatan: Java tidak punya destructor eksplisit seperti C++
    // atau Python. Dulu ada method finalize() yang mirip destructor,
    // tapi sekarang sudah deprecated (tidak disarankan dipakai)
    // sehingga tidak digunakan di sini. Pembersihan object yang
    // sudah tidak dipakai ditangani otomatis oleh Garbage Collector
    // JVM di belakang layar.
}

// ================== MAIN / MENU ==================
/*
 * Class Main adalah entry point program (berisi method main).
 * Class ini BUKAN class OOP inti dari soal (soal minta 1 class
 * yaitu Film) - Main hanya wadah untuk logika menu & memanggil
 * method-method pada object Film.
 */
public class Main {
    // Wadah utama untuk menyimpan seluruh object Film yang sudah
    // dibuat (ini adalah "array/list of object" yang diminta soal).
    static ArrayList<Film> daftarFilm = new ArrayList<>();

    // Counter untuk membuat id baru secara otomatis (auto-increment)
    // setiap kali ada data film baru ditambahkan.
    static int nextId = 1;

    // Scanner dibuat sebagai field static supaya bisa dipakai
    // bersama oleh semua method (tambahData, updateData, dst)
    // tanpa perlu membuat Scanner baru berkali-kali.
    static Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) {
        // pilihan diberi nilai awal -1 (bukan angka menu manapun)
        // supaya variabel ini sudah pasti punya nilai walaupun
        // input pertama user tidak valid dan langsung 'continue'.
        int pilihan = -1;
        do {
            System.out.println("\n===== MENU BIOSKOP (Java) =====");
            System.out.println("1. Tambah Data Film");
            System.out.println("2. Tampilkan Semua Data");
            System.out.println("3. Update Data");
            System.out.println("4. Hapus Data");
            System.out.println("5. Cari Data");
            System.out.println("0. Keluar");
            System.out.print("Pilih menu: ");

            String baris = scanner.nextLine().trim();

            // Validasi sederhana: pastikan input hanya berisi digit
            // angka. Kalau tidak (misal user mengetik huruf), beri
            // pesan error dan tampilkan menu lagi tanpa crash.
            if (!baris.matches("\\d+")) {
                System.out.println("Input tidak valid.");
                continue;
            }
            pilihan = Integer.parseInt(baris);

            // switch-case untuk menjalankan method sesuai pilihan menu.
            switch (pilihan) {
                case 1: tambahData(); break;
                case 2: tampilkanData(); break;
                case 3: updateData(); break;
                case 4: hapusData(); break;
                case 5: cariData(); break;
                case 0: System.out.println("Terima kasih!"); break;
                default: System.out.println("Pilihan tidak valid.");
            }
        } while (pilihan != 0); // ulangi terus selama user belum memilih Keluar

        scanner.close();
    }

    // Fitur "Tambah Data": meminta input dari user, membuat 1 object
    // Film baru lewat constructor berparameter, lalu memasukkannya
    // ke dalam ArrayList daftarFilm.
    static void tambahData() {
        System.out.print("Masukkan judul film      : ");
        String judul = scanner.nextLine();
        System.out.print("Masukkan genre           : ");
        String genre = scanner.nextLine();
        System.out.print("Masukkan durasi (menit)  : ");
        int durasi = Integer.parseInt(scanner.nextLine().trim());
        System.out.print("Masukkan harga tiket     : ");
        double harga = Double.parseDouble(scanner.nextLine().trim());
        System.out.print("Masukkan path gambar (contoh: img/film1.jpg) : ");
        String gambar = scanner.nextLine();

        // Buat object Film baru menggunakan constructor berparameter,
        // dengan id otomatis dari nextId.
        Film f = new Film(nextId, judul, genre, durasi, harga, gambar);

        // Masukkan object baru tadi ke dalam ArrayList (list of object).
        daftarFilm.add(f);

        System.out.println(">> Data berhasil ditambahkan dengan ID: " + nextId);
        nextId++; // siapkan id untuk data berikutnya
    }

    // Fitur "Tampilkan Data": melakukan looping ke seluruh isi
    // ArrayList daftarFilm, lalu memanggil method tampilkan() tiap
    // object satu per satu (menggunakan enhanced for-loop).
    static void tampilkanData() {
        if (daftarFilm.isEmpty()) {
            System.out.println("Belum ada data film.");
            return;
        }
        System.out.println("=== DAFTAR FILM (" + daftarFilm.size() + " data) ===");
        for (Film f : daftarFilm) {
            f.tampilkan();
        }
    }

    // Fungsi bantu (helper) untuk mencari POSISI/INDEX sebuah film
    // di dalam ArrayList berdasarkan id-nya, menggunakan linear
    // search (mengecek satu per satu dari awal sampai ketemu).
    // Return: index (0, 1, 2, ...) jika ketemu, atau -1 jika tidak ada.
    static int cariIndexById(int id) {
        for (int i = 0; i < daftarFilm.size(); i++) {
            if (daftarFilm.get(i).getId() == id) {
                return i;
            }
        }
        return -1; // tidak ditemukan
    }

    // Fitur "Cari Data": user memasukkan id, lalu program mencari
    // dan menampilkan 1 data film yang id-nya cocok (tanpa mengubah
    // data).
    static void cariData() {
        System.out.print("Masukkan ID film yang dicari : ");
        int id = Integer.parseInt(scanner.nextLine().trim());
        int idx = cariIndexById(id);
        if (idx == -1) {
            System.out.println(">> Data dengan ID " + id + " tidak ditemukan.");
        } else {
            System.out.println(">> Data ditemukan:");
            daftarFilm.get(idx).tampilkan();
        }
    }

    // Fitur "Update Data": user memasukkan id film yang ingin diubah,
    // lalu untuk setiap field ditampilkan nilai lamanya dan user
    // boleh mengetik nilai baru atau mengosongkan input (Enter saja)
    // supaya nilai lama tetap dipakai (tidak berubah).
    static void updateData() {
        System.out.print("Masukkan ID film yang ingin diupdate : ");
        int id = Integer.parseInt(scanner.nextLine().trim());
        int idx = cariIndexById(id);
        if (idx == -1) {
            System.out.println(">> Data dengan ID " + id + " tidak ditemukan.");
            return;
        }
        // Ambil referensi object Film yang mau diupdate. Karena
        // object di Java diakses lewat referensi, memanggil setter
        // pada variabel f di bawah ini akan langsung mengubah data
        // yang sama persis di dalam ArrayList daftarFilm.
        Film f = daftarFilm.get(idx);

        // Untuk tiap atribut: tampilkan nilai lama sebagai referensi,
        // baca input baru, dan HANYA panggil setter kalau input tidak
        // kosong (kalau kosong berarti user memilih untuk skip field ini).
        System.out.print("Judul baru [" + f.getJudul() + "] (kosongkan utk skip): ");
        String judul = scanner.nextLine();
        if (!judul.isEmpty()) f.setJudul(judul);

        System.out.print("Genre baru [" + f.getGenre() + "] (kosongkan utk skip): ");
        String genre = scanner.nextLine();
        if (!genre.isEmpty()) f.setGenre(genre);

        System.out.print("Durasi baru [" + f.getDurasi() + "] (kosongkan utk skip): ");
        String durasiStr = scanner.nextLine();
        if (!durasiStr.isEmpty()) f.setDurasi(Integer.parseInt(durasiStr.trim()));

        System.out.print("Harga baru [" + f.getHarga() + "] (kosongkan utk skip): ");
        String hargaStr = scanner.nextLine();
        if (!hargaStr.isEmpty()) f.setHarga(Double.parseDouble(hargaStr.trim()));

        System.out.print("Path gambar baru [" + f.getGambar() + "] (kosongkan utk skip): ");
        String gambar = scanner.nextLine();
        if (!gambar.isEmpty()) f.setGambar(gambar);

        System.out.println(">> Data berhasil diupdate.");
    }

    // Fitur "Hapus Data": user memasukkan id film yang ingin
    // dihapus, program mencari index-nya lalu menghapus elemen
    // tersebut dari ArrayList menggunakan remove(index).
    static void hapusData() {
        System.out.print("Masukkan ID film yang ingin dihapus : ");
        int id = Integer.parseInt(scanner.nextLine().trim());
        int idx = cariIndexById(id);
        if (idx == -1) {
            System.out.println(">> Data dengan ID " + id + " tidak ditemukan.");
            return;
        }
        // ArrayList.remove(int index) menghapus 1 elemen pada
        // posisi tersebut dan otomatis menggeser elemen-elemen
        // setelahnya agar tidak ada "lubang" kosong di tengah list.
        daftarFilm.remove(idx);
        System.out.println(">> Data berhasil dihapus.");
    }
}
