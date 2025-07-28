<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Prop tiplerini tanımla
interface CarData {
    id: number;
    user_id: number;
    full_name: string;
    brand: string;
    model: string;
    color: string;
    license_plate: string;
    year: number;
    type: string;
    created_at: string; // YYYY-MM-DD HH:MM:SS formatında string
    updated_at: string; // YYYY-MM-DD HH:MM:SS formatında string
}

interface Props {
    car: CarData; // Doğrudan car objesi geliyor
}

const props = defineProps<Props>();

// Araç tipleri (Controller'dan gelmesi daha iyi olurdu, şimdilik hardcode)
const carTypes = [
    'Sedan',
    'Hatchback',
    'SUV / Crossover',
    'Kamyonet / Pickup',
    'Coupe',
    'Cabriolet / Cabrio',
    'Minivan / MPV',
    'Station Wagon / Kombi',
    'Off-road / 4x4',
    'Elektrikli',
    'Diğer',
];

// Form objesi - Mevcut araç verileriyle doldurulur
const form = useForm({
    _method: 'put', // Güncelleme için PUT metodu
    user_id: props.car.user_id, // Kullanıcı ID'si (değiştirilemez)
    brand: props.car.brand,
    model: props.car.model,
    color: props.car.color,
    license_plate: props.car.license_plate,
    year: props.car.year,
    type: props.car.type,
});

// Form gönderimi
const submit = () => {
    form.post(route('management.cars.update', props.car.id), {
        onSuccess: () => {
            alert('Araç başarıyla güncellendi!');
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};
</script>

<template>
    <Head :title="`Araç #${props.car.id} Düzenle`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Araç #{{ props.car.id }} Düzenle
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Araç Sahibi Personel (Sadece Görüntüle) -->
                            <div>
                                <InputLabel value="Araç Sahibi Personel" />
                                <div class="mt-1 block w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-md shadow-sm">
                                    {{ props.car.full_name || 'Bilinmeyen Personel' }}
                                </div>
                            </div>

                            <!-- Marka -->
                            <div>
                                <InputLabel for="brand" value="Marka" />
                                <TextInput
                                    id="brand"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.brand"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.brand" />
                            </div>

                            <!-- Model -->
                            <div>
                                <InputLabel for="model" value="Model" />
                                <TextInput
                                    id="model"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.model"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.model" />
                            </div>

                            <!-- Renk -->
                            <div>
                                <InputLabel for="color" value="Renk" />
                                <TextInput
                                    id="color"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.color"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.color" />
                            </div>

                            <!-- Plaka -->
                            <div>
                                <InputLabel for="license_plate" value="Plaka Numarası" />
                                <TextInput
                                    id="license_plate"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.license_plate"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.license_plate" />
                            </div>

                            <!-- Yıl -->
                            <div>
                                <InputLabel for="year" value="Model Yılı" />
                                <TextInput
                                    id="year"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model.number="form.year"
                                    required
                                    :min="1900"
                                    :max="new Date().getFullYear() + 1"
                                />
                                <InputError class="mt-2" :message="form.errors.year" />
                            </div>

                            <!-- Tip -->
                            <div>
                                <InputLabel for="type" value="Araç Tipi" />
                                <select
                                    id="type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.type"
                                    required
                                >
                                    <option v-for="carType in carTypes" :key="carType" :value="carType">
                                        {{ carType }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Güncelle
                                </PrimaryButton>
                                <Link :href="route('management.cars.index', props.car.id)"
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
