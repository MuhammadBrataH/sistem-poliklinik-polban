<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PasienLayout from '@/Layouts/PasienLayout.vue';

const props = defineProps({
    riwayat: Array,
});

const formatTanggal = (dateString) => {
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).format(new Date(dateString));
};

const formatJam = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
};

const getStatusColor = (status) => {
    switch (status) {
        case 'selesai': return 'bg-green-100 text-green-800 border-green-200';
        case 'batal': return 'bg-red-100 text-red-800 border-red-200';
        case 'menunggu_obat': return 'bg-blue-100 text-blue-800 border-blue-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <PasienLayout>
        <Head title="Riwayat Kunjungan" />

        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">Riwayat Kunjungan</h2>
                    <p class="text-gray-500 mt-2">Daftar rekam jejak kunjungan Anda ke Poliklinik Polban.</p>
                </div>
                <Link :href="route('pasien.dashboard')" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm text-sm font-medium">
                    Kembali
                </Link>
            </div>

            <div v-if="riwayat.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-bold text-gray-900">Belum Ada Riwayat</h3>
                <p class="text-gray-500 mt-2">Anda belum memiliki riwayat kunjungan medis yang tersimpan di sistem.</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="antrean in riwayat" :key="antrean.id" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col md:flex-row gap-6 items-start md:items-center">
                    
                    <div class="flex-shrink-0 w-20 h-20 rounded-2xl bg-indigo-50 flex flex-col items-center justify-center border border-indigo-100">
                        <span class="text-xs text-indigo-500 font-bold uppercase">Antrean</span>
                        <span class="text-xl font-black text-indigo-700">{{ antrean.nomor_antrean }}</span>
                    </div>

                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider" :class="getStatusColor(antrean.status)">
                                {{ antrean.status.replace('_', ' ') }}
                            </span>
                            <span class="text-sm font-medium text-gray-500 flex items-center gap-1" title="Tanggal Praktik">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ formatTanggal(antrean.jadwal_praktik.tanggal) }}
                            </span>
                            <span class="text-sm font-medium text-gray-500 flex items-center gap-1" title="Waktu pembaruan status">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pukul {{ formatJam(antrean.updated_at) }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900">{{ antrean.jadwal_praktik.dokter.poli.nama_poli }}</h3>
                        <p class="text-gray-600">dr. {{ antrean.jadwal_praktik.dokter.user.name }}</p>
                    </div>

                </div>
            </div>
        </div>
    </PasienLayout>
</template>
