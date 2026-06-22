# Product Requirements Document (PRD)

**Proyek:** Sistem Informasi Poliklinik Polban (MVP)  
**Platform:** Web Application (Responsive / Mobile-First untuk Pasien, Desktop untuk Staf)  
**Versi:** 1.0  
**Tanggal:** 22 Juni 2026  

---

## 1. Ringkasan Proyek (Executive Summary)
Sistem Informasi Poliklinik Polban adalah aplikasi berbasis web yang dirancang khusus untuk mendigitalisasi seluruh proses operasional di lingkungan klinik. Cakupan sistem ini meliputi proses pendaftaran pasien, manajemen antrean, rekam medis elektronik (Electronic Medical Record - EMR), pengelolaan apotek/farmasi, hingga pencetakan tagihan tindakan medis. 

Sistem ini dibangun dengan arsitektur modern yang mendukung penanganan antrean otomatis berbasis metode *First Come First Serve* (FCFS). Fleksibilitas sistem dirancang sedemikian rupa agar dapat memproses dan mengelompokkan berbagai kategori pasien, mulai dari mahasiswa, pegawai internal, hingga masyarakat umum di sekitar lingkungan kampus Polban.

## 2. Tujuan MVP (Minimum Viable Product)
Pengembangan fase MVP ini difokuskan pada penyelesaian masalah krusial operasional dengan target:
* **Eliminasi Antrean Fisik:** Menghilangkan penumpukan dan kepadatan pasien di ruang tunggu klinik melalui sistem pemantauan dan reservasi digital.
* **Digitalisasi Rekam Medis:** Memastikan seluruh data medis pasien (format SOAP) tersimpan secara digital, permanen, terstruktur, dan aman dari risiko kehilangan dokumen fisik.
* **Integritas Data Keuangan & Laporan:** Menyediakan laporan kunjungan serta laporan keuangan dasar yang kebal terhadap perubahan harga master di masa depan dengan menerapkan teknik *Snapshot Pricing* (Snapshot Pivot).
* **Kesiapan Integrasi Eksternal:** Mempersiapkan fondasi database yang kuat (seperti standardisasi kewajiban pengisian NIK) guna mempermudah integrasi dengan API BPJS (P-Care) pada fase pengembangan lanjutan.

## 3. Tumpukan Teknologi (Tech Stack)
Pilihan teknologi yang digunakan disesuaikan untuk menjamin kecepatan performa, kemudahan pemeliharaan, serta fleksibilitas pengembangan:
* **Backend:** Laravel (PHP Framework)
* **Database:** MySQL / MariaDB
* **Frontend:** Blade Templates / Vue.js via Inertia.js (menyesuaikan dengan kenyamanan operasional developer)
* **Export Engine:** * `dompdf`: Digunakan khusus untuk melakukan render berkas PDF pada dokumen Surat Rujukan dan Tagihan Tindakan.
    * `Laravel Excel`: Digunakan untuk menghasilkan laporan kunjungan pasien dalam format spreadsheet (`.xlsx`).

## 4. Peran & Hak Akses (User Roles)
Sistem membagi pengguna ke dalam 4 peran utama dengan menggunakan mekanisme pengenal unik (*credential login*) yang fleksibel berbasis identitas pengguna:
* **Mahasiswa:** Login menggunakan Nomor Induk Mahasiswa (**NIM**).
* **Pegawai:** Login menggunakan Nomor Induk Pegawai (**NIP**).
* **Umum:** Login menggunakan Nomor Induk Kependudukan (**NIK**).

### Matriks Akses Pengguna:
1.  **Pasien:**
    * Mengelola informasi profil mandiri (data pribadi, riwayat alergi, penanggung jawab).
    * Melakukan reservasi nomor antrean secara online.
    * Melihat riwayat rekam medis dasar dan memantau pergerakan antrean.
2.  **Admin (Front Office / Apotek):**
    * Mengelola antrean fisik di lokasi dan mendaftarkan pasien jalur *go-show* (langsung datang).
    * Melakukan validasi status kehadiran pasien.
    * Mengelola proses peracikan dan penyerahan obat kepada pasien.
    * Mencetak lembar tagihan tindakan medis.
3.  **Dokter:**
    * Memanggil pasien berdasarkan nomor urut antrean aktif.
    * Melakukan proses *screening* awal kesehatan pasien.
    * Mengisi formulir EMR dengan format SOAP secara komprehensif.
    * Memberikan instruksi medis, menerbitkan surat rujukan, serta meresepkan kombinasi obat.
4.  **Super Admin:**
    * Memiliki kendali penuh atas manajemen data master (manajemen akun pengguna, konfigurasi harga tindakan, dan data poliklinik).
    * Memantau metrik operasional melalui dasbor analitik global.

## 5. Fitur Inti (Core Features)

### A. Modul Pendaftaran & Antrean (Queue System)
* **Registrasi Komprehensif:** Formulir input profil pasien disesuaikan secara presisi dengan lembar fisik klinik, mencakup Data Pribadi, Program Studi/Unit Kerja, Pihak Penanggung Jawab, serta Riwayat Alergi.
* **Reservasi Online FCFS:** Pasien dapat memilih poliklinik tujuan dan hari kunjungan. Sistem secara otomatis menerbitkan Nomor Antrean berurutan tanpa opsi pemilihan jam kedatangan manual demi menjaga keadilan antrean.
* **Live Queue Tracker:** Dashboard interaktif yang memungkinkan pasien memantau nomor antrean yang sedang dilayani oleh dokter di dalam ruangan secara *real-time*.
* **Manajemen Status Antrean:** Fleksibilitas bagi Admin FO untuk mengubah status antrean (`Hadir`, `Menunggu Konfirmasi`, `Batal`) dengan fitur *state jumping* (lompatan status) guna mengatasi kondisi dinamis di lapangan.
* **WhatsApp Redirect:** Fitur pengiriman pesan pengingat instan kepada pasien menggunakan tautan *shortcut API Web WhatsApp* manual yang disediakan di panel Admin FO.

### B. Modul Rekam Medis Elektronik (EMR)
* **Screening Cepat:** Penyediaan komponen *checkbox* khusus yang mencolok untuk mendeteksi kondisi kritis pasien sejak awal: Alergi Obat, Gangguan Ginjal, dan Kondisi Menyusui.
* **Form SOAP Lengkap:** Form entri terstruktur bagi dokter untuk menginput komponen:
    * *Subjective* (S): Keluhan utama pasien.
    * *Objective* (O): Hasil pemeriksaan fisik dan tanda vital.
    * *Assessment* (A): Diagnosa penyakit.
    * *Plan* (P): Rencana terapi, tindakan, dan penatalaksanaan pasien.
* **Surat Rujukan Dinamis:** Pembuatan cetakan surat rujukan otomatis yang memanfaatkan mesin template teks. Pola kalimat rujukan dapat disesuaikan secara dinamis oleh admin melalui penempatan komponen *placeholder* variabel pasien.

### C. Modul Tindakan & Farmasi
* **Master Tindakan Dinamis:** Pemisahan katalog dan tarif tindakan medis berdasarkan jenis Poliklinik (contoh: Poli Gigi vs Poli Umum).
* **Snapshot Pricing (Anti-Korupsi Data):** Saat pemeriksaan selesai, nominal harga tindakan saat itu akan disalin secara statis ke dalam tabel pivot transaksi. Hal ini memastikan bahwa laporan keuangan masa lalu tidak akan berubah/mengalami kerusakan data meskipun di kemudian hari terjadi perubahan atau inflasi pada master data harga tindakan.
* **Cetak Tagihan Multi-Halaman:** Mekanisme cetak dokumen bukti tindakan di mana setiap satu jenis tindakan medis akan otomatis dipisahkan ke dalam satu halaman tersendiri (*page break*) guna mematuhi standar pengarsipan fisik klinik.
* **Logika Penyerahan Obat:** Pengurangan stok inventori pada tabel `obats` dilakukan secara otomatis dan seketika (*real-time execution*) setelah staf apotek menekan tombol konfirmasi "Selesai" pada menu penyerahan obat.

### D. Modul Pelaporan (Reporting)
* **Export Excel Kunjungan:** Penarikan data laporan kunjungan secara langsung (*on-the-fly*) dari relasi database tanpa membuat tabel fisik redundan. Hasil ekspor menghasilkan format spreadsheet yang memuat struktur kolom: Nomor, Tanggal, Nama Pasien, Penanggung, Jurusan/Unit, Keterangan, Obat yang Diberikan, dan Diagnosa Akhir, sesuai dengan format buku besar fisik klinik.

## 6. Di Luar Cakupan MVP (Out of Scope)
Untuk menjamin proses pengembangan berjalan tepat waktu dan sesuai dengan jadwal rilis, fitur-fitur di bawah ini disepakati untuk **TIDAK** dimasukkan ke dalam ruang lingkup Versi 1.0:
* Sistem Kasir / *Point of Sales* (POS) yang mengelola status pelunasan kompleks (Lunas/Piutang/Metode Pembayaran Digital).
* Integrasi otomatis berbasis Webhook / API WhatsApp Gateway pihak ketiga (semua komunikasi menggunakan *redirect URL* manual).
* Sinkronisasi dua arah dengan API P-Care BPJS Kesehatan (pada fase ini hanya sebatas penyediaan struktur kolom pendukung pada database).
* Modul log kartu stok mendalam untuk manajemen inventori obat (seperti pencatatan data *supplier*, harga beli, dan berita acara barang masuk).
