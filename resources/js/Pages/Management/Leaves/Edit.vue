<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { LeaveType, LeaveStatus } from '@/Types/enums';

// Prop tiplerini tanımla
interface LeaveData {
    id: number;
    type: string;
    start_date: string; // Y-m-d formatında string
    end_date: string; // Y-m-d formatında string
    days_count: number;
    reason: string | null;
    status: string;
}

interface UserData {
    id: number;
    first_name: string;
    last_name: string;
}

interface Props {
    leave: {
        data: LeaveData; // LeaveEditResource'dan gelen veri
    };
    user: UserData;
}

const props = defineProps<Props>();
const user_name = props.user.first_name + ' ' + props.user.last_name;

function enumToOptions<T extends Record<string, string>>(enumObj: T) {
    return Object.entries(enumObj).map(([key, value]) => ({ value, label: value }));
}

const leaveTypeOptions = enumToOptions(LeaveType);
const leaveStatusOptions = enumToOptions(LeaveStatus);

// Form objesi - Mevcut izin verileriyle doldurulur
const form = useForm({
    _method: 'patch', // Güncelleme için PATCH metodu
    type: props.leave.data.type,
    start_date: props.leave.data.start_date,
    end_date: props.leave.data.end_date,
    days_count: props.leave.data.days_count,
    reason: props.leave.data.reason || '',
    status: props.leave.data.status,
});

// Tarih seçimi değiştikçe gün sayısını otomatik hesapla
watch([() => form.start_date, () => form.end_date], ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        const start = new Date(newStartDate);
        const end = new Date(newEndDate);
        if (start <= end) {
            const diffTime = Math.abs(end.getTime() - start.getTime());
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Başlangıç ve bitiş gününü de dahil et
            form.days_count = diffDays;
        } else {
            form.days_count = 0; // Başlangıç tarihi bitiş tarihinden sonraysa
        }
    } else {
        form.days_count = 0;
    }
}, { immediate: true }); // Sayfa yüklendiğinde de çalıştır

// Form gönderimi
const submit = () => {
    form.post(route('management.leaves.update', props.leave.data.id), {
        onSuccess: () => {
            alert('İzin talebi başarıyla güncellendi.');
        },
        onError: (errors) => {
            console.error('Form gönderiminde hatalar:', errors);
        }
    });
};

// Tarih ve Saat formatlama fonksiyonu (GG.AA.YYYY SS:DD)
const formatDateTime = (dateTimeString: string) => {
    if (!dateTimeString) return '-';
    const date = new Date(dateTimeString);
    if (isNaN(date.getTime())) return dateTimeString;
    return date.toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="`İzin Talebi #${props.leave.data.id} Düzenle`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                İzin Talebi #{{ props.leave.data.id }} Düzenle
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Personel (Sadece Görüntüle) -->
                            <div>
                                <InputLabel value="Personel" />
                                <TextInput
                                    v-model="user_name"
                                    class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                                    disabled
                                />
                            </div>

                            <!-- İzin Tipi -->
                            <div>
                                <InputLabel for="type" value="İzin Tipi" />
                                <select
                                    id="type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.type"
                                    required
                                >
                                    <option v-for="typeOption in leaveTypeOptions" :key="typeOption.value" :value="typeOption.value">
                                        {{ typeOption.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>

                            <!-- Başlangıç Tarihi -->
                            <div>
                                <InputLabel for="start_date" value="Başlangıç Tarihi" />
                                <TextInput
                                    id="start_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.start_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <!-- Bitiş Tarihi -->
                            <div>
                                <InputLabel for="end_date" value="Bitiş Tarihi" />
                                <TextInput
                                    id="end_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.end_date"
                                    required
                                    :min="form.start_date"
                                />
                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>

                            <!-- Gün Sayısı (Otomatik hesaplanır, düzenlenebilir) -->
                            <div>
                                <InputLabel for="days_count" value="Gün Sayısı" />
                                <TextInput
                                    id="days_count"
                                    type="number"
                                    class="mt-1 block w-full bg-gray-100"
                                    v-model="form.days_count"
                                    required
                                    min="1"
                                    disabled
                                />
                                <InputError class="mt-2" :message="form.errors.days_count" />
                            </div>

                            <!-- Talep Nedeni -->
                            <div>
                                <InputLabel for="reason" value="Talep Nedeni" />
                                <textarea
                                    id="reason"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.reason"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.reason" />
                            </div>

                            <!-- Durum -->
                            <div>
                                <InputLabel for="status" value="Durum" />
                                <select
                                    id="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.status"
                                    required
                                >
                                    <option v-for="statusOption in leaveStatusOptions" :key="statusOption.value" :value="statusOption.value">
                                        {{ statusOption.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div class="flex items-center justify-end mt-4 space-x-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Güncelle
                                </PrimaryButton>
                                <Link :href="route('management.leaves.index', props.leave.data.id)"
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
