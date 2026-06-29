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
    polis: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.poli.index'),
        { search: value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
});

// Modal State
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const modalMode = ref('add'); // 'add' or 'edit'

const form = useForm({
    id: null,
    nama_poli: '',
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (poli) => {
    form.reset();
    form.clearErrors();
    form.id = poli.id;
    form.nama_poli = poli.nama_poli;
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (poli) => {
    form.id = poli.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.poli.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.poli.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deletePoli = () => {
    form.delete(route('admin.poli.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Manajemen Data Poli" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Poli</h2>
            <p class="text-gray-600 mt-1">Kelola poliklinik yang tersedia di klinik.</p>
        </div>

        <DataTable
            :columns="[{ key: 'nama_poli', label: 'Nama Poli' }]"
            :data="polis"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Tambah Poli
                </PrimaryButton>
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ modalMode === 'add' ? 'Tambah Poli Baru' : 'Edit Data Poli' }}
                </h2>

                <form @submit.prevent="submitForm">
                    <div class="mt-4">
                        <InputLabel for="nama_poli" value="Nama Poli" />
                        <TextInput
                            id="nama_poli"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.nama_poli"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.nama_poli" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
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
                    Hapus Poli
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus poli ini? Data dokter dan tindakan yang terkait dengan poli ini mungkin akan ikut terhapus.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deletePoli" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
