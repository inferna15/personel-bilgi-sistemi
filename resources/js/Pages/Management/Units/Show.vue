<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Birim prop'unun tipini tanımla
// Bu yapı, UnitShowResource'unuzun döndürdüğü veriyle eşleşmelidir.
interface UnitData {
    id: number;
    name: string;
    description: string | null;
    created_at: string; // Örn: "2025-07-17T12:37:07.000000Z" (resource'dan geliyorsa)
}

interface Props {
    unit: UnitData;
}

const props = defineProps<Props>();

// template içinde daha kısa erişim için bir sabit oluşturuyoruz
const unit = props.unit;

// Tarih formatlama fonksiyonu
const formatDateTime = (isoString: string) => {
    if (!isoString) return '-';
    const date = new Date(isoString);
    if (isNaN(date.getTime())) {
        return isoString;
    }
    return date.toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`${unit.name} Birim Detayları`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ unit.name }} Birim Detayları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4">
                                    {{ unit.name }}
                                </h3>

                                <div class="grid grid-cols-1 gap-x-8 gap-y-4 text-gray-700 sm:grid-cols-2">
                                    <div>
                                        <p class="font-semibold text-gray-500 text-sm">Birim Adı:</p>
                                        <p class="text-gray-900 mt-1">{{ unit.name }}</p>
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <p class="font-semibold text-gray-500 text-sm">Açıklama:</p>
                                        <p class="text-gray-900 mt-1">{{ unit.description || 'Açıklama belirtilmemiş.' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-500 text-sm">Oluşturulma Tarihi:</p>
                                        <p class="text-gray-900 mt-1">{{ formatDateTime(unit.created_at) }}</p>
                                    </div>
                                </div>

                                <div class="mt-8 flex space-x-4">
                                    <Link :href="route('management.units.edit', unit.id)"
                                          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Düzenle
                                    </Link>
                                    <Link :href="route('management.units.index')"
                                          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Geri Dön
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
