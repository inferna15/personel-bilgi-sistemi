<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Prop tiplerini tanımla
interface UserOption {
    id: number;
    first_name: string; // LeaveUserResource'dan gelecek
    last_name: string;
}

interface Props {
    users: UserOption[] | null; // Sadece user_id yoksa gelir (tüm kullanıcılar)
    user_id: number | null; // URL'den geliyorsa (önceden seçili kullanıcı ID'si)
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

// Form objesi
const form = useForm({
    // user_id'yi başlatırken: Eğer props.user_id varsa onu kullan, yoksa
    // props.users (tüm kullanıcılar listesi) varsa ilkini seç, yoksa null.
    user_id: props.user_id || (props.users && props.users.length > 0 ? props.users[0].id : null),
    serial_no: '',
    type: cardTypes[0], // Varsayılan olarak ilk kart tipini seç
    issue_date: '',
    balance: 0,
});

// Form gönderimi
const submit = () => {
    form.post(route('management.cards.store'), {
        onSuccess: () => {
            alert('Kart başarıyla oluşturuldu.');
            form.reset(); // Formu sıfırla
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};
</script>

<template>
    <Head title="Yeni Kart Oluştur" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Kart Oluştur</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Kart Sahibi Personel Seçimi / Görüntüleme -->
                            <!-- Senaryo 1: Tüm kullanıcılar listesi varsa (user_id URL'den gelmediyse) -->
                            <div v-if="user_id === null">
                                <InputLabel for="user_id" value="Kart Sahibi Personel" />
                                <select
                                    id="user_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.user_id"
                                    required
                                >
                                    <option :value="null" disabled>Personel Seçiniz</option>
                                    <option v-for="userOption in props.users" :key="userOption.id" :value="userOption.id">
                                        {{ userOption.first_name }} {{ userOption.last_name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.user_id" />
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
                                    autofocus
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

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Kaydet
                                </PrimaryButton>
                                <Link :href="route('management.cards.index')"
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
