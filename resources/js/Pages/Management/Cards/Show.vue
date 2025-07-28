<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Prop tiplerini tanımla
interface CardData {
    id: number;
    user_id: number;
    full_name: string;
    serial_no: string;
    type: string;
    issue_date: string; // YYYY-MM-DD formatında string
    balance: number;
    last_spend_date: string | null; // YYYY-MM-DD HH:MM:SS formatında string
    last_spend_place: string | null;
    last_upload_date: string | null; // YYYY-MM-DD HH:MM:SS formatında string
    last_upload_place: string | null;
    created_at: string; // YYYY-MM-DD HH:MM:SS formatında string
    updated_at: string; // YYYY-MM-DD HH:MM:SS formatında string
}

interface Props {
    card: CardData; // Doğrudan card objesi geliyor
}

const props = defineProps<Props>();

// Daha kısa erişim için sabit
const card = props.card;

// Tarih formatlama fonksiyonu (GG.AA.YYYY)
const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    // YYYY-MM-DD veya YYYY-MM-DD HH:MM:SS formatları için
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; // Geçersiz tarihse orijinali döndür
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
    <Head :title="`Kart #${card.id} Detayları`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kart #{{ card.id }} Detayları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                    Kart Detayları
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                    <div>
                                        <p class="font-semibold">Kart Sahibi:</p>
                                        <p>{{ card.full_name || '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Seri Numarası:</p>
                                        <p>{{ card.serial_no }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Kart Tipi:</p>
                                        <p>{{ card.type }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Bakiye:</p>
                                        <p>{{ card.balance.toFixed(2) }} TL</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Veriliş Tarihi:</p>
                                        <p>{{ formatDate(card.issue_date) }}</p>
                                    </div>
                                </div>

                                <h4 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Son İşlem Bilgileri</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                    <div>
                                        <p class="font-semibold">Son Harcama Tarihi:</p>
                                        <p>{{ formatDateTime(card.last_spend_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Son Harcama Yeri:</p>
                                        <p>{{ card.last_spend_place || '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Son Yükleme Tarihi:</p>
                                        <p>{{ formatDateTime(card.last_upload_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Son Yükleme Yeri:</p>
                                        <p>{{ card.last_upload_place || '-' }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 text-sm text-gray-500">
                                    <p>Oluşturulma Tarihi: {{ formatDateTime(card.created_at) }}</p>
                                    <p>Son Güncelleme: {{ formatDateTime(card.updated_at) }}</p>
                                </div>

                                <!-- Butonlar: Düzenle ve Geri Dön -->
                                <div class="mt-8 flex space-x-4">
                                    <Link :href="route('management.cards.edit', card.id)"
                                          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Düzenle
                                    </Link>
                                    <Link :href="route('management.cards.index')"
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
