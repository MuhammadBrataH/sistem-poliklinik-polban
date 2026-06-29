<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPassword = ref(false);

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    nik: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    kategori: 'umum',
    no_telp: '',
    alamat: '',
    prodi_unit: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

</script>

<template>
    <GuestLayout>
        <Head title="Registrasi Pasien" />

        <div class="mb-4 text-center">
            <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Pasien Baru</h2>
            <p class="text-sm text-gray-600 mt-1">Silakan lengkapi data profil medis Anda</p>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Username & Password -->
            <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                <h3 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Informasi Akun</h3>
                
                <div class="mt-4">
                    <InputLabel for="username" value="Username (NIM / NIP / NIK)" />
                    <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required autofocus />
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email (Opsional, untuk fitur Lupa Sandi)" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <div class="relative">
                        <TextInput id="password" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full pr-10" v-model="form.password" required />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700">
                            {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                    <div class="relative">
                        <TextInput id="password_confirmation" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full pr-10" v-model="form.password_confirmation" required />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700">
                            {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
            </div>

            <!-- Data Pribadi -->
            <div class="col-span-1 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100 mt-2">
                <h3 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">Data Pribadi</h3>

                <div class="mt-4">
                    <InputLabel for="name" value="Nama Lengkap" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="nik" value="NIK (Nomor Induk Kependudukan)" />
                    <TextInput id="nik" type="text" class="mt-1 block w-full" v-model="form.nik" required />
                    <InputError class="mt-2" :message="form.errors.nik" />
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <InputLabel for="tempat_lahir" value="Tempat Lahir" />
                        <TextInput id="tempat_lahir" type="text" class="mt-1 block w-full" v-model="form.tempat_lahir" required />
                        <InputError class="mt-2" :message="form.errors.tempat_lahir" />
                    </div>
                    <div>
                        <InputLabel for="tanggal_lahir" value="Tanggal Lahir" />
                        <TextInput id="tanggal_lahir" type="date" class="mt-1 block w-full" v-model="form.tanggal_lahir" required />
                        <InputError class="mt-2" :message="form.errors.tanggal_lahir" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                        <select id="jenis_kelamin" v-model="form.jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.jenis_kelamin" />
                    </div>
                    <div>
                        <InputLabel for="kategori" value="Kategori Pasien" />
                        <select id="kategori" v-model="form.kategori" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="keluarga_pegawai">Keluarga Pegawai</option>
                            <option value="umum">Umum</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.kategori" />
                    </div>
                </div>

                <div class="mt-4" v-if="form.kategori === 'mahasiswa' || form.kategori === 'pegawai'">
                    <InputLabel for="prodi_unit" value="Prodi / Unit Kerja" />
                    <TextInput id="prodi_unit" type="text" class="mt-1 block w-full" v-model="form.prodi_unit" />
                    <InputError class="mt-2" :message="form.errors.prodi_unit" />
                </div>

                <div class="mt-4">
                    <InputLabel for="no_telp" value="Nomor Telepon / WA" />
                    <TextInput id="no_telp" type="text" class="mt-1 block w-full" v-model="form.no_telp" required />
                    <InputError class="mt-2" :message="form.errors.no_telp" />
                </div>

                <div class="mt-4">
                    <InputLabel for="alamat" value="Alamat Lengkap" />
                    <textarea id="alamat" v-model="form.alamat" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <InputError class="mt-2" :message="form.errors.alamat" />
                </div>
            </div>

            <div class="col-span-1 md:col-span-2 mt-6 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Sudah memiliki akun?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar Sekarang
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
