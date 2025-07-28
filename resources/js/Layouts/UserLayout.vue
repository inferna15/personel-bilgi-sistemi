<script setup lang="ts">
import { defineProps } from 'vue';
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const role = page.props.auth?.user?.role;

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
}>();

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

const getPhotoDisplayUrl = (photoPath: string | null) => {
    if (photoPath) {
        // Önüne '/storage/' ekleyerek doğru yolu oluşturuyoruz.
        // Örneğin: 'user_photos/abc.png' -> '/storage/user_photos/abc.png'
        return `/storage/${photoPath}`;
    }
    return '/storage/user_photos/default_avatar.jpg';
};
</script>

<template>
    <div class="flex min-h-screen font-sans bg-gradient-to-br from-slate-50 to-blue-50 text-gray-800">
        <div class="w-80 fixed top-0 left-0 h-full bg-white/80 backdrop-blur-sm p-8 shadow-xl border-r border-gray-200 flex flex-col overflow-y-auto">
            <div class="text-center mb-8">
                <div class="relative inline-block mb-4">
                    <img :src="getPhotoDisplayUrl(user.photo)" class="w-28 h-28 object-cover rounded-full shadow-lg border-4 border-white" />
                    <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ user.first_name }} {{ user.last_name }}</h2>
                <p class="text-sm text-gray-600 font-medium">{{ user.email }}</p>
                <div class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                    {{ user.position }}
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">TC Kimlik:</span>
                            <span class="font-medium">{{ user.identity_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Cinsiyet:</span>
                            <span class="font-medium capitalize">{{ user.gender }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Birim:</span>
                            <span class="font-medium">{{ unit }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Doğum Tarihi:</span>
                            <span class="font-medium">{{ formatDate(user.birth_date) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Telefon:</span>
                            <span class="font-medium">{{ user.phone }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm">
                        <span class="text-gray-500 block mb-1">Adres:</span>
                        <span class="font-medium">{{ user.address }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-3 mt-auto">
                <Link v-if="role === 'Admin' || role === 'Yönetici'"
                      :href="route('management.dashboard')"
                      class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-200 text-center font-medium shadow-md">
                    Yönetim Paneli
                </Link>
                <Link method="post" :href="route('logout')"
                      class="w-full py-3 px-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 text-center font-medium shadow-md">
                    Çıkış Yap
                </Link>
            </div>
        </div>

        <div class="ml-80 w-full min-h-screen p-8 space-y-8 overflow-y-auto">
            <slot />
        </div>
    </div>
</template>

<style scoped>
/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #6366f1, #8b5cf6);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed);
}
</style>
