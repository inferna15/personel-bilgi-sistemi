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
    announcements: { // Updated prop type for pagination object
        data: Array<{
            id: number;
            title: string;
            date: string;
            content: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        current_page: number;
        last_page: number;
        // ... other pagination meta data
    };
}>();

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-8 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                    <h2 class="text-3xl font-bold text-gray-800">Tüm Şirket Duyuruları</h2>
                </div>
                <Link class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-300 transition-colors duration-200 shadow-sm mx-4"
                      :href="route('home')">
                    Geri dön
                </Link>
            </div>

            <div v-if="announcements.data.length > 0" class="space-y-4">
                <div v-for="announcement in announcements.data" :key="announcement.id"
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

                <div class="mt-8 flex justify-center">
                    <nav class="flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <Link v-for="(link, index) in announcements.links" :key="index"
                              :href="link.url || '#'"
                              :disabled="!link.url"
                              :class="{
                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                                  'bg-blue-600 text-white border-blue-600': link.active,
                                  'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active && link.url,
                                  'rounded-l-md': index === 0,
                                  'rounded-r-md': index === announcements.links.length - 1,
                                  'text-gray-400 cursor-not-allowed': !link.url
                              }"
                              v-html="link.label">
                        </Link>
                    </nav>
                </div>
            </div>
            <div v-else class="bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                <p class="text-gray-500 italic text-lg">Henüz duyuru yok.</p>
            </div>
        </div>
    </UserLayout>
</template>
