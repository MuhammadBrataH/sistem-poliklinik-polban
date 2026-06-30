<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    jadwalHariIni: {
        type: Array,
        default: () => [],
    }
});
</script>

<template>
    <Head title="Selamat Datang - Poliklinik Polban" />

    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Navbar -->
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 text-white flex items-center justify-center rounded-lg font-bold text-xl">
                        +
                    </div>
                    <span class="font-bold text-xl text-gray-900 tracking-tight">Poliklinik<span class="text-indigo-600">Polban</span></span>
                </div>
                
                <nav v-if="canLogin" class="flex gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('pasien.dashboard')"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm"
                        >
                            Register Pasien
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
                        Layanan Kesehatan Cepat, <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Tanpa Antrean Panjang.</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Sistem antrean pintar Poliklinik Polban. Daftar secara online dari mana saja, pantau antrean secara real-time, dan datang tepat waktu.
                    </p>
                    <div class="flex justify-center gap-4">
                        <Link :href="route('pasien.antrean.create')" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                            Ambil Antrean Sekarang
                        </Link>
                    </div>
                </div>

                <!-- Jadwal Hari Ini Section -->
                <div class="mt-12 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Jadwal Praktik Hari Ini</h2>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Live
                        </span>
                    </div>

                    <div v-if="jadwalHariIni.length === 0" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-gray-500">Tidak ada jadwal praktik dokter yang tersedia untuk hari ini.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="jadwal in jadwalHariIni" :key="jadwal.id" class="bg-gray-50 border border-gray-100 p-5 rounded-xl hover:border-indigo-200 hover:shadow-md transition cursor-default">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ jadwal.dokter.user.name }}</h3>
                                    <p class="text-sm text-indigo-600 font-medium">{{ jadwal.dokter.poli.nama_poli }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {{ jadwal.dokter.user.name.charAt(0) }}
                                </div>
                            </div>
                            
                            <div class="space-y-2 mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ jadwal.jam_mulai.substring(0,5) }} - {{ jadwal.jam_selesai.substring(0,5) }} WIB
                                </div>
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-gray-500">Sisa Kuota:</span>
                                    <span class="font-bold" :class="jadwal.sisa_kuota > 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ jadwal.sisa_kuota > 0 ? jadwal.sisa_kuota + ' Pasien' : 'Habis' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto py-8">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                &copy; {{ new Date().getFullYear() }} Poliklinik Politeknik Negeri Bandung. All rights reserved.
            </div>
        </footer>
    </div>
</template>
