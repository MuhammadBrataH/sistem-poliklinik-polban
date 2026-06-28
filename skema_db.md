// ==========================================
// ENUMS (Daftar Pilihan Tetap)
// ==========================================

Enum user_role {
  super_admin
  admin
  dokter
  pasien
}

Enum pasien_kategori {
  mahasiswa
  pegawai
  keluarga_pegawai
  umum
}

Enum antrean_status {
  menunggu_konfirmasi
  terkonfirmasi
  hadir
  diperiksa
  pending
  menunggu_obat
  selesai
  batal
}

// ==========================================
// DOMAIN 1: AUTH & PENGGUNA
// ==========================================

Table users {
  id int [pk, increment]
  username varchar [unique, not null, note: 'Bisa diisi NIK (untuk pasien), atau NIP/Teks (untuk staf)']
  name varchar [not null]
  password varchar [not null]
  role user_role [not null, default: 'pasien']
  is_active boolean [not null, default: true, note: 'Soft disable akun agar data histori tidak rusak']
  created_at timestamp
  updated_at timestamp
}

// ==========================================
// DOMAIN 2: DATA MASTER (INTI)
// ==========================================

Table pasiens {
  id int [pk, increment]
  user_id int [null, note: 'Kosong jika pasien Go-Show/Umum belum bikin akun web']
  no_rm varchar [unique, not null, note: 'Auto-generate: RM-2026-0001']
  nik varchar [unique, not null]
  nama_lengkap varchar [not null]
  tempat_lahir varchar [not null]
  tanggal_lahir date [not null]
  jenis_kelamin char(1) [not null, note: 'L atau P']
  kategori pasien_kategori [not null]
  nim_nip varchar [null]
  prodi_unit varchar [null, note: 'Jurusan mhs atau Unit kerja pegawai']
  no_telp varchar [not null]
  alamat text [not null]
  status_bpjs varchar [null]
  nama_penanggung varchar [null, note: 'Untuk mahasiswa atau keluarga pegawai']
  hubungan_penanggung varchar [null, note: 'Contoh: Anak, Istri, Suami']
  created_at timestamp
  updated_at timestamp
}

Table polis {
  id int [pk, increment]
  nama_poli varchar [not null, note: 'Contoh: Poli Umum, Poli Gigi']
}

Table dokters {
  id int [pk, increment]
  user_id int [not null]
  poli_id int [not null]
  spesialisasi varchar [not null]
}

Table obats {
  id int [pk, increment]
  nama_obat varchar [not null]
  satuan varchar [not null, note: 'Contoh: Tablet, Botol, Strip']
  stok int [not null, default: 0]
  gambar varchar [null]
}

Table tindakans {
  id int [pk, increment]
  poli_id int [not null, note: 'Membedakan tindakan Gigi dan Umum']
  nama_tindakan varchar [not null]
  tarif int [not null, default: 0, note: 'Dinamis, bisa diubah oleh admin']
}

// ==========================================
// DOMAIN 3: OPERASIONAL & ANTREAN
// ==========================================

Table jadwal_praktiks {
  id int [pk, increment]
  dokter_id int [not null]
  tanggal date [not null]
  jam_mulai time [not null]
  jam_selesai time [not null]
  kuota int [not null]
  sisa_kuota int [not null, note: 'Denormalisasi untuk performa query']
}

Table antreans {
  id int [pk, increment]
  pasien_id int [not null]
  jadwal_praktik_id int [not null]
  nomor_antrean varchar [not null, note: 'Contoh: U-015']
  status antrean_status [not null, default: 'menunggu_konfirmasi']
  created_at timestamp
  updated_at timestamp
}

// ==========================================
// DOMAIN 4: REKAM MEDIS (EMR)
// ==========================================

Table rekam_medis {
  id int [pk, increment]
  antrean_id int [not null]
  pasien_id int [not null]
  dokter_id int [not null]
  
  // Screening
  alergi_obat boolean [not null, default: false]
  gangguan_ginjal boolean [not null, default: false]
  menyusui boolean [not null, default: false]
  
  // SOAP
  keluhan_subjektif text [not null]
  pemeriksaan_objektif text [not null]
  diagnosa text [not null]
  plan_penatalaksanaan text [not null, note: 'Instruksi dokter/nasihat medis']
  butuh_rujukan boolean [not null, default: false]
  
  created_at timestamp
  updated_at timestamp
}

Table rekam_medis_tindakans {
  id int [pk, increment]
  rekam_medis_id int [not null]
  tindakan_id int [not null]
  biaya_saat_ini int [not null, note: 'Snapshot tarif agar history tidak berubah']
}

Table resep_obats {
  id int [pk, increment]
  rekam_medis_id int [not null]
  obat_id int [not null]
  jumlah int [not null]
  aturan_pakai varchar [not null, note: 'Contoh: 3x Sehari Sesudah Makan']
}

Table template_rujukans {
  id int [pk, increment]
  nama_template varchar [not null, note: 'Misal: Rujukan Standar BPJS']
  isi_template text [not null, note: 'Teks/HTML dengan placeholders']
  is_active boolean [not null, default: true]
}

Table rujukans {
  id int [pk, increment]
  rekam_medis_id int [unique, not null, note: 'UNIQUE Constraint 1-to-1']
  rs_tujuan varchar [not null]
  poli_tujuan varchar [not null]
  catatan text [null]
  created_at timestamp
}

// ==========================================
// RELATIONS (Foreign Keys)
// ==========================================

// Relasi 1-to-1
Ref: users.id - pasiens.user_id
Ref: users.id - dokters.user_id
Ref: antreans.id - rekam_medis.antrean_id
Ref: rekam_medis.id - rujukans.rekam_medis_id

// Relasi 1-to-Many
Ref: polis.id < dokters.poli_id
Ref: polis.id < tindakans.poli_id
Ref: dokters.id < jadwal_praktiks.dokter_id
Ref: pasiens.id < antreans.pasien_id
Ref: jadwal_praktiks.id < antreans.jadwal_praktik_id
Ref: pasiens.id < rekam_medis.pasien_id
Ref: dokters.id < rekam_medis.dokter_id
Ref: rekam_medis.id < resep_obats.rekam_medis_id
Ref: obats.id < resep_obats.obat_id
Ref: rekam_medis.id < rekam_medis_tindakans.rekam_medis_id
Ref: tindakans.id < rekam_medis_tindakans.tindakan_id