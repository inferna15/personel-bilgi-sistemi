<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface UserData {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    identity_number: string;
    birth_date: string | null;
    gender: string;
    phone: string;
    address: string;
    position: string | null;
    photo: string | null;
    unit: string | null;
    role: string | null;
    created_at: string | null;
    updated_at: string | null;
}

interface Props {
    user: {
        data: UserData;
    };
}

const props = defineProps<Props>();

const user = props.user.data;

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
    <Head :title="`${user.first_name} ${user.last_name} Detayları`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ user.first_name }} {{ user.last_name }} Detayları
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8">
                            <div class="flex-shrink-0">
                                <img :src="getPhotoDisplayUrl(user.photo)"
                                    :alt="`${user.first_name} ${user.last_name} Profil Fotoğrafı`"
                                    class="w-32 h-32 object-cover rounded-full shadow-lg border-4 border-indigo-500"
                                />
                            </div>

                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                    {{ user.first_name }} {{ user.last_name }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                                    <div>
                                        <p class="font-semibold">E-posta:</p>
                                        <p>{{ user.email }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Kimlik Numarası:</p>
                                        <p>{{ user.identity_number }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Doğum Tarihi:</p>
                                        <p>{{ user.birth_date || 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Cinsiyet:</p>
                                        <p>{{ user.gender === 'male' ? 'Erkek' : 'Kadın' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Telefon:</p>
                                        <p>{{ user.phone || 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Birim:</p>
                                        <p>{{ user.unit || 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Pozisyon:</p>
                                        <p>{{ user.position || 'Belirtilmemiş' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Rol:</p>
                                        <p>{{ user.role || 'Belirtilmemiş' }}</p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <p class="font-semibold">Adres:</p>
                                    <p>{{ user.address || 'Belirtilmemiş' }}</p>
                                </div>

                                <div class="mt-6 text-sm text-gray-500">
                                    <p v-if="user.created_at">Oluşturulma Tarihi: {{ user.created_at }}</p>
                                    <p v-if="user.updated_at">Son Güncelleme: {{ user.updated_at }}</p>
                                </div>

                                <div class="mt-8 flex space-x-4">
                                    <Link :href="route('management.users.index')"
                                          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Geri Dön
                                    </Link>
                                    <Link :href="route('management.users.edit', user.id)"
                                          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Düzenle
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
