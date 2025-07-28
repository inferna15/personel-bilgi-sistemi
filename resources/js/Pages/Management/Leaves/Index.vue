<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';

// Prop tiplerini tanımla
interface LeaveData {
    id: number;
    user: string;
    type: string; // Enum değeri (string olarak gelir)
    start_date: string; // Y-m-d formatında string
    end_date: string; // Y-m-d formatında string
    days_count: number;
    reason: string | null;
    status: string; // Enum değeri (string olarak gelir)
    reviewed_by: number | null; // LeaveUserResource'dan geliyorsa
    reviewed_at: string | null; // Y-m-d H:i:s formatında string
    created_at: string;
    updated_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    leaves: { // LeaveIndexResource::collection($leaves) yapısı
        data: LeaveData[];
        links: { // root links (first, last, prev, next)
            first: string | null;
            last: string | null;
            prev: string | null;
            next: string | null;
        };
        meta: { // pagination meta information
            current_page: number;
            from: number | null;
            last_page: number;
            links: PaginationLink[]; // Sayfa numarası linkleri
            path: string;
            per_page: number;
            to: number | null;
            total: number;
        };
    };
    filters: {
        search: string | null;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('management.leaves.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Tarih formatlama fonksiyonu (GG.AA.YYYY)
const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; // Geçersiz tarihse orijinali döndür
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

// Tarih ve Saat formatlama fonksiyonu (GG.AA.YYYY SS:DD)
const formatDateTime = (dateTimeString: string) => {
    if (!dateTimeString) return '-';
    const date = new Date(dateTimeString);
    if (isNaN(date.getTime())) return dateTimeString;
    return date.toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Durum etiketi için renk sınıfı (LeaveStatus enum'daki color() metoduna göre)
const getStatusColorClass = (status: string) => {
    switch (status) {
        case 'Beklemede': return 'bg-yellow-100 text-yellow-800';
        case 'Onaylandı': return 'bg-green-100 text-green-800';
        case 'Reddedildi': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="İzin Talepleri" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">İzin Yönetimi</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">İzin Talepleri Listesi</h3>
                            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                                <TextInput
                                    type="text"
                                    v-model="search"
                                    placeholder="İzinlerde ara..."
                                    class="w-full md:w-auto"
                                />
                                <Link :href="route('management.leaves.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                    Yeni İzin Ekle
                                </Link>
                                <Link :href="route('management.leaves.review')" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                    İzinleri İncele
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İzin Tipi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlangıç</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gün Sayısı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="props.leaves.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Hiç izin talebi bulunamadı.
                                    </td>
                                </tr>
                                <tr v-for="leave in props.leaves.data" :key="leave.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ leave.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ leave.user }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ leave.type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(leave.start_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(leave.end_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ leave.days_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColorClass(leave.status)]">
                                            {{ leave.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('management.leaves.show', leave.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2"
                                              as="button">
                                            Gör
                                        </Link>
                                        <Link :href="route('management.leaves.edit', leave.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                                              as="button">
                                            Düzenle
                                        </Link>
                                        <Link method="delete" :href="route('management.leaves.destroy', leave.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                              as="button">
                                            Sil
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Sayfalama -->
                        <div v-if="props.leaves.meta.links.length > 3" class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    :href="props.leaves.links.prev || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.leaves.links.prev ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.leaves.links.prev" >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.56 10l3.23 3.71a.75.75 0 11-1.06 1.06l-3.75-4.25a.75.75 0 010-1.06l3.75-4.25a.75.75 0 011.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>

                                <Link
                                    v-for="(link, key) in props.leaves.meta.links.slice(1, -1)" :key="key"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!link.url"
                                />

                                <Link
                                    :href="props.leaves.links.next || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.leaves.links.next ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.leaves.links.next" >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.44 10 7.21 6.29a.75.75 0 111.06-1.06l3.75 4.25a.75.75 0 010 1.06l-3.75 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            {{ props.leaves.meta.from || 0 }}-{{ props.leaves.meta.to || 0 }} arası gösteriliyor, toplam {{ props.leaves.meta.total }} izin talebi.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
