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
    templates: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.template-rujukan.index'),
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
    nama_template: '',
    isi_template: '',
    is_active: true,
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (template) => {
    form.reset();
    form.clearErrors();
    form.id = template.id;
    form.nama_template = template.nama_template;
    form.isi_template = template.isi_template;
    form.is_active = template.is_active;
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (template) => {
    form.id = template.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.template-rujukan.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.template-rujukan.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteTemplate = () => {
    form.delete(route('admin.template-rujukan.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Template Surat Rujukan" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Template Surat Rujukan</h2>
            <p class="text-gray-600 mt-1">Kelola format teks untuk pencetakan surat rujukan pasien ke Rumah Sakit.</p>
        </div>

        <DataTable
            :columns="[
                { key: 'nama_template', label: 'Nama Template' },
                { key: 'isi_template', label: 'Format / Isi Template' },
                { key: 'is_active', label: 'Status' },
            ]"
            :data="templates"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Buat Template
                </PrimaryButton>
            </template>

            <template #col-isi_template="{ row }">
                <p class="text-gray-600 text-xs truncate max-w-md">{{ row.isi_template }}</p>
            </template>

            <template #col-is_active="{ row }">
                <span v-if="row.is_active" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Aktif
                </span>
                <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    Nonaktif
                </span>
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                    {{ modalMode === 'add' ? 'Buat Template Baru' : 'Edit Template Rujukan' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="nama_template" value="Nama Template" />
                        <TextInput id="nama_template" type="text" class="mt-1 block w-full" v-model="form.nama_template" required autofocus placeholder="Contoh: Rujukan BPJS Standar" />
                        <InputError class="mt-2" :message="form.errors.nama_template" />
                    </div>

                    <div>
                        <InputLabel for="isi_template" value="Isi Template" />
                        <p class="text-xs text-gray-500 mb-2">Gunakan placeholder dinamis seperti: {nama_pasien}, {diagnosa}, {rs_tujuan}</p>
                        <textarea 
                            id="isi_template" 
                            v-model="form.isi_template" 
                            rows="6" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" 
                            required
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.isi_template" />
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Aktifkan template ini agar bisa digunakan oleh dokter
                        </label>
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
                    Hapus Template
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus template rujukan ini?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deleteTemplate" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
