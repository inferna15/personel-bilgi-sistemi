<script setup lang="ts">
import { ref } from 'vue';
import {useForm, Head, Link} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
// import SelectInput from '@/Components/SelectInput.vue'; // Bu satır kaldırıldı!

// Propları tanımla (Laravel kontrolcüsünden gelecek veriler)
interface Props {
    units: Array<{ id: number; name: string }>;
    roles: Array<{string}>;
}

const props = defineProps<Props>();

// Inertia form objesi
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    identity_number: '',
    address: '',
    birth_date: '',
    phone: '',
    gender: '',
    unit_id: null as number | null,
    position: '',
    role: '',
    photo: null as File | null,
});

// Form gönderimi
const submit = () => {
    form.post(route('management.users.store'), {
        forceFormData: true,
        onSuccess: () => {
            alert('Kullanıcı başarıyla eklendi!');
            form.reset();
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};

// Fotoğraf seçildiğinde
const handlePhotoUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.photo = target.files[0];
    }
};
</script>

<template>
    <Head title="Yeni Kullanıcı Ekle" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Kullanıcı Ekle</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="first_name" value="Ad" />
                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                    autocomplete="first_name"
                                />
                                <InputError class="mt-2" :message="form.errors.first_name" />
                            </div>

                            <div>
                                <InputLabel for="last_name" value="Soyad" />
                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.last_name"
                                    required
                                    autocomplete="last_name"
                                />
                                <InputError class="mt-2" :message="form.errors.last_name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="E-posta" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="identity_number" value="Kimlik Numarası" />
                                <TextInput
                                    id="identity_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.identity_number"
                                    required
                                    autocomplete="off"
                                    maxlength="11"
                                />
                                <InputError class="mt-2" :message="form.errors.identity_number" />
                            </div>

                            <div>
                                <InputLabel for="birth_date" value="Doğum Tarihi" />
                                <TextInput
                                    id="birth_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.birth_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.birth_date" />
                            </div>

                            <div>
                                <InputLabel for="phone" value="Telefon Numarası" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    required
                                    autocomplete="tel"
                                    placeholder="örn: 5xx xxx xx xx"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <div>
                                <InputLabel for="address" value="Adres" />
                                <textarea
                                    id="address"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.address"
                                    rows="3"
                                    required
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div>
                                <InputLabel for="gender" value="Cinsiyet" />
                                <select
                                    id="gender"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.gender"
                                    required
                                >
                                    <option value="" disabled>Cinsiyet Seçiniz</option>
                                    <option value="Erkek">Erkek</option>
                                    <option value="Kadın">Kadın</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.gender" />
                            </div>

                            <div>
                                <InputLabel for="unit_id" value="Birim" />
                                <select
                                    id="unit_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.unit_id"
                                    required
                                >
                                    <option :value="null" disabled>Birim Seçiniz</option>
                                    <option v-for="unit in props.units" :key="unit.id" :value="unit.id">
                                        {{ unit.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.unit_id" />
                            </div>

                            <div>
                                <InputLabel for="role" value="Rol" />
                                <select
                                    id="role"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.role"
                                    required
                                >
                                    <option value="" disabled>Rol Seçiniz</option>
                                    <option v-for="role in props.roles" :key="role" :value="role">
                                        {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div>
                                <InputLabel for="photo" value="Fotoğraf" />
                                <input
                                    id="photo"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    @change="handlePhotoUpload"
                                    accept="image/*"
                                />
                                <InputError class="mt-2" :message="form.errors.photo" />
                                <p v-if="form.photo" class="mt-2 text-sm text-gray-600">Seçilen dosya: {{ form.photo.name }}</p>
                            </div>

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Kaydet
                                </PrimaryButton>
                                <Link :href="route('management.users.index')"
                                      class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    İptal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
