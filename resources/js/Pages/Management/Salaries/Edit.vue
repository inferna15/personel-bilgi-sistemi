<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// --- TypeScript Arayüzleri (Interfaces) ---
interface User {
    id: number;
    first_name: string;
    last_name: string;
}

interface Salary {
    id: number;
    user_id: number;
    user: User; // İlişkili user objesi, controller'dan loaded gelecek
    pay_date: string;
    net_salary: string;
    gross_salary: string | null;
    payroll_file_path: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    salary: Salary; // Sadece düzenlenecek maaş kaydı gönderiliyor
    // users: User[]; // Artık bu prop gönderilmiyor
}

const props = defineProps<Props>();

// --- Form State ---
const form = useForm({
    _method: 'put',
    user_id: props.salary.user_id, // User ID'si yine de backend'e gönderilmeli
    pay_date: props.salary.pay_date,
    net_salary: props.salary.net_salary,
    gross_salary: props.salary.gross_salary,
    payroll_file: null as File | null,
    notes: props.salary.notes,
});

// --- Form Gönderme Metodu ---
const submit = () => {
    form.post(route('management.salaries.update', props.salary.id), {
        forceFormData: true,
        onSuccess: () => {
            // Başarılı olursa liste sayfasına yönlendirme veya bildirim
        },
    });
};

// --- Dosya Seçimi Yönetimi ---
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.payroll_file = target.files[0];
    } else {
        form.payroll_file = null;
    }
};

// Mevcut bordro dosyasını silme fonksiyonu (backend update metodunda ele alınıyor)
const removeExistingFile = () => {
    if (confirm('Mevcut bordro dosyasını silmek istediğinizden emin misiniz?')) {
        form.payroll_file = null; // Yeni dosya seçilmediğini belirtir
        props.salary.payroll_file_path = null; // Frontend'de görsel olarak temizle
        alert('Mevcut bordro dosyası kaldırıldı. Güncellemek için "Kaydet" butonuna basın.');
    }
};
</script>

<template>
    <Head title="Maaş Kaydını Düzenle" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Maaş Kaydını Düzenle</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel value="Personel" />
                                <p class="mt-1 p-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700">
                                    {{ props.salary.user.first_name }} {{ props.salary.user.last_name }}
                                </p>
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
                                <InputLabel value="Maaş Bordrosu (PDF) (İsteğe Bağlı)" />

                                <div v-if="props.salary.payroll_file_path" class="flex items-center space-x-2 mt-2">
                                    <a :href="handleFileChange(props.salary.payroll_file_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l3-3m-3 3l-3-3m-3 8h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Mevcut Bordroyu Görüntüle
                                    </a>
                                    <button @click.prevent="removeExistingFile" type="button" class="text-red-600 hover:text-red-900 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>

                                <input
                                    type="file"
                                    id="payroll_file"
                                    @change="handleFileChange"
                                    accept="application/pdf"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-2"
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
                                    Güncelle
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
