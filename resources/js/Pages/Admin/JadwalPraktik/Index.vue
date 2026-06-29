<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    jadwals: Object,
    dokters: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.jadwal-praktik.index'),
        { search: value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
});

// Modal State
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const modalMode = ref('add');

const form = useForm({
    id: null,
    dokter_id: '',
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
    status: true,
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (jadwal) => {
    form.reset();
    form.clearErrors();
    form.id = jadwal.id;
    form.dokter_id = jadwal.dokter_id;
    form.hari = jadwal.hari;
    
    // Format H:i
    const jmMulai = jadwal.jam_mulai.substring(0, 5);
    const jmSelesai = jadwal.jam_selesai.substring(0, 5);
    
    form.jam_mulai = jmMulai;
    form.jam_selesai = jmSelesai;
    form.status = jadwal.status;
    
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (jadwal) => {
    form.id = jadwal.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.jadwal-praktik.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.jadwal-praktik.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteJadwal = () => {
    form.delete(route('admin.jadwal-praktik.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Manajemen Jadwal Praktik" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Jadwal Praktik</h2>
            <p class="text-gray-600 mt-1">Kelola jam kerja operasional setiap dokter per poliklinik.</p>
        </div>

        <DataTable
            :columns="[
                { key: 'dokter', label: 'Nama Dokter (Poli)' },
                { key: 'hari', label: 'Hari Praktik' },
                { key: 'jam', label: 'Jam Praktik' },
                { key: 'status', label: 'Status Jadwal' },
            ]"
            :data="jadwals"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Tambah Jadwal
                </PrimaryButton>
            </template>

            <template #col-dokter="{ row }">
                <div class="font-medium text-gray-900">{{ row.dokter.user.name }}</div>
                <div class="text-xs text-gray-500">{{ row.dokter.poli.nama_poli }}</div>
            </template>

            <template #col-hari="{ row }">
                <span class="font-semibold text-gray-700">{{ row.hari }}</span>
            </template>

            <template #col-jam="{ row }">
                <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ row.jam_mulai.substring(0, 5) }} - {{ row.jam_selesai.substring(0, 5) }}
                </div>
            </template>

            <template #col-status="{ row }">
                <span v-if="row.status" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Aktif
                </span>
                <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Libur / Tidak Aktif
                </span>
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                    {{ modalMode === 'add' ? 'Tambah Jadwal Praktik Baru' : 'Edit Jadwal Praktik' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="dokter_id" value="Pilih Dokter" />
                        <select id="dokter_id" v-model="form.dokter_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="" disabled>Pilih Dokter...</option>
                            <option v-for="dokter in dokters" :key="dokter.id" :value="dokter.id">
                                {{ dokter.user.name }} (Poli: {{ dokter.poli.nama_poli }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.dokter_id" />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="hari" value="Hari Praktik" />
                            <select id="hari" v-model="form.hari" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.hari" />
                        </div>
                        <div>
                            <InputLabel for="jam_mulai" value="Jam Mulai" />
                            <TextInput id="jam_mulai" type="time" class="mt-1 block w-full" v-model="form.jam_mulai" required />
                            <InputError class="mt-2" :message="form.errors.jam_mulai" />
                        </div>
                        <div>
                            <InputLabel for="jam_selesai" value="Jam Selesai" />
                            <TextInput id="jam_selesai" type="time" class="mt-1 block w-full" v-model="form.jam_selesai" required />
                            <InputError class="mt-2" :message="form.errors.jam_selesai" />
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="status" v-model="form.status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="status" class="ml-2 block text-sm text-gray-900">
                            Jadwal Aktif (Bisa dipilih pasien)
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.status" />

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                        <SecondaryButton @click="closeModal" :disabled="form.processing">
                            Batal
                        </SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal Hapus -->
        <Modal :show="isDeleteModalOpen" @close="closeModal" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Hapus Jadwal
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus jadwal ini? Pasien yang sudah terlanjur mendaftar di jadwal ini perlu dihubungi secara manual.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deleteJadwal" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
