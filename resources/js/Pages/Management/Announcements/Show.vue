<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Prop tipini tanımla
interface AnnouncementData {
    id: number;
    title: string;
    date: string; // YYYY-MM-DD formatında string
    content: string;
    created_at: string; // YYYY-MM-DD HH:MM:SS formatında string
    updated_at: string; // YYYY-MM-DD HH:MM:SS formatında string
}

interface Props {
    announcement: AnnouncementData; // Doğrudan duyuru objesi geliyor
}

const props = defineProps<Props>();

// Daha kısa erişim için sabit
const announcement = props.announcement;

// Tarih formatlama fonksiyonu (GG.AA.YYYY)
const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

// Tarih ve Saat formatlama fonksiyonu (GG.AA.YYYY SS:DD)
const formatDateTime = (dateTimeString: string | null) => {
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
</script>

<template>
    <Head :title="`Duyuru #${announcement.id} Detayları`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Duyuru #{{ announcement.id }} Detayları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                    {{ announcement.title }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                    <div>
                                        <p class="font-semibold">Duyuru Tarihi:</p>
                                        <p>{{ formatDate(announcement.date) }}</p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <p class="font-semibold">İçerik:</p>
                                    <p class="whitespace-pre-wrap">{{ announcement.content }}</p>
                                </div>

                                <div class="mt-6 text-sm text-gray-500">
                                    <p>Oluşturulma Tarihi: {{ formatDateTime(announcement.created_at) }}</p>
                                    <p>Son Güncelleme: {{ formatDateTime(announcement.updated_at) }}</p>
                                </div>

                                <!-- Butonlar: Düzenle ve Geri Dön -->
                                <div class="mt-8 flex space-x-4">
                                    <Link :href="route('management.announcements.edit', announcement.id)"
                                          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Düzenle
                                    </Link>
                                    <Link :href="route('management.announcements.index')"
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
