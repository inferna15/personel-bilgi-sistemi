<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash'; // npm install lodash kurmayı unutmayın!
import TextInput from '@/Components/TextInput.vue';

// --- TypeScript Arayüzleri (Interfaces) ---
// Veri yapılarının netliğini artırmak için
interface User {
    id: number;
    first_name: string;
    last_name: string;
}

interface Salary {
    id: number;
    user_id: number;
    user: User; // User ilişkisi
    pay_date: string; // "YYYY-MM-DD" formatında gelecektir
    net_salary: string; // Decimal olarak gelir ama string olabilir
    gross_salary: string | null;
    payroll_file_path: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    salaries: {
        current_page: number;
        data: Salary[];
        first_page_url: string | null;
        from: number | null;
        last_page: number;
        last_page_url: string | null;
        links: PaginationLink[]; // Paginator'ın kendi oluşturduğu link dizisi (Prev, 1, 2, Next)
        next_page_url: string | null;
        path: string;
        per_page: number;
        prev_page_url: string | null;
        to: number | null;
        total: number;
    };
    filters: { // Backend'den gelen filtreleri almak için
        search: string | null;
    };
}

const props = defineProps<Props>();

// --- Reactive State ---
const search = ref(props.filters.search || '');

// Maaş verisi olup olmadığını kontrol eden değişken
const hasSalaries = ref(props.salaries && props.salaries.data && props.salaries.data.length > 0);
// Toplam kayıt sayısını gösteren değişken, boşsa 0
const totalSalaries = ref(props.salaries?.total || 0);

// --- Watchers ---
// Arama input'ı değiştiğinde tetiklenecek ve 300ms gecikmeli çalışacak fonksiyon
watch(search, debounce(() => {
    router.get(route('management.salaries.index'), { search: search.value }, {
        preserveState: true, // Sayfa durumunu (scroll pozisyonu gibi) koru
        replace: true,       // Tarayıcı geçmişini kirletme (yeni bir giriş ekleme yerine URL'yi değiştir)
    });
}, 300)); // 300 milisaniye gecikme

// --- Yardımcı Fonksiyonlar ---
const formatCurrency = (value: string | number | null) => {
    if (value === null) return '-';
    // Türkiye para birimi (TL) formatında göster
    return parseFloat(value as string).toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
};

const formatDate = (dateString: string) => {
    // Türkiye tarih formatında göster
    return new Date(dateString).toLocaleDateString('tr-TR', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getFileDisplayUrl = (filePath: string | null) => {
    if (filePath) {
        return `/storage/${filePath}`;
    }
    return null;
};
</script>

<template>
    <Head title="Maaşlar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Maaş Yönetimi</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Maaş Kayıtları</h3>
                            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                                <TextInput
                                    type="text"
                                    v-model="search"
                                    placeholder="Maaşlarda ara..."
                                    class="w-full md:w-auto"
                                />
                                <Link :href="route('management.salaries.create')"
                                      class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                    Yeni Maaş Kaydı Ekle
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ödeme Tarihi</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Maaş</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Brüt Maaş</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bordro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notlar</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!hasSalaries">
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Hiç maaş kaydı bulunamadı.
                                    </td>
                                </tr>
                                <tr v-for="salary in props.salaries.data" :key="salary.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ salary.user.first_name }} {{ salary.user.last_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(salary.pay_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatCurrency(salary.net_salary) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatCurrency(salary.gross_salary) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a v-if="salary.payroll_file_path" :href="getFileDisplayUrl(salary.payroll_file_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l3-3m-3 3l-3-3m-3 8h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Bordroyu Gör
                                        </a>
                                        <span v-else>-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ salary.notes || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('management.salaries.edit', salary.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                                              as="button">
                                            Düzenle
                                        </Link>
                                        <Link method="delete" :href="route('management.salaries.destroy', salary.id)"
                                              class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                              as="button"> Sil
                                        </Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="props.salaries?.links?.length > 3" class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <Link
                                    :href="props.salaries?.prev_page_url || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.salaries?.prev_page_url ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.salaries?.prev_page_url" >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.56 10l3.23 3.71a.75.75 0 11-1.06 1.06l-3.75-4.25a.75.75 0 010-1.06l3.75-4.25a.75.75 0 011.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>

                                <Link
                                    v-for="(link, key) in props.salaries?.links?.slice(1, -1)" :key="key"
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
                                    :href="props.salaries?.next_page_url || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.salaries?.next_page_url ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.salaries?.next_page_url" >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.44 10 7.21 6.29a.75.75 0 111.06-1.06l3.75 4.25a.75.75 0 010 1.06l-3.75 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            {{ props.salaries?.from || 0 }}-{{ props.salaries?.to || 0 }} arası gösteriliyor, toplam {{ totalSalaries }} maaş kaydı.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
