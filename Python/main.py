"""
==========================================================
 TUGAS PRAKTIKUM 1 - DPBO
 Manajemen Data Bioskop menggunakan OOP (Class, Object,
 Constructor, Encapsulation) - Versi Python (CLI)
==========================================================

Program ini mengelola sekumpulan object Film (disimpan dalam
list Python) melalui menu di terminal. Fitur yang tersedia:
Tambah, Tampilkan, Update, Hapus, dan Cari data film.
"""


class Film:
    """
    Class Film merepresentasikan satu buah film yang diputar di
    bioskop. Seluruh atribut dibuat 'private' dengan awalan __
    (name mangling) sehingga tidak bisa diakses/diubah langsung
    dari luar class (encapsulation); perubahan hanya boleh lewat
    method setter yang disediakan.
    """

    # ================== CONSTRUCTOR ==================
    def __init__(self, id: int, judul: str, genre: str, durasi: int, harga: float, gambar: str):
        # __init__ adalah constructor di Python, otomatis dipanggil
        # setiap kali kita membuat object baru, misal: Film(1, "Inception", ...)
        self.__id = id          # identifier unik tiap film (tidak diubah setelah dibuat)
        self.__judul = judul    # judul film, misal "Inception"
        self.__genre = genre    # genre film, misal "Sci-Fi"
        self.__durasi = durasi  # durasi tayang dalam satuan menit
        self.__harga = harga    # harga tiket film tersebut (dalam Rupiah)
        self.__gambar = gambar  # path file lokal untuk poster film (bukan URL internet)

    # ================== GETTER ==================
    # Getter dipakai untuk MEMBACA nilai atribut private dari luar
    # class, karena atribut dengan awalan __ tidak bisa diakses
    # langsung (misal: film.__judul akan error di luar class).
    def getId(self) -> int:
        return self.__id

    def getJudul(self) -> str:
        return self.__judul

    def getGenre(self) -> str:
        return self.__genre

    def getDurasi(self) -> int:
        return self.__durasi

    def getHarga(self) -> float:
        return self.__harga

    def getGambar(self) -> str:
        return self.__gambar

    # ================== SETTER ==================
    # Setter dipakai untuk MENGUBAH nilai atribut private dari luar
    # class secara terkontrol. Tidak ada setId() karena id bersifat
    # tetap (identifier) dan tidak boleh diubah setelah object dibuat.
    def setJudul(self, judul: str) -> None:
        self.__judul = judul

    def setGenre(self, genre: str) -> None:
        self.__genre = genre

    def setDurasi(self, durasi: int) -> None:
        self.__durasi = durasi

    def setHarga(self, harga: float) -> None:
        self.__harga = harga

    def setGambar(self, gambar: str) -> None:
        self.__gambar = gambar

    def tampilkan(self) -> None:
        """Menampilkan seluruh data 1 object Film ke layar dalam
        format yang rapi. Dipanggil dari fitur Tampilkan & Cari."""
        print(f"ID       : {self.__id}")
        print(f"Judul    : {self.__judul}")
        print(f"Genre    : {self.__genre}")
        print(f"Durasi   : {self.__durasi} menit")
        print(f"Harga    : Rp{self.__harga}")
        print(f"Gambar   : {self.__gambar}")
        print("----------------------------")

    def __del__(self):
        """Destructor: dipanggil otomatis oleh Python saat object
        ini dihapus dari memori (misalnya dikumpulkan oleh garbage
        collector karena sudah tidak ada referensi ke object ini
        lagi). Dikosongkan karena Film tidak memegang resource
        eksternal (seperti file/koneksi) yang perlu ditutup manual."""
        pass


# ================== DATA GLOBAL ==================
# Wadah utama untuk menyimpan seluruh object Film yang sudah dibuat
# (ini adalah "array/list of object" yang diminta di soal).
daftar_film = []

# Counter untuk membuat id baru secara otomatis (auto-increment)
# setiap kali ada data film baru ditambahkan.
next_id = 1


# ================== FUNGSI CRUD ==================

def tambah_data():
    """Fitur 'Tambah Data': meminta input dari user, membuat 1
    object Film baru lewat constructor, lalu memasukkannya ke
    dalam list daftar_film."""
    global next_id
    judul = input("Masukkan judul film      : ")
    genre = input("Masukkan genre           : ")
    durasi = int(input("Masukkan durasi (menit)  : "))
    harga = float(input("Masukkan harga tiket     : "))
    gambar = input("Masukkan path gambar (contoh: img/film1.jpg) : ")

    # Buat object Film baru dengan id otomatis dari next_id.
    film = Film(next_id, judul, genre, durasi, harga, gambar)

    # Masukkan object baru tadi ke dalam list (list of object).
    daftar_film.append(film)

    print(f">> Data berhasil ditambahkan dengan ID: {next_id}")
    next_id += 1  # siapkan id untuk data berikutnya


def tampilkan_data():
    """Fitur 'Tampilkan Data': melakukan looping ke seluruh isi
    list daftar_film, lalu memanggil method tampilkan() tiap
    object satu per satu."""
    if not daftar_film:
        print("Belum ada data film.")
        return
    print(f"=== DAFTAR FILM ({len(daftar_film)} data) ===")
    for film in daftar_film:
        film.tampilkan()


def cari_index_by_id(id_cari: int) -> int:
    """Fungsi bantu (helper) untuk mencari POSISI/INDEX sebuah film
    di dalam list berdasarkan id-nya, menggunakan linear search
    (mengecek satu per satu dari awal sampai ketemu).
    Return: index (0, 1, 2, ...) jika ketemu, atau -1 jika tidak ada."""
    for i, film in enumerate(daftar_film):
        if film.getId() == id_cari:
            return i
    return -1  # tidak ditemukan


def cari_data():
    """Fitur 'Cari Data': user memasukkan id, lalu program mencari
    dan menampilkan 1 data film yang id-nya cocok (tanpa mengubah
    data)."""
    id_cari = int(input("Masukkan ID film yang dicari : "))
    idx = cari_index_by_id(id_cari)
    if idx == -1:
        print(f">> Data dengan ID {id_cari} tidak ditemukan.")
    else:
        print(">> Data ditemukan:")
        daftar_film[idx].tampilkan()


def update_data():
    """Fitur 'Update Data': user memasukkan id film yang ingin
    diubah, lalu untuk setiap field ditampilkan nilai lamanya dan
    user boleh mengetik nilai baru atau mengosongkan input (Enter
    saja) supaya nilai lama tetap dipakai (tidak berubah)."""
    id_update = int(input("Masukkan ID film yang ingin diupdate : "))
    idx = cari_index_by_id(id_update)
    if idx == -1:
        print(f">> Data dengan ID {id_update} tidak ditemukan.")
        return

    # Ambil referensi object Film yang mau diupdate. Karena object
    # di Python diakses lewat referensi, memanggil setter pada
    # variabel film di bawah ini akan langsung mengubah data yang
    # sama persis di dalam list daftar_film.
    film = daftar_film[idx]

    # Untuk tiap atribut: tampilkan nilai lama sebagai referensi,
    # baca input baru, dan HANYA panggil setter kalau input tidak
    # kosong (kalau kosong berarti user memilih untuk skip field ini).
    judul = input(f"Judul baru [{film.getJudul()}] (kosongkan utk skip): ")
    if judul:
        film.setJudul(judul)

    genre = input(f"Genre baru [{film.getGenre()}] (kosongkan utk skip): ")
    if genre:
        film.setGenre(genre)

    durasi = input(f"Durasi baru [{film.getDurasi()}] (kosongkan utk skip): ")
    if durasi:
        film.setDurasi(int(durasi))

    harga = input(f"Harga baru [{film.getHarga()}] (kosongkan utk skip): ")
    if harga:
        film.setHarga(float(harga))

    gambar = input(f"Path gambar baru [{film.getGambar()}] (kosongkan utk skip): ")
    if gambar:
        film.setGambar(gambar)

    print(">> Data berhasil diupdate.")


def hapus_data():
    """Fitur 'Hapus Data': user memasukkan id film yang ingin
    dihapus, program mencari index-nya lalu menghapus elemen
    tersebut dari list menggunakan pop(index)."""
    id_hapus = int(input("Masukkan ID film yang ingin dihapus : "))
    idx = cari_index_by_id(id_hapus)
    if idx == -1:
        print(f">> Data dengan ID {id_hapus} tidak ditemukan.")
        return
    # list.pop(index) menghapus 1 elemen pada posisi tersebut dan
    # otomatis menggeser elemen-elemen setelahnya agar tidak ada
    # "lubang" kosong di tengah list.
    daftar_film.pop(idx)
    print(">> Data berhasil dihapus.")


# ================== MAIN / MENU ==================
def main():
    """Titik masuk program. Menampilkan menu berulang (while True)
    sampai user memilih menu 0 (Keluar)."""
    while True:
        print("\n===== MENU BIOSKOP (Python) =====")
        print("1. Tambah Data Film")
        print("2. Tampilkan Semua Data")
        print("3. Update Data")
        print("4. Hapus Data")
        print("5. Cari Data")
        print("0. Keluar")
        pilihan = input("Pilih menu: ")

        # if-elif berantai untuk menjalankan fungsi sesuai pilihan menu.
        if pilihan == "1":
            tambah_data()
        elif pilihan == "2":
            tampilkan_data()
        elif pilihan == "3":
            update_data()
        elif pilihan == "4":
            hapus_data()
        elif pilihan == "5":
            cari_data()
        elif pilihan == "0":
            print("Terima kasih!")
            break  # keluar dari while True, program selesai
        else:
            print("Pilihan tidak valid.")


# Baris ini memastikan main() hanya dijalankan kalau file ini
# dijalankan langsung (python3 main.py), bukan saat file ini
# di-import sebagai module oleh file Python lain.
if __name__ == "__main__":
    main()
