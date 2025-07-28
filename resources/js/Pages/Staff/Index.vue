<script setup lang="ts">
import { defineProps } from 'vue';
import { Link } from "@inertiajs/vue3";
import UserLayout from '@/Layouts/UserLayout.vue';

// --- TypeScript Arayüzleri (Interfaces) ---
// Maaş için yeni bir interface ekleyin
interface Salary {
    id: number;
    pay_date: string;
    net_salary: string;
    gross_salary: string | null;
    payroll_file_path: string | null;
    notes: string | null;
}

const props = defineProps<{
    user: {
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
    unit: string;
    leaves: Array<{
        id: number;
        type: string;
        start_date: string;
        end_date: string;
        days_count: number;
        status: string;
    }>;
    cards: Array<{
        id: number;
        serial_no: string;
        balance: number;
        issue_date: string;
        type: string;
    }>;
    cars: Array<{
        id: number;
        brand: string;
        model: string;
        license_plate: string;
        year: string;
        type: string;
    }>;
    announcements: Array<{
        id: number;
        title: string;
        date: string;
        content: string;
    }>;
    salaries: Salary[]; // <-- Maaş prop'u buraya eklendi!
}>();

console.log(props);

// --- Yardımcı Fonksiyonlar ---
const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    // Geçersiz tarih kontrolü
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'Onaylandı': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'Beklemede': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'Reddedildi': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const formatCurrency = (value: string | number | null) => {
    if (value === null || value === undefined) return '-';
    // parseFloat ile önce sayıya çevirip sonra formatlıyoruz
    return parseFloat(value as string).toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
};

const getFileDisplayUrl = (filePath: string | null) => {
    if (filePath) {
        // Laravel'in storage link'ini kullandığınızdan emin olun: php artisan storage:link
        return `/storage/${filePath}`;
    }
    return null;
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                Personel Paneli
            </h1>
            <p class="text-gray-600">Hoş geldiniz, {{ user.first_name }}!</p>
        </div>

        <section class="space-y-6 mt-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-800">Kartlarım</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="card in cards" :key="card.id"
                     class="bg-white/70 backdrop-blur-sm shadow-lg p-6 rounded-2xl border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-500 font-medium">Kart Numarası</div>
                        <div class="w-10 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-md"></div>
                    </div>
                    <div class="text-xl font-bold text-gray-900 mb-4 font-mono">{{ card.serial_no }}</div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-gray-600">Bakiye</span>
                        <span class="text-2xl font-bold text-emerald-600">{{ formatCurrency(card.balance) }}</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Veriliş Tarihi:</span>
                            <span>{{ formatDate(card.issue_date) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tür:</span>
                            <span class="capitalize font-medium">{{ card.type }}</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <Link :href="route('cards.edit', card.id)"
                              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300 flex justify-center items-center">
                            Bakiye Yükle
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-6 mt-10">
            <div class="flex items-center gap-3 mb-6 justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-800">Maaş Bilgilerim</h2>
                </div>
                <Link :href="route('salaries.index')"
                      class="py-2 px-6 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 font-medium shadow-md">
                    Tüm Maaşları Görüntüle
                </Link>
            </div>

            <div v-if="salaries.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="salary in salaries" :key="salary.id"
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
                    <p v-if="salary.notes" class="text-xs text-gray-500 mt-3 italic truncate">
                        Not: {{ salary.notes }}
                    </p>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz maaş kaydınız bulunmamaktadır.</p>
            </div>
        </section>


        <section class="space-y-6 mt-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-teal-500 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-800">İzin Taleplerim</h2>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('leaves.create')"
                          class="py-2 px-6 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg hover:from-green-600 hover:to-teal-600 transition-all duration-200 font-medium shadow-md">
                        Yeni İzin Talep Et
                    </Link>
                    <Link :href="route('leaves.index')"
                          class="py-2 px-6 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 font-medium shadow-md">
                        Tüm İzinleri Görüntüle
                    </Link>
                </div>
            </div>

            <div v-if="leaves.length > 0" class="space-y-4">
                <div v-for="request in leaves.slice(0, 3)" :key="request.id"
                     class="bg-white/70 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ request.type }}</h3>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <span>{{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">{{ request.days_count }} gün</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span :class="['px-4 py-2 rounded-full text-sm font-semibold capitalize border', getStatusColor(request.status)]">
                                {{ request.status }}
                            </span>
                            <Link :href="route('leaves.show', request.id)"
                                  class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Detaylar →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz bir izin talebiniz yok.</p>
                <Link :href="route('leaves.create')"
                      class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    İlk İzin Talebinizi Oluşturun
                </Link>
            </div>
        </section>

        <section class="space-y-6 mt-10">
            <div class="flex items-center gap-3">
                <div class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-800">Araç Bilgileri</h2>
            </div>

            <div v-if="cars.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="car in cars.slice(0, 2)" :key="car.id"
                     class="bg-white/70 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white text-xl">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">{{ car.brand }} {{ car.model }}</h3>
                            <p class="text-gray-600 font-mono text-sm">{{ car.license_plate }}</p>
                            <div class="flex gap-4 mt-2 text-sm text-gray-500">
                                <span>{{ car.year }}</span>
                                <span class="capitalize">{{ car.type }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Atanmış araç bulunmamaktadır</p>
            </div>
        </section>

        <section class="space-y-6 mt-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-gray-800">Şirket Duyuruları</h2>
                </div>
                <Link :href="route('announcements.index')"
                      class="py-2 px-6 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-lg hover:from-purple-600 hover:to-indigo-600 transition-all duration-200 font-medium shadow-md">
                    Tüm Duyuruları Görüntüle
                </Link>
            </div>

            <div v-if="announcements.length > 0" class="space-y-4">
                <div v-for="announcement in announcements.slice(0, 3)" :key="announcement.id"
                     class="bg-white/70 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xl flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ announcement.title }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ formatDate(announcement.date) }}</p>
                            <p class="text-gray-700 leading-relaxed mb-4">{{ announcement.content.substring(0, 200) }}...</p>
                            <Link :href="route('announcements.show', announcement.id)"
                                  class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                                Devamını Oku →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz duyuru yok.</p>
            </div>
        </section>
    </UserLayout>
</template>
