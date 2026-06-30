<script setup>
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    columns: {
        type: Array,
        required: true,
        // Example: [{ key: 'nama_poli', label: 'Nama Poli' }, { key: 'deskripsi', label: 'Deskripsi' }]
    },
    data: {
        type: Object, // Laravel paginator object
        required: true,
    },
    search: {
        type: String,
        default: '',
    },
    actionTemplate: {
        type: Boolean,
        default: true,
    }
});

</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Toolbar -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div class="w-1/3">
                <TextInput 
                    :model-value="search" 
                    @update:model-value="$emit('update:search', $event)" 
                    type="search" 
                    placeholder="Cari data..." 
                    class="w-full text-sm" 
                />
            </div>
            <div>
                <slot name="toolbar-right" />
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                            No
                        </th>
                        <th v-for="col in columns" :key="col.key" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ col.label }}
                        </th>
                        <th v-if="actionTemplate" scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="data.data.length === 0">
                        <td :colspan="columns.length + (actionTemplate ? 2 : 1)" class="px-6 py-8 text-center text-gray-500 text-sm">
                            Tidak ada data yang ditemukan.
                        </td>
                    </tr>
                    <tr v-for="(row, index) in data.data" :key="row.id" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ data.from + index }}
                        </td>
                        <td v-for="col in columns" :key="col.key" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <!-- Support for custom slots for specific columns -->
                            <slot :name="`col-${col.key}`" :row="row">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                        <td v-if="actionTemplate" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <slot name="actions" :row="row">
                                <button @click="$emit('edit', row)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button @click="$emit('delete', row)" class="text-red-600 hover:text-red-900">Hapus</button>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-medium text-gray-700">{{ data.from || 0 }}</span> sampai <span class="font-medium text-gray-700">{{ data.to || 0 }}</span> dari <span class="font-medium text-gray-700">{{ data.total }}</span> hasil
            </div>
            <Pagination :links="data.links" />
        </div>
    </div>
</template>
