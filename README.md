# Kredit-Aktivitas-Mahasiswa
Program Kredit Aktivitas Kemahasiswaan (KAK) adalah sistem untuk mencatat dan mengelola kegiatan kemahasiswaan yang diikuti oleh mahasiswa. Program ini memungkinkan mahasiswa untuk mendapatkan kredit berdasarkan partisipasi mereka dalam berbagai aktivitas yang diakui oleh perguruan tinggi. Sistem ini juga memfasilitasi monitoring dan laporan kredit yang diperoleh mahasiswa.

## Fitur Utama

### 1. **Pencatatan Aktivitas**
Mahasiswa dapat mendaftar dan mencatat berbagai aktivitas kemahasiswaan yang mereka ikuti. Setiap aktivitas akan diberi bobot kredit yang disesuaikan dengan tingkat kesulitan dan durasi kegiatan.

### 2. **Penghitungan Kredit**
Program ini secara otomatis menghitung jumlah kredit yang diperoleh mahasiswa berdasarkan aktivitas yang mereka lakukan. Aktivitas seperti organisasi, seminar, pelatihan, dan proyek dapat menjadi sumber kredit.

### 3. **Laporan dan Monitoring**
Mahasiswa dapat melihat perkembangan kredit mereka melalui laporan yang terstruktur. Laporan ini mencakup total kredit yang diperoleh serta rincian aktivitas yang diikuti.

### 4. **Integrasi dengan Sistem Akademik**
Aktivitas yang diikuti dan kredit yang diperoleh dapat terintegrasi dengan sistem akademik perguruan tinggi untuk evaluasi semesteran mahasiswa.

### 5. **Notifikasi dan Pengingat**
Program ini akan mengirimkan notifikasi dan pengingat kepada mahasiswa terkait aktivitas yang mereka ikuti dan kredit yang dibutuhkan untuk mencapai tujuan tertentu.

### 6. **Manajemen Admin**
Admin dapat mengelola dan memverifikasi aktivitas mahasiswa, menambah aktivitas baru, serta mengatur bobot kredit untuk masing-masing kegiatan.

## Teknologi yang Digunakan
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP (Laravel atau lainnya)
- **Database**: MySQL

## Instalasi dan Penggunaan

### 1. **Instalasi**

1. Clone repository ini ke dalam folder lokal:
    ```bash
    git clone https://github.com/lukman754/Kredit-Aktivitas-Kemahasiswaan.git
    ```

2. Masuk ke folder project:
    ```bash
    cd Kredit-Aktivitas-Kemahasiswaan
    ```

3. Install dependensi yang diperlukan untuk backend (misalnya, menggunakan Composer untuk PHP):
    ```bash
    composer install
    ```

4. Buat database MySQL dan sesuaikan konfigurasi database pada file `.env`.

5. Jalankan migration untuk membuat tabel yang dibutuhkan:
    ```bash
    php artisan migrate
    ```

6. Jalankan server lokal:
    ```bash
    php artisan serve
    ```

7. Buka aplikasi di browser dengan alamat `http://localhost:8000`.

### 2. **Penggunaan**

1. Mahasiswa dapat mendaftar dan login ke aplikasi.
2. Setelah login, mahasiswa dapat mencatat dan melihat aktivitas yang mereka ikuti.
3. Mahasiswa dapat melihat jumlah kredit yang telah mereka peroleh dari setiap aktivitas.
4. Admin dapat menambah atau mengelola aktivitas yang tersedia untuk mahasiswa.
5. Laporan dan monitoring kredit dapat diakses oleh mahasiswa dan admin untuk evaluasi perkembangan.

## Kontribusi
Jika Anda ingin berkontribusi pada proyek ini, silakan fork repository ini dan kirimkan pull request Anda.

User Page
![kak](https://github.com/user-attachments/assets/09c812e7-e227-4d54-a977-b59a6f86ec2f)
![image](https://github.com/user-attachments/assets/65015cb6-9bed-41ae-97ac-7d9bc83add89)

Admin Page
![screencapture-localhost-web-kak-admin-index-php-2025-01-21-00_31_06](https://github.com/user-attachments/assets/9de769d0-c1a8-4480-9497-ba0b4dcfb79f)
