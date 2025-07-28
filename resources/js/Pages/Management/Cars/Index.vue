<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';

// Prop tiplerini, Controller'dan gelen paginator yapısına uygun olarak tanımlıyoruz.
interface CarData {
    id: number;
    user_id: number;
    full_name: string;
    brand: string;
    model: string;
    color: string;
    license_plate: string;
    year: number;
    type: string;
    // created_at ve updated_at modelde var ama props'ta belirtilmemiş,
    // eğer API'den geliyorsa CarData'ya eklenmelidir.
    // created_at: string; // YYYY-MM-DD HH:MM:SS formatında string
    // updated_at: string; // YYYY-MM-DD HH:MM:SS formatında string
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    data: { // Bu, Laravel'in paginator çıktısının tamamını içeren ana 'data' prop'u
        current_page: number;
        data: CarData[]; // Asıl araç verileri burada
        first_page_url: string | null;
        from: number | null;
        last_page: number;
        last_page_url: string | null;
        links: PaginationLink[]; // Sayfa numarası linkleri (Prev, 1, 2, Next)
        next_page_url: string | null;
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
    filters: {
        search: string | null;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('management.cars.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Tarih ve Saat formatlama fonksiyonu (GG.AA.YYYY SS:DD) - Eğer created_at/updated_at kullanılacaksa
// const formatDateTime = (dateTimeString: string | null) => {
//     if (!dateTimeString) return '-';
//     const date = new Date(dateTimeString);
//     if (isNaN(date.getTime())) return dateTimeString;
//     return date.toLocaleDateString('tr-TR', {
//         year: 'numeric',
//         month: 'numeric',
//         day: 'numeric',
//         hour: '2-digit',
//         minute: '2-digit'
//     });
// };
</script>

<template>
    <Head title="Araçlar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Araç Yönetimi</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Araç Listesi</h3>
                            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                                <TextInput
                                    type="text"
                                    v-model="search"
                                    placeholder="Araçlarda ara..."
                                    class="w-full md:w-auto"
                                />
                                <Link :href="route('management.cars.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                    Yeni Araç Ekle
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sahip</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marka</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plaka</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="props.data.data.length === 0">
                                    <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Hiç araç bulunamadı.
                                    </td>
                                </tr>
                                <tr v-for="car in props.data.data" :key="car.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ car.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ car.full_name || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ car.brand }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ car.model }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ car.license_plate }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ car.type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('management.cars.show', car.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2"
                                              as="button">
                                            Gör
                                        </Link>
                                        <Link :href="route('management.cars.edit', car.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                                              as="button">
                                            Düzenle
                                        </Link>
                                        <Link method="delete" :href="route('management.cars.destroy', car.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                              as="button">
                                            Sil
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Sayfalama Navigasyonu (Users/Index.vue ve Leaves/Index.vue ile aynı yapı) -->
                        <div v-if="props.data.links.length > 3" class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Button -->
                                <Link
                                    :href="props.data.prev_page_url || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.data.prev_page_url ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.data.prev_page_url" >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.56 10l3.23 3.71a.75.75 0 11-1.06 1.06l-3.75-4.25a.75.75 0 010-1.06l3.75-4.25a.75.75 0 011.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>

                                <!-- Page Number Links -->
                                <Link
                                    v-for="(link, key) in props.data.links.slice(1, -1)" :key="key"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!link.url"
                                />

                                <!-- Next Button -->
                                <Link
                                    :href="props.data.next_page_url || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.data.next_page_url ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.data.next_page_url" >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.44 10 7.21 6.29a.75.75 0 111.06-1.06l3.75 4.25a.75.75 0 010 1.06l-3.75 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            {{ props.data.from || 0 }}-{{ props.data.to || 0 }} arası gösteriliyor, toplam {{ props.data.total }} araç.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
