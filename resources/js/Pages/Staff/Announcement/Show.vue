<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { defineProps } from 'vue';

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
    announcement: {
        id: number;
        title: string;
        date: string;
        content: string;
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
        <div class="max-w-4xl mx-auto bg-white/70 backdrop-blur-sm p-8 rounded-2xl shadow-lg border border-gray-200">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ announcement.title }}</h1>
            <p class="text-sm text-gray-500 mb-6">{{ formatDate(announcement.date) }}</p>
            <div class="prose max-w-none text-gray-800 leading-relaxed text-lg">
                <p>{{ announcement.content }}</p>
            </div>
            <div class="mt-8 text-right">
                <button @click="$inertia.visit(route('announcements.index'))"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    ← Tüm Duyurulara Geri Dön
                </button>
            </div>
        </div>
    </UserLayout>
</template>
