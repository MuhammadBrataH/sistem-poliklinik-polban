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

// Group jadwals by Poli for easier selection
const groupedByPoli = computed(() => {
    const groups = {};
    props.jadwals.forEach(j => {
        const poliName = j.dokter.poli.nama_poli;
        if (!groups[poliName]) groups[poliName] = [];
        groups[poliName].push(j);
    });
    return groups;
});

const selectedPoli = ref('');

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
                    
                    <!-- Langkah 1: Pilih Poli -->
                    <div>
                        <label class="block text-lg font-bold text-gray-800 mb-4">1. Pilih Poliklinik Tujuan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label v-for="(jadwalList, poliName) in groupedByPoli" :key="poliName"
                                class="relative flex flex-col p-5 cursor-pointer rounded-2xl border-2 transition-all"
                                :class="selectedPoli === poliName ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'"
                            >
                                <input type="radio" v-model="selectedPoli" :value="poliName" class="sr-only" @change="form.jadwal_praktik_id = ''">
                                <span class="font-bold text-gray-900 text-lg">{{ poliName }}</span>
                                <span class="text-sm text-gray-500 mt-1">{{ jadwalList.length }} jadwal tersedia</span>
                                
                                <div v-if="selectedPoli === poliName" class="absolute top-4 right-4 text-indigo-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Langkah 2: Pilih Jadwal -->
                    <div v-if="selectedPoli" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <label class="block text-lg font-bold text-gray-800 mb-4">2. Pilih Jadwal Praktik</label>
                        <div class="space-y-4">
                            <label v-for="jadwal in availableJadwals" :key="jadwal.id"
                                class="relative flex flex-col sm:flex-row sm:items-center justify-between p-5 cursor-pointer rounded-2xl border-2 transition-all"
                                :class="form.jadwal_praktik_id === jadwal.id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'"
                            >
                                <input type="radio" v-model="form.jadwal_praktik_id" :value="jadwal.id" class="sr-only">
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold"
                                        :class="jadwal.sisa_kuota > 5 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                        Sisa Kuota: {{ jadwal.sisa_kuota }}
                                    </span>
                                </div>

                                <div v-if="form.jadwal_praktik_id === jadwal.id" class="absolute top-4 right-4 sm:top-1/2 sm:-translate-y-1/2 text-indigo-600 hidden sm:block">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </label>
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
