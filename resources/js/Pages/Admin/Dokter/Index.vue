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
    dokters: Object,
    polis: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        route('admin.dokter.index'),
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
    username: '',
    name: '',
    email: '',
    password: '',
    poli_id: '',
    spesialisasi: '',
});

const openAddModal = () => {
    form.reset();
    form.clearErrors();
    modalMode.value = 'add';
    isModalOpen.value = true;
};

const openEditModal = (dokter) => {
    form.reset();
    form.clearErrors();
    form.id = dokter.id;
    form.username = dokter.user.username;
    form.name = dokter.user.name;
    form.email = dokter.user.email || '';
    form.password = ''; // kosongkan password saat edit, diisi hanya jika ingin diubah
    form.poli_id = dokter.poli_id;
    form.spesialisasi = dokter.spesialisasi;
    modalMode.value = 'edit';
    isModalOpen.value = true;
};

const openDeleteModal = (dokter) => {
    form.id = dokter.id;
    isDeleteModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isDeleteModalOpen.value = false;
    setTimeout(() => form.reset(), 300);
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('admin.dokter.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('admin.dokter.update', form.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteDokter = () => {
    form.delete(route('admin.dokter.destroy', form.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Manajemen Data Dokter" />

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Dokter</h2>
            <p class="text-gray-600 mt-1">Kelola data dokter dan informasi akun login mereka.</p>
        </div>

        <DataTable
            :columns="[
                { key: 'nama_dokter', label: 'Nama Dokter' },
                { key: 'username', label: 'NIP / Username' },
                { key: 'poli', label: 'Poli' },
                { key: 'spesialisasi', label: 'Spesialisasi' },
            ]"
            :data="dokters"
            v-model:search="search"
            @edit="openEditModal"
            @delete="openDeleteModal"
        >
            <template #toolbar-right>
                <PrimaryButton @click="openAddModal">
                    + Tambah Dokter
                </PrimaryButton>
            </template>

            <!-- Custom Slots for nested data -->
            <template #col-nama_dokter="{ row }">
                <div class="font-medium text-gray-900">{{ row.user.name }}</div>
                <div class="text-xs text-gray-500">{{ row.user.email || '-' }}</div>
            </template>
            <template #col-username="{ row }">
                {{ row.user.username }}
            </template>
            <template #col-poli="{ row }">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ row.poli.nama_poli }}
                </span>
            </template>
            <template #col-spesialisasi="{ row }">
                {{ row.spesialisasi }}
            </template>
        </DataTable>

        <!-- Modal Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                    {{ modalMode === 'add' ? 'Tambah Dokter Baru' : 'Edit Data Dokter' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    
                    <div>
                        <InputLabel for="name" value="Nama Lengkap (dengan Gelar)" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="username" value="NIP / Username Login" />
                            <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required />
                            <InputError class="mt-2" :message="form.errors.username" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email (Opsional)" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="password" :value="modalMode === 'add' ? 'Password' : 'Password (Kosongkan jika tidak ingin diubah)'" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" :required="modalMode === 'add'" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t mt-4">
                        <div>
                            <InputLabel for="poli_id" value="Poliklinik" />
                            <select id="poli_id" v-model="form.poli_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>Pilih Poli...</option>
                                <option v-for="poli in polis" :key="poli.id" :value="poli.id">
                                    {{ poli.nama_poli }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.poli_id" />
                        </div>
                        <div>
                            <InputLabel for="spesialisasi" value="Spesialisasi" />
                            <TextInput id="spesialisasi" type="text" class="mt-1 block w-full" v-model="form.spesialisasi" required placeholder="Contoh: Dokter Gigi Umum" />
                            <InputError class="mt-2" :message="form.errors.spesialisasi" />
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
                    Hapus Dokter
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus dokter ini? Akun login dan data jadwal terkait dokter ini akan ikut terhapus secara permanen.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Batal
                    </SecondaryButton>
                    <DangerButton @click="deleteDokter" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>
