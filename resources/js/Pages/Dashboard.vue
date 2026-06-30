<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PasienLayout from '@/Layouts/PasienLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const role = page.props.auth.user.role;
</script>

<template>
    <Head title="Dashboard" />

    <component :is="role === 'pasien' ? PasienLayout : DashboardLayout">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                You're logged in as {{ role }}!
            </div>
        </div>
    </component>
</template>
