<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// --- TypeScript Arayüzleri (Interfaces) ---
// Prop olarak gelecek kullanıcı verisi için
interface User {
    id: number;
    first_name: string;
    last_name: string;
}

// Vue bileşenine gelecek props'un tanımı
interface Props {
    users: User[]; // Controller'dan gelen kullanıcı listesi
}

// Props'u tanımla
const props = defineProps<Props>();

// --- Form State ---
// Inertia useForm hook'u ile form verilerini ve durumunu yönet
const form = useForm({
    user_id: '', // Seçilen personelin ID'si
    pay_date: '', // Maaşın yatırıldığı tarih
    net_salary: '', // Net maaş miktarı
    gross_salary: '', // Brüt maaş (isteğe bağlı)
    payroll_file: null as File | null, // Bordro dosyası (File tipinde veya null)
    notes: '', // Ek notlar (isteğe bağlı)
});

// --- Form Gönderme Metodu ---
const submit = () => {
    // Formu POST isteğiyle Laravel'e gönder
    form.post(route('management.salaries.store'), {
        forceFormData: true, // Dosya yüklemek için Inertia'ya FormData kullanmasını söyle
        onSuccess: () => {
            // Başarılı olursa formu sıfırla
            form.reset('user_id', 'pay_date', 'net_salary', 'gross_salary', 'payroll_file', 'notes');
            // Kullanıcıya bildirim gösterebilirsiniz (flash mesajlar aracılığıyla)
        },
        // Hata durumunda ne yapılacağını burada belirtebilirsiniz (örneğin toast mesajı)
    });
};

// --- Dosya Seçimi Yönetimi ---
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.payroll_file = target.files[0]; // Seçilen ilk dosyayı atar
    } else {
        form.payroll_file = null; // Dosya seçimi iptal edilirse null yapar
    }
};
</script>

<template>
    <Head title="Yeni Maaş Kaydı" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Maaş Kaydı Ekle</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="user_id" value="Personel" />
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Personel Seçiniz</option>
                                    <option v-for="user in props.users" :key="user.id" :value="user.id">
                                        {{ user.first_name }} {{ user.last_name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.user_id" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="pay_date" value="Ödeme Tarihi" />
                                <TextInput
                                    id="pay_date"
                                    v-model="form.pay_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.pay_date" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="net_salary" value="Net Maaş (TL)" />
                                <TextInput
                                    id="net_salary"
                                    v-model="form.net_salary"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.net_salary" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="gross_salary" value="Brüt Maaş (TL) (İsteğe Bağlı)" />
                                <TextInput
                                    id="gross_salary"
                                    v-model="form.gross_salary"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.gross_salary" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="payroll_file" value="Maaş Bordrosu (PDF) (İsteğe Bağlı)" />
                                <input
                                    type="file"
                                    id="payroll_file"
                                    @change="handleFileChange"
                                    accept="application/pdf"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <InputError class="mt-2" :message="form.errors.payroll_file" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="notes" value="Notlar (İsteğe Bağlı)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('management.salaries.index')" class="mr-4 text-gray-600 hover:text-gray-900">
                                    İptal
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Kaydet
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
