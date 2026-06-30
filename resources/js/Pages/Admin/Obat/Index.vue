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
    obats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.obat.index'),
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
    nama_obat: '',
    satuan: '',
    stok: 0,
    gambar: null,
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (obat) => {
    form.reset();
    form.clearErrors();
    form.id = obat.id;
    form.nama_obat = obat.nama_obat;
    form.satuan = obat.satuan;
    form.stok = obat.stok;
    form.gambar = null; // Reset form gambar
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (obat) => {
    form.id = obat.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.obat.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(route('admin.obat.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteObat = () => {
    form.delete(route('admin.obat.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Manajemen Data Obat" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Obat</h2>
            <p class="text-gray-600 mt-1">Kelola daftar obat dan pantau jumlah stok yang tersedia di apotek.</p>
        </div>

        <DataTable
            :columns="[
                { key: 'gambar', label: 'Gambar' },
                { key: 'nama_obat', label: 'Nama Obat' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'stok', label: 'Stok Tersedia' },
            ]"
            :data="obats"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Tambah Obat
                </PrimaryButton>
            </template>

            <template #col-gambar="{ row }">
                <img v-if="row.gambar" :src="`/storage/${row.gambar}`" class="w-12 h-12 object-cover rounded-md border" alt="Gambar Obat" />
                <div v-else class="w-12 h-12 bg-gray-100 rounded-md border flex items-center justify-center text-gray-400 text-xs">
                    No Img
                </div>
            </template>
            
            <template #col-stok="{ row }">
                <span :class="{'text-red-600 font-bold': row.stok <= 10, 'text-green-600 font-medium': row.stok > 10}">
                    {{ row.stok }}
                </span>
                <span v-if="row.stok === 0" class="ml-2 text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">Habis</span>
                <span v-if="row.stok > 0 && row.stok <= 5" class="ml-2 text-xs text-orange-500 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-100">Menipis</span>
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                    {{ modalMode === 'add' ? 'Tambah Obat Baru' : 'Edit Data Obat' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="nama_obat" value="Nama Obat" />
                        <TextInput id="nama_obat" type="text" class="mt-1 block w-full" v-model="form.nama_obat" required autofocus placeholder="Contoh: Paracetamol 500mg" />
                        <InputError class="mt-2" :message="form.errors.nama_obat" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="satuan" value="Satuan (Kemasan)" />
                            <TextInput id="satuan" type="text" class="mt-1 block w-full" v-model="form.satuan" required placeholder="Contoh: Strip, Botol, Tablet" />
                            <InputError class="mt-2" :message="form.errors.satuan" />
                        </div>
                        <div>
                            <InputLabel for="stok" value="Stok Awal" />
                            <TextInput id="stok" type="number" min="0" class="mt-1 block w-full" v-model="form.stok" required />
                            <InputError class="mt-2" :message="form.errors.stok" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="gambar" value="Gambar Obat (Opsional)" />
                        <input id="gambar" type="file" @change="e => form.gambar = e.target.files[0]" class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100
                        " accept="image/png, image/jpeg, image/jpg" />
                        <InputError class="mt-2" :message="form.errors.gambar" />
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG | Maks: 2MB</p>
                        <p v-if="modalMode === 'edit'" class="text-xs text-gray-500 mt-1">*Biarkan kosong jika tidak ingin mengubah gambar</p>
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
                    Hapus Obat
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus data obat ini?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deleteObat" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
