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

// Helper for formatting date
const formatTanggal = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

// Modal State
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const modalMode = ref('add');

const form = useForm({
    id: null,
    dokter_id: '',
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
    kuota: 10,
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
    form.tanggal = jadwal.tanggal;
    
    // Format H:i
    const jmMulai = jadwal.jam_mulai.substring(0, 5);
    const jmSelesai = jadwal.jam_selesai.substring(0, 5);
    
    form.jam_mulai = jmMulai;
    form.jam_selesai = jmSelesai;
    form.kuota = jadwal.kuota;
    
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
                { key: 'tanggal', label: 'Tanggal Praktik' },
                { key: 'jam', label: 'Jam Praktik' },
                { key: 'kuota', label: 'Sisa Kuota / Total' },
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

            <template #col-tanggal="{ row }">
                <span class="font-semibold text-gray-700">{{ formatTanggal(row.tanggal) }}</span>
            </template>

            <template #col-jam="{ row }">
                <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ row.jam_mulai.substring(0, 5) }} - {{ row.jam_selesai.substring(0, 5) }}
                </div>
            </template>

            <template #col-kuota="{ row }">
                <span class="font-medium" :class="{'text-red-600': row.sisa_kuota === 0, 'text-green-600': row.sisa_kuota > 0}">
                    {{ row.sisa_kuota }}
                </span>
                <span class="text-gray-500 text-xs">/ {{ row.kuota }} pasien</span>
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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="tanggal" value="Tanggal Praktik" />
                            <TextInput id="tanggal" type="date" class="mt-1 block w-full" v-model="form.tanggal" required />
                            <InputError class="mt-2" :message="form.errors.tanggal" />
                        </div>
                        <div>
                            <InputLabel for="kuota" value="Kuota Pasien" />
                            <TextInput id="kuota" type="number" min="1" class="mt-1 block w-full" v-model="form.kuota" required />
                            <InputError class="mt-2" :message="form.errors.kuota" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
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
