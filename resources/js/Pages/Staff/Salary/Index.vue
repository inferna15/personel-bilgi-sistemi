<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

// --- TypeScript Arayüzleri (Interfaces) ---
interface Salary {
    id: number;
    pay_date: string;
    net_salary: string;
    gross_salary: string | null;
    payroll_file_path: string | null;
    notes: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    user: { // UserLayout için gerekli
        first_name: string;
        last_name: string;
        email: string;
        identity_number: string;
        birth_date: string;
        gender: string;
        phone: string;
        address: string;
        position: string;
        photo: string;
    };
    unit: string; // UserLayout için gerekli
    salaries: { // Laravel'in Paginator objesi
        data: Salary[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        // Diğer paginator meta verileri buraya eklenebilir (first_page_url, path, per_page vb.)
    };
}

const props = defineProps<Props>();

// --- Yardımcı Fonksiyonlar ---
const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

const formatCurrency = (value: string | number | null) => {
    if (value === null || value === undefined) return '-';
    return parseFloat(value as string).toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
};

const getFileDisplayUrl = (filePath: string | null) => {
    if (filePath) {
        return `/storage/${filePath}`;
    }
    return null;
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="space-y-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></div>
                    <h2 class="text-3xl font-bold text-gray-800">Tüm Maaş Bilgilerim</h2>
                </div>
                <Link class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-300 transition-colors duration-200 shadow-sm"
                      :href="route('home')">
                    Geri dön
                </Link>
            </div>

            <div v-if="props.salaries.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="salary in props.salaries.data" :key="salary.id"
                     class="bg-white/70 backdrop-blur-sm shadow-lg p-6 rounded-2xl border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-gray-600">Ödeme Tarihi:</span>
                        <span class="font-bold text-lg text-gray-900">{{ formatDate(salary.pay_date) }}</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between items-center">
                            <span>Net Maaş:</span>
                            <span class="font-semibold text-emerald-600 text-base">{{ formatCurrency(salary.net_salary) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Brüt Maaş:</span>
                            <span class="font-semibold text-gray-600 text-base">{{ formatCurrency(salary.gross_salary) }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a v-if="salary.payroll_file_path" :href="getFileDisplayUrl(salary.payroll_file_path)" target="_blank"
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-3 15h2v-4h-2v4zm3-7V3.5L18.5 9H14z"/></svg>
                            Bordroyu Görüntüle
                        </a>
                        <span v-else class="text-sm text-gray-500 block text-center mt-2">Bordro mevcut değil</span>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz maaş kaydınız bulunmamaktadır.</p>
            </div>

            <div class="mt-8 flex justify-center">
                <nav class="flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <Link v-for="(link, index) in props.salaries.links" :key="index"
                          :href="link.url || '#'"
                          :disabled="!link.url"
                          :class="{
                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                                  'bg-purple-600 text-white border-purple-600': link.active,
                                  'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active && link.url,
                                  'rounded-l-md': index === 0,
                                  'rounded-r-md': index === props.salaries.links.length - 1,
                                  'text-gray-400 cursor-not-allowed': !link.url
                              }"
                          v-html="link.label">
                    </Link>
                </nav>
            </div>
        </div>
    </UserLayout>
</template>
