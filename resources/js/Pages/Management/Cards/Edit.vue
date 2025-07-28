<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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
}

interface Props {
    card: CardData; // Doğrudan card objesi geliyor
}

const props = defineProps<Props>();

// Kart tipleri (Controller'dan gelmesi daha iyi olurdu, şimdilik hardcode)
const cardTypes = [
    'Personel Kimlik Kartı',
    'Giriş Kartı',
    'Ödeme Kartı',
    'Misafir Kartı',
    'Araç Geçiş Kartı',
    'Yemek Kartı',
];

// Form objesi - Mevcut kart verileriyle doldurulur
const form = useForm({
    _method: 'put', // Güncelleme için PUT metodu
    user_id: props.card.user_id, // Kullanıcı ID'si (değiştirilemez)
    serial_no: props.card.serial_no,
    type: props.card.type,
    issue_date: props.card.issue_date,
    balance: props.card.balance,
    last_spend_date: props.card.last_spend_date ? props.card.last_spend_date.split(' ')[0] : '', // Sadece tarih kısmını al
    last_spend_place: props.card.last_spend_place || '',
    last_upload_date: props.card.last_upload_date ? props.card.last_upload_date.split(' ')[0] : '', // Sadece tarih kısmını al
    last_upload_place: props.card.last_upload_place || '',
});

// Form gönderimi
const submit = () => {
    form.post(route('management.cards.update', props.card.id), {
        onSuccess: () => {
            alert('Kart başarıyla güncellendi.');
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};
</script>

<template>
    <Head :title="`Kart #${props.card.id} Düzenle`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kart #{{ props.card.id }} Düzenle
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Kart Sahibi Personel (Sadece Görüntüle) -->
                            <div>
                                <InputLabel value="Kart Sahibi Personel" />
                                <div class="mt-1 block w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-md shadow-sm">
                                    {{ props.card.full_name }}
                                </div>
                            </div>

                            <!-- Seri No -->
                            <div>
                                <InputLabel for="serial_no" value="Seri Numarası" />
                                <TextInput
                                    id="serial_no"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.serial_no"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.serial_no" />
                            </div>

                            <!-- Kart Tipi -->
                            <div>
                                <InputLabel for="type" value="Kart Tipi" />
                                <select
                                    id="type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.type"
                                    required
                                >
                                    <option v-for="cardType in cardTypes" :key="cardType" :value="cardType">
                                        {{ cardType }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>

                            <!-- Veriliş Tarihi -->
                            <div>
                                <InputLabel for="issue_date" value="Veriliş Tarihi" />
                                <TextInput
                                    id="issue_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.issue_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.issue_date" />
                            </div>

                            <!-- Bakiye -->
                            <div>
                                <InputLabel for="balance" value="Bakiye (TL)" />
                                <TextInput
                                    id="balance"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model.number="form.balance"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.balance" />
                            </div>

                            <h4 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Son İşlem Bilgileri</h4>

                            <!-- Son Harcama Tarihi -->
                            <div>
                                <InputLabel for="last_spend_date" value="Son Harcama Tarihi" />
                                <TextInput
                                    id="last_spend_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.last_spend_date"
                                />
                                <InputError class="mt-2" :message="form.errors.last_spend_date" />
                            </div>

                            <!-- Son Harcama Yeri -->
                            <div>
                                <InputLabel for="last_spend_place" value="Son Harcama Yeri" />
                                <TextInput
                                    id="last_spend_place"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.last_spend_place"
                                />
                                <InputError class="mt-2" :message="form.errors.last_spend_place" />
                            </div>

                            <!-- Son Yükleme Tarihi -->
                            <div>
                                <InputLabel for="last_upload_date" value="Son Yükleme Tarihi" />
                                <TextInput
                                    id="last_upload_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.last_upload_date"
                                />
                                <InputError class="mt-2" :message="form.errors.last_upload_date" />
                            </div>

                            <!-- Son Yükleme Yeri -->
                            <div>
                                <InputLabel for="last_upload_place" value="Son Yükleme Yeri" />
                                <TextInput
                                    id="last_upload_place"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.last_upload_place"
                                />
                                <InputError class="mt-2" :message="form.errors.last_upload_place" />
                            </div>

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Güncelle
                                </PrimaryButton>
                                <Link :href="route('management.cards.index', props.card.id)"
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
