<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PasienLayout from '@/Layouts/PasienLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    jadwals: Array,
});

const form = useForm({
    jadwal_praktik_id: '',
});

const getLocalToday = () => {
    const now = new Date();
    const formatter = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Asia/Jakarta', year: 'numeric', month: '2-digit', day: '2-digit'
    });
    const parts = formatter.formatToParts(now);
    const p = {};
    parts.forEach(part => { p[part.type] = part.value; });
    return `${p.year}-${p.month}-${p.day}`;
};

const filterDate = ref(getLocalToday());

// Group jadwals by Poli for easier selection (filtered by date)
const groupedByPoli = computed(() => {
    const groups = {};
    // Potong substring T00:00:00Z dari tanggal bawaan Laravel agar bisa dibandingkan dengan string filterDate (YYYY-MM-DD)
    const filteredJadwals = props.jadwals.filter(j => j.tanggal.substring(0, 10) === filterDate.value);
    
    filteredJadwals.forEach(j => {
        const poliName = j.dokter.poli.nama_poli;
        if (!groups[poliName]) groups[poliName] = [];
        groups[poliName].push(j);
    });
    return groups;
});

const selectedPoli = ref('');

import { watch } from 'vue';
watch(filterDate, () => {
    selectedPoli.value = '';
    form.jadwal_praktik_id = '';
});

const togglePoli = (poliName) => {
    if (selectedPoli.value === poliName) {
        selectedPoli.value = '';
    } else {
        selectedPoli.value = poliName;
    }
    form.jadwal_praktik_id = '';
};

const isJadwalExpired = (jadwal) => {
    // 1. Ambil waktu HARI INI secara spesifik di zona Jakarta (WIB)
    const now = new Date();
    
    // Ekstrak waktu WIB menggunakan Intl (menghindari bug zona waktu browser)
    const formatter = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Asia/Jakarta',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
    
    const parts = formatter.formatToParts(now);
    const p = {};
    parts.forEach(part => { p[part.type] = part.value; });
    
    const todayStr = `${p.year}-${p.month}-${p.day}`;
    let currentHour = parseInt(p.hour);
    if (currentHour === 24) currentHour = 0; // Fix untuk browser tertentu
    const currentMinute = parseInt(p.minute);
    
    // 2. Bersihkan tanggal jadwal (membuang jam dari ISO string Laravel misal T00:00:00.000000Z)
    const jadwalTanggal = jadwal.tanggal.substring(0, 10);
    
    // 3. Logika Perbandingan
    if (jadwalTanggal < todayStr) return true;
    
    if (jadwalTanggal === todayStr) {
        const [endHour, endMinute] = jadwal.jam_selesai.split(':');
        const endTotalMinutes = parseInt(endHour) * 60 + parseInt(endMinute);
        const currentTotalMinutes = currentHour * 60 + currentMinute;
        
        // Expired jika waktu saat ini (WIB) sudah melebihi atau sama dengan jam selesai
        return currentTotalMinutes >= endTotalMinutes;
    }
    
    return false; // Jadwal di masa depan (besok dsb)
};

const isJadwalDisabled = (jadwal) => {
    return jadwal.sisa_kuota <= 0 || isJadwalExpired(jadwal);
};

const getDisabledReason = (jadwal) => {
    const noKuota = jadwal.sisa_kuota <= 0;
    const expired = isJadwalExpired(jadwal);
    
    if (noKuota && expired) return 'Waktu praktik telah lewat dan kuota habis';
    if (noKuota) return 'Kuota antrean habis';
    if (expired) return 'Waktu praktik telah berakhir';
    return '';
};

const toggleJadwal = (jadwalId) => {
    // Cari object jadwal
    const jadwal = props.jadwals.find(j => j.id === jadwalId);
    if (jadwal && isJadwalDisabled(jadwal)) return; // Prevent selection

    if (form.jadwal_praktik_id === jadwalId) {
        form.jadwal_praktik_id = '';
    } else {
        form.jadwal_praktik_id = jadwalId;
    }
};

// Filter jadwals based on selected poli
const availableJadwals = computed(() => {
    if (!selectedPoli.value) return [];
    return groupedByPoli.value[selectedPoli.value];
});

const submit = () => {
    form.post(route('pasien.antrean.store'));
};

const formatTanggal = (dateString) => {
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).format(new Date(dateString));
};
</script>

<template>
    <PasienLayout>
        <Head title="Pendaftaran Antrean" />

        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link :href="route('pasien.dashboard')" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1 mb-4">
                    &larr; Kembali ke Dashboard
                </Link>
                <h2 class="text-3xl font-extrabold text-gray-900">Pendaftaran Antrean</h2>
                <p class="text-gray-600 mt-2">Pilih poliklinik dan jadwal dokter yang tersedia. Sistem antrean menggunakan metode First Come First Serve.</p>
            </div>

            <!-- Pesan Error / Success -->
            <div v-if="$page.props.flash?.error" class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                {{ $page.props.flash.error }}
            </div>

            <div class="bg-white shadow-xl shadow-gray-200/50 rounded-3xl overflow-hidden border border-gray-100">
                <form @submit.prevent="submit" class="p-6 sm:p-10 space-y-8">
                    
                    <!-- Tanggal Kunjungan -->
                    <div class="mb-4">
                        <label class="block text-lg font-bold text-gray-800 mb-2">Tanggal Kunjungan</label>
                        <input 
                            type="date" 
                            v-model="filterDate"
                            :min="getLocalToday()"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm sm:text-lg w-full sm:w-auto"
                        >
                    </div>

                    <!-- Langkah 1: Pilih Poli -->
                    <div class="pt-6 border-t border-gray-100">
                        <label class="block text-lg font-bold text-gray-800 mb-4">1. Pilih Poliklinik Tujuan</label>
                        
                        <div v-if="Object.keys(groupedByPoli).length === 0" class="p-6 bg-gray-50 border border-dashed border-gray-300 rounded-xl text-center text-gray-500">
                            Tidak ada jadwal praktik yang tersedia pada tanggal ini.
                        </div>
                        
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="(jadwalList, poliName) in groupedByPoli" :key="poliName"
                                @click="togglePoli(poliName)"
                                class="relative flex flex-col p-5 cursor-pointer rounded-2xl border-2 transition-all select-none"
                                :class="selectedPoli === poliName ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'"
                            >
                                <span class="font-bold text-gray-900 text-lg">{{ poliName }}</span>
                                <span class="text-sm text-gray-500 mt-1">{{ jadwalList.length }} jadwal</span>
                                
                                <div v-if="selectedPoli === poliName" class="absolute top-4 right-4 text-indigo-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Langkah 2: Pilih Jadwal -->
                    <div v-if="selectedPoli" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <label class="block text-lg font-bold text-gray-800 mb-4">2. Pilih Jadwal Praktik</label>
                        <div class="space-y-4">
                            <div v-for="jadwal in availableJadwals" :key="jadwal.id"
                                @click="toggleJadwal(jadwal.id)"
                                class="relative flex flex-col sm:flex-row sm:items-center justify-between p-5 rounded-2xl border-2 transition-all select-none"
                                :class="[
                                    isJadwalDisabled(jadwal)
                                        ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed'
                                        : 'cursor-pointer ' + (form.jadwal_praktik_id === jadwal.id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-white')
                                ]"
                            >
                                <div>
                                    <h4 class="font-bold text-gray-900">dr. {{ jadwal.dokter.user.name }}</h4>
                                    <div class="text-sm text-gray-600 flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ formatTanggal(jadwal.tanggal) }}
                                    </div>
                                    <div class="text-sm text-gray-600 flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ jadwal.jam_mulai.substring(0,5) }} - {{ jadwal.jam_selesai.substring(0,5) }} WIB
                                    </div>
                                </div>
                                
                                <div class="mt-4 sm:mt-0 text-left sm:text-right">
                                    <div v-if="isJadwalDisabled(jadwal)" class="text-xs font-bold text-red-600 bg-red-100 px-3 py-1 rounded-full inline-block">
                                        {{ getDisabledReason(jadwal) }}
                                    </div>
                                    <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold"
                                        :class="jadwal.sisa_kuota > 5 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                        Sisa Kuota: {{ jadwal.sisa_kuota }}
                                    </span>
                                </div>

                                <div v-if="form.jadwal_praktik_id === jadwal.id" class="absolute top-4 right-4 sm:top-1/2 sm:-translate-y-1/2 text-indigo-600 hidden sm:block">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.jadwal_praktik_id" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-100">
                        <button 
                            type="submit" 
                            :disabled="!form.jadwal_praktik_id || form.processing"
                            class="w-full py-4 px-6 text-white font-bold text-lg rounded-xl transition-all flex items-center justify-center gap-2"
                            :class="form.jadwal_praktik_id ? 'bg-indigo-600 hover:bg-indigo-700 hover:-translate-y-1 shadow-lg shadow-indigo-200' : 'bg-gray-300 cursor-not-allowed'"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Konfirmasi & Ambil Antrean</span>
                        </button>
                        <p class="text-center text-sm text-gray-500 mt-4">Pastikan Anda datang 15 menit sebelum waktu praktik berakhir.</p>
                    </div>

                </form>
            </div>
        </div>
    </PasienLayout>
</template>
