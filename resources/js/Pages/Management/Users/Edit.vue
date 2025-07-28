<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Propları tanımla (Laravel kontrolcüsünden gelecek veriler)
interface UserData {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    identity_number: string;
    address: string;
    birth_date: string | null; // Y-m-d formatında string olarak gelecek
    phone: string;
    gender: string;
    unit_id: number | null;
    position: string | null;
    role: string | null; // Mevcut rol adı
    photo_url: string | null; // Mevcut fotoğrafın URL'si
}

interface Props {
    user: { // Buradaki değişiklik: user bir objenin 'data' anahtarını içeriyor
        data: UserData;
    };
    units: Array<{ id: number; name: string }>;
    roles: string[]; // Roller doğrudan string dizisi olarak gelecek
}

const props = defineProps<Props>();

// props.user.data'ya doğrudan erişim için bir sabit tanımlıyoruz
const user = props.user.data;

// Inertia form objesi - Mevcut kullanıcı verileriyle dolduruluyor
const form = useForm({
    _method: 'put', // Laravel'de PUT/PATCH isteği için bu gerekli
    first_name: user.first_name, // Artık doğrudan 'user.' kullanıyoruz
    last_name: user.last_name,
    email: user.email,
    identity_number: user.identity_number,
    address: user.address,
    birth_date: user.birth_date,
    phone: user.phone,
    gender: user.gender,
    unit_id: user.unit_id,
    position: user.position,
    role: user.role,
    photo: null as File | null, // Yeni yüklenecek fotoğraf
    remove_photo: false, // Mevcut fotoğrafı kaldırmak için checkbox durumu
});

// Form gönderimi
const submit = () => {
    form.post(route('management.users.update', user.id), { // users.update rotasına ve kullanıcının ID'sine gönderiyoruz
        forceFormData: true, // Dosya yüklemek için gerekli
        onSuccess: () => {
            alert('Kullanıcı başarıyla güncellendi!');
            // Başarılı güncelleme sonrası genellikle detay sayfasına yönlendirilir
            // Inertia otomatik olarak yönlendirecektir (kontrolcüdeki redirect'e göre)
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};

// Yeni fotoğraf seçildiğinde
const handlePhotoUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.photo = target.files[0];
        form.remove_photo = false; // Yeni fotoğraf yüklenince kaldır seçeneğini kapat
    }
};
</script>

<template>
    <Head :title="`${user.first_name} ${user.last_name} Düzenle`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ user.first_name }} {{ user.last_name }} Düzenle
            </h2>
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
                                >
                                    <option :value="null" disabled>Birim Seçiniz</option>
                                    <option v-for="unit in props.units" :key="unit.id" :value="unit.id">
                                        {{ unit.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.unit_id" />
                            </div>

                            <div>
                                <InputLabel for="position" value="Pozisyon" />
                                <TextInput
                                    id="position"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.position"
                                />
                                <InputError class="mt-2" :message="form.errors.position" />
                            </div>

                            <div>
                                <InputLabel for="role" value="Rol" />
                                <select
                                    id="role"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.role"
                                >
                                    <option value="" disabled>Rol Seçiniz</option>
                                    <option v-for="roleName in props.roles" :key="roleName" :value="roleName">
                                        {{ roleName.charAt(0).toUpperCase() + roleName.slice(1) }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div v-if="user.photo_url">
                                <InputLabel value="Mevcut Fotoğraf" />
                                <img :src="user.photo_url" alt="Mevcut Fotoğraf" class="w-24 h-24 object-cover rounded-full mt-2" />
                                <div class="mt-2">
                                    <input type="checkbox" id="remove_photo" v-model="form.remove_photo" />
                                    <label for="remove_photo" class="ml-2 text-sm text-gray-600">Mevcut fotoğrafı kaldır</label>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="photo" value="Yeni Fotoğraf Yükle" />
                                <input
                                    id="photo"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    @change="handlePhotoUpload"
                                    accept="image/*"
                                />
                                <InputError class="mt-2" :message="form.errors.photo" />
                                <p v-if="form.photo" class="mt-2 text-sm text-gray-600">Seçilen yeni dosya: {{ form.photo.name }}</p>
                            </div>

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Güncelle
                                </PrimaryButton>
                                <Link :href="route('management.users.show', user.id)"
                                      class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
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
