<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { defineProps } from 'vue';
import {Link} from "@inertiajs/vue3";

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
    leave: {
        id: number;
        type: string;
        start_date: string;
        end_date: string;
        days_count: number;
        status: string;
        reason: string;
        created_at: string; // Oluşturulma tarihi
        updated_at: string; // Güncellenme tarihi
    };
}>();

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'pending': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'rejected': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="max-w-3xl mx-auto bg-white/70 backdrop-blur-sm p-8 rounded-2xl shadow-lg border border-gray-200">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">İzin Talebi Detayları (#{{ leave.id }})</h2>
            <div class="space-y-4 text-gray-700">
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-lg font-semibold">İzin Türü:</span>
                    <span class="text-lg font-medium">{{ leave.type }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-lg font-semibold">Başlangıç Tarihi:</span>
                    <span class="text-lg font-medium">{{ formatDate(leave.start_date) }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-lg font-semibold">Bitiş Tarihi:</span>
                    <span class="text-lg font-medium">{{ formatDate(leave.end_date) }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-lg font-semibold">Gün Sayısı:</span>
                    <span class="text-lg font-medium">{{ leave.days_count }} gün</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-lg font-semibold">Durum:</span>
                    <span :class="['px-4 py-2 rounded-full text-sm font-semibold capitalize border', getStatusColor(leave.status)]">
                        {{ leave.status }}
                    </span>
                </div>
                <div>
                    <span class="text-lg font-semibold block mb-2">Sebep:</span>
                    <p class="bg-gray-50 p-4 rounded-md border border-gray-200">{{ leave.reason || 'Belirtilmemiş' }}</p>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500 border-gray-200 flex flex-col gap-2">
                        <span>Oluşturulma: {{ formatDate(leave.created_at) }}</span>
                        <span>Son Güncelleme: {{ formatDate(leave.updated_at) }}</span>
                    </div>
                    <Link class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-300 transition-colors duration-200 shadow-sm mx-4"
                          :href="route('leaves.index')">
                        Geri dön
                    </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
