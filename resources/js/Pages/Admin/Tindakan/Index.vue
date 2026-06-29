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
    tindakans: Object,
    polis: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.tindakan.index'),
        { search: value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
});

// Format currency IDR
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Modal State
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const modalMode = ref('add');

const form = useForm({
    id: null,
    nama_tindakan: '',
    poli_id: '',
    tarif: 0,
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (tindakan) => {
    form.reset();
    form.clearErrors();
    form.id = tindakan.id;
    form.nama_tindakan = tindakan.nama_tindakan;
    form.poli_id = tindakan.poli_id;
    form.tarif = tindakan.tarif;
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (tindakan) => {
    form.id = tindakan.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.tindakan.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.tindakan.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteTindakan = () => {
    form.delete(route('admin.tindakan.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Manajemen Data Tindakan" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Tindakan & Tarif</h2>
            <p class="text-gray-600 mt-1">Kelola daftar tindakan medis dan konfigurasi harga per poli.</p>
        </div>

        <DataTable
            :columns="[
                { key: 'nama_tindakan', label: 'Nama Tindakan' },
                { key: 'poli', label: 'Kategori Poli' },
                { key: 'tarif', label: 'Tarif / Harga' },
            ]"
            :data="tindakans"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Tambah Tindakan
                </PrimaryButton>
            </template>

            <template #col-poli="{ row }">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    {{ row.poli.nama_poli }}
                </span>
            </template>
            <template #col-tarif="{ row }">
                {{ formatRupiah(row.tarif) }}
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                    {{ modalMode === 'add' ? 'Tambah Tindakan Baru' : 'Edit Data Tindakan' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="nama_tindakan" value="Nama Tindakan" />
                        <TextInput id="nama_tindakan" type="text" class="mt-1 block w-full" v-model="form.nama_tindakan" required autofocus placeholder="Contoh: Cabut Gigi Anak" />
                        <InputError class="mt-2" :message="form.errors.nama_tindakan" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="poli_id" value="Kategori Poli" />
                            <select id="poli_id" v-model="form.poli_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>Pilih Poli...</option>
                                <option v-for="poli in polis" :key="poli.id" :value="poli.id">
                                    {{ poli.nama_poli }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.poli_id" />
                        </div>
                        <div>
                            <InputLabel for="tarif" value="Tarif / Harga (Rp)" />
                            <TextInput id="tarif" type="number" min="0" step="1000" class="mt-1 block w-full" v-model="form.tarif" required />
                            <InputError class="mt-2" :message="form.errors.tarif" />
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
                    Hapus Tindakan
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus tindakan ini? Histori rekam medis pasien yang sudah menggunakan tindakan ini tidak akan terpengaruh.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deleteTindakan" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
