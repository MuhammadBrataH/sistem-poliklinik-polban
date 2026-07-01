<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PasienLayout from '@/Layouts/PasienLayout.vue';

const props = defineProps({
    antreanAktifSaya: Object,
    antreanSedangBerjalan: Object,
});

// Helper untuk format tanggal
const formatTanggal = (dateString) => {
    if (!dateString) return '-';
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <PasienLayout>
        <Head title="Dashboard Pasien" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Antrean Saya</h2>
                    <p class="text-gray-500 text-sm mt-1">Pantau status antrean Anda hari ini.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <Link
                        :href="route('pasien.history')"
                        class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl shadow-sm transition-colors text-center"
                    >
                        Riwayat Kunjungan
                    </Link>
                    
                    <Link
                        v-if="!antreanAktifSaya"
                        :href="route('pasien.antrean.create')"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-md transition-colors text-center"
                    >
                        + Ambil Antrean Baru
                    </Link>
                </div>
            </div>

            <!-- Jika tidak ada antrean -->
            <div v-if="!antreanAktifSaya" class="bg-indigo-50 border border-indigo-100 rounded-2xl p-8 text-center">
                <svg class="w-16 h-16 text-indigo-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-lg font-bold text-indigo-900 mb-2">Belum Ada Antrean Aktif</h3>
                <p class="text-indigo-700 mb-6">Anda tidak memiliki jadwal periksa untuk hari ini. Silakan ambil antrean baru jika Anda ingin berobat.</p>
                <Link :href="route('pasien.antrean.create')" class="inline-block px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    Daftar Antrean Sekarang
                </Link>
            </div>

            <!-- Jika ADA antrean -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kartu Antrean Saya -->
                <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-3xl p-1 shadow-lg">
                    <div class="bg-white rounded-[23px] h-full overflow-hidden flex flex-col relative">
                        <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex justify-between items-center">
                            <span class="font-bold text-indigo-900">Tiket Antrean Anda</span>
                            <span class="px-3 py-1 bg-white text-indigo-700 text-xs font-bold rounded-full border border-indigo-200">
                                {{ formatTanggal(antreanAktifSaya.jadwal_praktik.tanggal) }}
                            </span>
                        </div>
                        
                        <div class="p-8 text-center flex-grow flex flex-col justify-center">
                            <p class="text-sm text-gray-500 mb-1 uppercase tracking-widest font-semibold">Nomor Antrean</p>
                            <h1 class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 my-2">
                                {{ antreanAktifSaya.nomor_antrean }}
                            </h1>
                            <p class="text-lg font-bold text-gray-800 mt-2">{{ antreanAktifSaya.jadwal_praktik.dokter.poli.nama_poli }}</p>
                            <p class="text-gray-500">dr. {{ antreanAktifSaya.jadwal_praktik.dokter.user.name }}</p>
                            
                            <div class="mt-6 inline-block mx-auto px-4 py-2 rounded-lg bg-gray-100 text-sm font-semibold border border-gray-200">
                                Status: 
                                <span class="uppercase" :class="{
                                    'text-yellow-600': antreanAktifSaya.status === 'menunggu_konfirmasi',
                                    'text-green-600': antreanAktifSaya.status === 'hadir',
                                    'text-blue-600': antreanAktifSaya.status === 'diperiksa'
                                }">
                                    {{ antreanAktifSaya.status.replace('_', ' ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Queue Tracker -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col justify-center items-center text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Live Tracker</h3>
                    <p class="text-gray-500 text-sm mb-6">Nomor antrean yang SEDANG DIPERIKSA di poli ini sekarang:</p>
                    
                    <div v-if="antreanSedangBerjalan" class="w-full bg-blue-50 border border-blue-200 rounded-2xl p-6">
                        <h2 class="text-5xl font-black text-blue-700">{{ antreanSedangBerjalan.nomor_antrean }}</h2>
                        <p class="mt-2 text-blue-600 font-medium animate-pulse">Sedang ditangani dokter...</p>
                    </div>
                    
                    <div v-else class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <h2 class="text-4xl font-bold text-gray-400">---</h2>
                        <p class="mt-2 text-gray-500 text-sm">Belum ada pasien yang diperiksa atau dokter sedang bersiap.</p>
                    </div>
                </div>
            </div>
        </div>
    </PasienLayout>
</template>
