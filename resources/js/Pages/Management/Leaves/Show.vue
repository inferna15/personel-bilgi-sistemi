<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Prop tiplerini tanımla
interface UserDataForLeave {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

interface LeaveData {
    id: number;
    type: string;
    start_date: string;
    end_date: string;
    days_count: number;
    reason: string | null;
    status: string;
    reviewed_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    leave: { data: LeaveData }; // LeaveShowResource'dan gelen veri
    user: UserDataForLeave; // Leave'in sahibi olan kullanıcı (UserShowResource)
    reviewed_by: UserDataForLeave | null; // İnceleyen personel (LeaveUserResource), null olabilir
}

const props = defineProps<Props>();

// Daha kısa erişim için sabitler
const leave = props.leave.data;
const user = props.user;
const reviewedBy = props.reviewed_by || null; // Nullable olabilir

// Tarih formatlama fonksiyonu (GG.AA.YYYY)
const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
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

// Durum etiketi için renk sınıfı
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
    <Head :title="`İzin Talebi #${leave.id} Detayları`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                İzin Talebi #{{ leave.id }} Detayları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                    İzin Talebi Detayları
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                    <div>
                                        <p class="font-semibold">Personel:</p>
                                        <p>{{ user.first_name }} {{ user.last_name }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">E-posta:</p>
                                        <p>{{ user.email }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">İzin Tipi:</p>
                                        <p>{{ leave.type }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Başlangıç Tarihi:</p>
                                        <p>{{ formatDate(leave.start_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Bitiş Tarihi:</p>
                                        <p>{{ formatDate(leave.end_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Gün Sayısı:</p>
                                        <p>{{ leave.days_count }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Durum:</p>
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColorClass(leave.status)]">
                                            {{ leave.status }}
                                        </span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="font-semibold">Talep Nedeni:</p>
                                        <p>{{ leave.reason || 'Belirtilmemiş' }}</p>
                                    </div>
                                </div>

                                <!-- İnceleyen Personel Bilgileri -->
                                <div v-if="reviewedBy" class="mt-6 border-t pt-4 border-gray-200">
                                    <p class="font-semibold text-lg text-gray-800 mb-2">İnceleme Bilgileri:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                        <div>
                                            <p class="font-semibold">İnceleyen Personel:</p>
                                            <p>{{ reviewedBy.first_name }} {{ reviewedBy.last_name }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">İnceleme Tarihi:</p>
                                            <p>{{ formatDateTime(leave.reviewed_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Butonlar: Düzenle ve Geri Dön -->
                                <div class="mt-8 flex space-x-4">
                                    <Link :href="route('management.leaves.edit', leave.id)"
                                          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Düzenle
                                    </Link>
                                    <Link :href="route('management.leaves.index')"
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
