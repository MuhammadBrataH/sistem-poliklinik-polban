<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
        <!-- Top Navbar untuk Pasien (Mobile Friendly) -->
        <header class="bg-white/70 backdrop-blur-lg border-b border-white/50 sticky top-0 z-50 shadow-sm">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <h1 class="text-xl font-extrabold bg-gradient-to-r from-indigo-600 to-blue-500 bg-clip-text text-transparent">
                    Poliklinik Polban
                </h1>
                
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-700 hidden sm:block">Halo, {{ $page.props.auth.user.name }}</span>
                    <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 bg-white/80 border border-gray-200 rounded-full text-sm font-medium text-red-600 shadow-sm hover:bg-red-50 hover:border-red-100 transition-all">
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
