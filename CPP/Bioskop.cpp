/*
 * ==========================================================
 *  TUGAS PRAKTIKUM 1 - DPBO
 *  Manajemen Data Bioskop menggunakan OOP (Class, Object,
 *  Constructor, Encapsulation) - Versi C++ (CLI)
 * ==========================================================
 *
 * Program ini mengelola sekumpulan object Film (disimpan dalam
 * vector<Film>) melalui menu di terminal. Fitur yang tersedia:
 * Tambah, Tampilkan, Update, Hapus, dan Cari data film.
 */

#include <iostream>
#include <vector>
#include <string>
using namespace std;

// ================== CLASS FILM ==================
/*
 * Class Film merepresentasikan satu buah film yang diputar di
 * bioskop. Seluruh atribut dibuat 'private' (encapsulation) agar
 * tidak bisa diubah sembarangan dari luar class; perubahan hanya
 * boleh lewat method setter yang disediakan.
 */
class Film {
private:
    // ---- Atribut (private) ----
    int id;         // identifier unik tiap film (tidak diubah setelah dibuat)
    string judul;   // judul film, misal "Inception"
    string genre;   // genre film, misal "Sci-Fi"
    int durasi;     // durasi tayang dalam satuan menit
    double harga;   // harga tiket film tersebut (dalam Rupiah)
    string gambar;  // path file lokal untuk poster film (bukan URL internet)

public:
    // ---------- Constructor ----------

    // Constructor kosong (default). Dipanggil kalau kita ingin
    // membuat object Film tanpa langsung mengisi datanya.
    Film() {}

    // Constructor berparameter. Dipanggil saat kita ingin langsung
    // membuat object Film lengkap dengan datanya (ini yang dipakai
    // saat fitur "Tambah Data").
    // this-> dipakai untuk membedakan atribut class dengan parameter
    // yang kebetulan punya nama sama.
    Film(int id, string judul, string genre, int durasi, double harga, string gambar) {
        this->id = id;
        this->judul = judul;
        this->genre = genre;
        this->durasi = durasi;
        this->harga = harga;
        this->gambar = gambar;
    }

    // ---------- Getter ----------
    // Getter dipakai untuk MEMBACA nilai atribut private dari luar
    // class, karena atribut private tidak bisa diakses langsung
    // (misal: objek.id akan error, harus lewat objek.getId()).
    int getId() { return id; }
    string getJudul() { return judul; }
    string getGenre() { return genre; }
    int getDurasi() { return durasi; }
    double getHarga() { return harga; }
    string getGambar() { return gambar; }

    // ---------- Setter ----------
    // Setter dipakai untuk MENGUBAH nilai atribut private dari luar
    // class secara terkontrol. Perhatikan: tidak ada setId(), karena
    // id bersifat tetap (identifier) dan tidak boleh diubah setelah
    // object dibuat.
    void setJudul(string judul) { this->judul = judul; }
    void setGenre(string genre) { this->genre = genre; }
    void setDurasi(int durasi) { this->durasi = durasi; }
    void setHarga(double harga) { this->harga = harga; }
    void setGambar(string gambar) { this->gambar = gambar; }

    // Method untuk menampilkan seluruh data 1 object Film ke layar
    // dalam format yang rapi. Dipanggil dari fitur Tampilkan & Cari.
    void tampilkan() {
        cout << "ID       : " << id << endl;
        cout << "Judul    : " << judul << endl;
        cout << "Genre    : " << genre << endl;
        cout << "Durasi   : " << durasi << " menit" << endl;
        cout << "Harga    : Rp" << harga << endl;
        cout << "Gambar   : " << gambar << endl;
        cout << "----------------------------" << endl;
    }

    // Destructor: dipanggil otomatis oleh C++ saat object Film
    // dihapus/keluar dari scope (misalnya saat vector.erase()
    // menghapus elemen, atau program selesai). Di sini kosong
    // karena Film tidak memegang resource khusus (seperti pointer
    // dinamis) yang perlu dibersihkan manual.
    ~Film() {}
};

// ================== DATA GLOBAL ==================
// Wadah utama untuk menyimpan seluruh object Film yang sudah dibuat
// (ini adalah "array/list of object" yang diminta di soal).
vector<Film> daftarFilm;

// Counter untuk membuat id baru secara otomatis (auto-increment)
// setiap kali ada data film baru ditambahkan.
int nextId = 1;

// ================== FUNGSI CRUD ==================

// Fitur "Tambah Data": meminta input dari user, membuat 1 object
// Film baru lewat constructor berparameter, lalu memasukkannya
// ke dalam vector daftarFilm.
void tambahData() {
    string judul, genre, gambar;
    int durasi;
    double harga;

    // cin.ignore() dipakai untuk membuang karakter newline ('\n')
    // yang masih tersisa di buffer input setelah "cin >> pilihan"
    // di menu utama, supaya getline() berikutnya tidak langsung
    // membaca string kosong.
    cin.ignore();
    cout << "Masukkan judul film      : ";
    getline(cin, judul);
    cout << "Masukkan genre           : ";
    getline(cin, genre);
    cout << "Masukkan durasi (menit)  : ";
    cin >> durasi;
    cout << "Masukkan harga tiket     : ";
    cin >> harga;
    cin.ignore(); // buang newline setelah cin >> harga, sebelum getline lagi
    cout << "Masukkan path gambar (contoh: img/film1.jpg) : ";
    getline(cin, gambar);

    // Buat object Film baru menggunakan constructor berparameter,
    // dengan id otomatis dari nextId.
    Film f(nextId, judul, genre, durasi, harga, gambar);

    // Masukkan object baru tadi ke dalam vector (list of object).
    daftarFilm.push_back(f);

    cout << ">> Data berhasil ditambahkan dengan ID: " << nextId << endl;
    nextId++; // siapkan id untuk data berikutnya
}

// Fitur "Tampilkan Data": melakukan looping ke seluruh isi vector
// daftarFilm, lalu memanggil method tampilkan() tiap object satu
// per satu.
void tampilkanData() {
    if (daftarFilm.empty()) {
        cout << "Belum ada data film." << endl;
        return;
    }
    cout << "=== DAFTAR FILM (" << daftarFilm.size() << " data) ===" << endl;
    for (size_t i = 0; i < daftarFilm.size(); i++) {
        daftarFilm[i].tampilkan();
    }
}

// Fungsi bantu (helper) untuk mencari POSISI/INDEX sebuah film di
// dalam vector berdasarkan id-nya, menggunakan linear search
// (mengecek satu per satu dari awal sampai ketemu).
// Return: index (0, 1, 2, ...) jika ketemu, atau -1 jika tidak ada.
int cariIndexById(int id) {
    for (size_t i = 0; i < daftarFilm.size(); i++) {
        if (daftarFilm[i].getId() == id) {
            return (int)i;
        }
    }
    return -1; // tidak ditemukan
}

// Fitur "Cari Data": user memasukkan id, lalu program mencari dan
// menampilkan 1 data film yang id-nya cocok (tanpa mengubah data).
void cariData() {
    int id;
    cout << "Masukkan ID film yang dicari : ";
    cin >> id;
    int idx = cariIndexById(id);
    if (idx == -1) {
        cout << ">> Data dengan ID " << id << " tidak ditemukan." << endl;
    } else {
        cout << ">> Data ditemukan:" << endl;
        daftarFilm[idx].tampilkan();
    }
}

// Fitur "Update Data": user memasukkan id film yang ingin diubah,
// lalu untuk setiap field ditampilkan nilai lamanya dan user boleh
// mengetik nilai baru atau mengosongkan input (Enter saja) supaya
// nilai lama tetap dipakai (tidak berubah).
void updateData() {
    int id;
    cout << "Masukkan ID film yang ingin diupdate : ";
    cin >> id;
    int idx = cariIndexById(id);
    if (idx == -1) {
        cout << ">> Data dengan ID " << id << " tidak ditemukan." << endl;
        return;
    }

    string input;
    cin.ignore(); // buang newline sisa dari "cin >> id"

    // Untuk tiap atribut: tampilkan nilai lama sebagai referensi,
    // baca input baru, dan HANYA panggil setter kalau input tidak
    // kosong (kalau kosong berarti user memilih untuk skip field ini).
    cout << "Judul baru [" << daftarFilm[idx].getJudul() << "] (kosongkan utk skip): ";
    getline(cin, input);
    if (!input.empty()) daftarFilm[idx].setJudul(input);

    cout << "Genre baru [" << daftarFilm[idx].getGenre() << "] (kosongkan utk skip): ";
    getline(cin, input);
    if (!input.empty()) daftarFilm[idx].setGenre(input);

    cout << "Durasi baru [" << daftarFilm[idx].getDurasi() << "] (0 utk skip): ";
    getline(cin, input);
    if (!input.empty()) daftarFilm[idx].setDurasi(stoi(input)); // stoi = string to int

    cout << "Harga baru [" << daftarFilm[idx].getHarga() << "] (0 utk skip): ";
    getline(cin, input);
    if (!input.empty()) daftarFilm[idx].setHarga(stod(input)); // stod = string to double

    cout << "Path gambar baru [" << daftarFilm[idx].getGambar() << "] (kosongkan utk skip): ";
    getline(cin, input);
    if (!input.empty()) daftarFilm[idx].setGambar(input);

    cout << ">> Data berhasil diupdate." << endl;
}

// Fitur "Hapus Data": user memasukkan id film yang ingin dihapus,
// program mencari index-nya lalu menghapus elemen tersebut dari
// vector menggunakan erase().
void hapusData() {
    int id;
    cout << "Masukkan ID film yang ingin dihapus : ";
    cin >> id;
    int idx = cariIndexById(id);
    if (idx == -1) {
        cout << ">> Data dengan ID " << id << " tidak ditemukan." << endl;
        return;
    }
    // vector.erase() menghapus 1 elemen pada posisi (begin() + idx)
    // dan otomatis menggeser elemen-elemen setelahnya agar tidak
    // ada "lubang" kosong di tengah vector.
    daftarFilm.erase(daftarFilm.begin() + idx);
    cout << ">> Data berhasil dihapus." << endl;
}

// ================== MAIN / MENU ==================
// Titik masuk program. Menampilkan menu berulang (do-while) sampai
// user memilih menu 0 (Keluar).
int main() {
    int pilihan;
    do {
        cout << "\n===== MENU BIOSKOP (C++) =====" << endl;
        cout << "1. Tambah Data Film" << endl;
        cout << "2. Tampilkan Semua Data" << endl;
        cout << "3. Update Data" << endl;
        cout << "4. Hapus Data" << endl;
        cout << "5. Cari Data" << endl;
        cout << "0. Keluar" << endl;
        cout << "Pilih menu: ";
        cin >> pilihan;

        // Validasi sederhana: kalau user mengetik input yang bukan
        // angka (misal huruf), cin akan gagal (fail state). Bagian
        // ini membersihkan error tersebut supaya program tidak stuck
        // di infinite loop.
        if (cin.fail()) {
            cin.clear();               // reset status error pada cin
            cin.ignore(10000, '\n');   // buang sisa input yang tidak valid
            cout << "Input tidak valid." << endl;
            continue; // langsung ulangi loop, tampilkan menu lagi
        }

        // switch-case untuk menjalankan fungsi sesuai pilihan menu.
        switch (pilihan) {
            case 1: tambahData(); break;
            case 2: tampilkanData(); break;
            case 3: updateData(); break;
            case 4: hapusData(); break;
            case 5: cariData(); break;
            case 0: cout << "Terima kasih!" << endl; break;
            default: cout << "Pilihan tidak valid." << endl;
        }
    } while (pilihan != 0); // ulangi terus selama user belum memilih Keluar

    return 0;
}
