<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const userRole = page.props.auth.user.role;
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex md:flex-col shadow-sm">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Klinik Polban</h1>
            </div>
            
            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                <!-- Menu Admin & Super Admin -->
                <template v-if="['super_admin', 'admin'].includes(userRole)">
                    <Link :href="route('admin.dashboard')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.dashboard') }">
                        Dashboard Utama
                    </Link>
                    
                    <div class="pt-4 pb-1">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</p>
                    </div>
                    
                    <Link :href="route('admin.poli.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.poli.*') }">
                        Data Poli
                    </Link>
                    <Link :href="route('admin.dokter.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.dokter.*') }">
                        Data Dokter
                    </Link>
                    <Link :href="route('admin.tindakan.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.tindakan.*') }">
                        Data Tindakan & Tarif
                    </Link>
                    <Link :href="route('admin.obat.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.obat.*') }">
                        Data Obat
                    </Link>
                    <Link :href="route('admin.template-rujukan.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.template-rujukan.*') }">
                        Template Rujukan
                    </Link>
                    <Link :href="route('admin.jadwal-praktik.index')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" :class="{ 'bg-blue-50 text-blue-600': route().current('admin.jadwal-praktik.*') }">
                        Jadwal Praktik
                    </Link>

                    <div class="pt-4 pb-1">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operasional</p>
                    </div>
                    <Link href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        Kelola Antrean
                    </Link>
                    <Link href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        Apotek & Obat
                    </Link>
                </template>

                <!-- Menu Dokter -->
                <template v-if="userRole === 'dokter'">
                    <Link :href="route('admin.dashboard')" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                        Ruang Periksa (Antrean)
                    </Link>
                    <Link href="#" class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                        Riwayat Rekam Medis
                    </Link>
                </template>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10 shadow-sm">
                <div>
                    <!-- Hamburger for mobile -->
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.name }} ({{ userRole }})</span>
                    <Link :href="route('logout')" method="post" as="button" class="text-sm text-red-600 font-medium hover:text-red-800 transition">
                        Logout
                    </Link>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
