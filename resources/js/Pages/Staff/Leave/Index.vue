<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

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
    leaves: { // Updated prop type for pagination object
        data: Array<{
            id: number;
            type: string;
            start_date: string;
            end_date: string;
            days_count: number;
            status: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
        // ... other pagination meta data like total, from, to
    };
}>();

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
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
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-teal-500 rounded-full"></div>
                    <h2 class="text-3xl font-bold text-gray-800">Tüm İzin Taleplerim</h2>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('leaves.create')"
                          class="py-2 px-6 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg hover:from-green-600 hover:to-teal-600 transition-all duration-200 font-medium shadow-md">
                        Yeni İzin Talep Et
                    </Link>
                    <Link class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-300 transition-colors duration-200 shadow-sm mx-4"
                          :href="route('home')">
                        Geri dön
                    </Link>
                </div>
            </div>

            <div v-if="leaves.data.length > 0" class="space-y-4">
                <div v-for="request in leaves.data" :key="request.id"
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
                            <div class="flex gap-4 mt-2">
                                <Link v-if="request.status === 'Beklemede'"
                                      :href="route('leaves.edit', request.id)"
                                      class="inline-flex items-center px-3 py-1.5 border border-purple-300 rounded-md shadow-sm text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1 -ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-3.536 3.536l-2.828 2.828-1.757 1.757A1.5 1.5 0 005.172 13.5l.364.364-1.757 1.757a1.5 1.5 0 002.121 2.121l1.757-1.757.364.364a1.5 1.5 0 001.06-.44L14.414 7.586a2 2 0 000-2.828L13.586 3.586z"/>
                                    </svg>
                                    Düzenle
                                </Link>
                                <Link :href="route('leaves.show', request.id)"
                                      class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1 -ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Detaylar
                                </Link>
                                <Link v-if="request.status === 'Beklemede'" method="delete" as="button" :href="route('leaves.destroy', request.id)"
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1 -ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd"/>
                                    </svg>
                                    İptal Et
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <nav class="flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <Link v-for="(link, index) in leaves.links" :key="index"
                              :href="link.url || '#'"
                              :disabled="!link.url"
                              :class="{
                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                                  'bg-blue-600 text-white border-blue-600': link.active,
                                  'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active && link.url,
                                  'rounded-l-md': index === 0,
                                  'rounded-r-md': index === leaves.links.length - 1,
                                  'text-gray-400 cursor-not-allowed': !link.url
                              }"
                              v-html="link.label">
                        </Link>
                    </nav>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz bir izin talebiniz yok.</p>
                <Link :href="route('leaves.create')"
                      class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    İlk İzin Talebinizi Oluşturun
                </Link>
            </div>
        </div>
    </UserLayout>
</template>
