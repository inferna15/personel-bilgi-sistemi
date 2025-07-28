<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';
import { defineProps, onMounted, watch } from 'vue'; // watch'ı ekledik
import { Link, useForm } from "@inertiajs/vue3"; // useForm'u ekledik
import { LeaveType } from '@/Types/enums'; // Enum'unuzu doğru yoldan import ettiğinizden emin olun

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
    leave: { // Düzenlenecek izin talebinin detayları
        id: number;
        type: string;
        start_date: string;
        end_date: string;
        days_count: number;
        status: string;
        reason: string;
    };
}>();

function enumToOptions<T extends Record<string, string>>(enumObj: T) {
    return Object.values(enumObj).map(value => ({ value, label: value }));
}

const leaveTypeOptions = enumToOptions(LeaveType);

// useForm ile form durumunu yönetme
const form = useForm({
    type: '' as string,
    start_date: '' as string,
    end_date: '' as string,
    reason: '' as string,
    days_count: 0 as number, // days_count'u form objesine ekledik
});

// Bileşen yüklendiğinde mevcut izin bilgilerini forma doldur
onMounted(() => {
    if (props.leave) {
        form.type = props.leave.type;
        // Tarihleri YYYY-MM-DD formatına dönüştürerek atama
        form.start_date = formatDateForInput(props.leave.start_date);
        form.end_date = formatDateForInput(props.leave.end_date);
        form.reason = props.leave.reason;
        form.days_count = props.leave.days_count; // Mevcut gün sayısını da doldur
    }
});

// Tarihleri YYYY-MM-DD formatına dönüştüren yardımcı fonksiyon
const formatDateForInput = (dateString: string): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Başlangıç veya bitiş tarihi değiştiğinde days_count'u hesapla
watch([() => form.start_date, () => form.end_date], ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        const startDateObj = new Date(newStartDate);
        const endDateObj = new Date(newEndDate);

        if (!isNaN(startDateObj.getTime()) && !isNaN(endDateObj.getTime()) && startDateObj <= endDateObj) {
            const timeDiff = endDateObj.getTime() - startDateObj.getTime();
            form.days_count = Math.round(timeDiff / (1000 * 60 * 60 * 24)) + 1;
        } else {
            form.days_count = 0;
        }
    } else {
        form.days_count = 0;
    }
}, { immediate: true }); // Bileşen yüklendiğinde de bir kez çalışmasını sağla


// Formu gönderme fonksiyonu
const updateLeaveRequest = () => {
    // PUT isteği ile güncelleme
    form.put(route('leaves.update', props.leave.id), {
        onSuccess: () => {
            alert('İzin talebi başarıyla güncellendi!');
            // Başarılı güncelleme sonrası yönlendirme veya başka bir işlem
        },
        onError: (errors) => {
            console.error('İzin talebi güncellenirken hata oluştu:', errors);
        },
        onFinish: () => {
            // İstek tamamlandığında (başarılı veya hatalı) yapılacaklar
        }
    });
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="max-w-3xl mx-auto bg-white/70 backdrop-blur-sm p-8 rounded-2xl shadow-lg border border-gray-200">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">İzin Talebi Güncelle</h2>
            <form @submit.prevent="updateLeaveRequest" class="space-y-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">İzin Türü</label>
                    <select
                        id="type"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        :class="{'border-red-500': form.errors.type}"
                        v-model="form.type"
                        required
                    >
                        <option value="" disabled>İzin Tipi Seçiniz</option>
                        <option v-for="typeOption in leaveTypeOptions" :key="typeOption.value" :value="typeOption.value">
                            {{ typeOption.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">{{ form.errors.type }}</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                        <input type="date" id="start_date" v-model="form.start_date" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50"
                               :class="{'border-red-500': form.errors.start_date}">
                        <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                        <input type="date" id="end_date" v-model="form.end_date" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50"
                               :class="{'border-red-500': form.errors.end_date}">
                        <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                    </div>
                </div>

                <!-- days_count gösterimi -->
                <div v-if="form.start_date && form.end_date && form.days_count > 0"
                     class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-md text-blue-700 text-sm">
                    <span class="font-medium">Hesaplanan İzin Günü:</span>
                    <span class="font-bold text-lg">{{ form.days_count }} gün</span>
                </div>
                <div v-if="form.errors.days_count" class="text-red-500 text-xs mt-1">{{ form.errors.days_count }}</div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Sebep</label>
                    <textarea id="reason" v-model="form.reason" rows="4"
                              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50"
                              :class="{'border-red-500': form.errors.reason}"></textarea>
                    <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">{{ form.errors.reason }}</div>
                </div>
                <div class="flex justify-end items-center">
                    <Link class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-medium text-sm hover:bg-gray-300 transition-colors duration-200 shadow-sm mx-4"
                          :href="route('leaves.index')">
                        Geri dön
                    </Link>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200"
                            :disabled="form.processing">
                        Talebi Güncelle
                    </button>
                </div>
            </form>
        </div>
    </UserLayout>
</template>
