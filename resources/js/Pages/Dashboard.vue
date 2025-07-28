<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

// Chart.js ve vue-chartjs importları
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

// Chart.js'in gerekli bileşenlerini kaydediyoruz
ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    totalStaffCount: Number,
    totalUnitCount: Number,
    unitStaffDistribution: Array,
    staffOnLeaveToday: Array,
    pendingLeaveRequestsCount: Number,
    recentlyHiredStaff: Array,
    upcomingBirthdays: Array,
});

// Birim dağılımı için pasta grafiği verisi
const pieChartData = {
    labels: props.unitStaffDistribution.map(item => item.name),
    datasets: [
        {
            backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16', '#A2C8EE', '#FF6384', '#36A2EB', '#FFCE56', '#8e5ea2', '#3cba9f', '#e8c3b9', '#c45850'], // Daha fazla renk ekleyebilirsiniz
            data: props.unitStaffDistribution.map(item => item.value),
        }
    ]
};

// Pasta grafiği seçenekleri
const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right', // Legend'ı sağ tarafa al
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed !== null) {
                        label += context.parsed + ' Personel';
                    }
                    return label;
                }
            }
        }
    }
};

// Tarih formatlama için yardımcı fonksiyonlar
const formatDate = (dateString) => {
    if (!dateString) return 'Belirtilmemiş';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('tr-TR', options);
};

const formatMonthDay = (dateString) => {
    if (!dateString) return 'Belirtilmemiş';
    const options = { month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('tr-TR', options);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Gösterge Paneli</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Genel Bakış</h3>
                        <Link :href="route('home')"
                              class="w-full md:w-auto py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-center">
                            Personel Paneline Git
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg shadow-md flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-blue-700">Toplam Personel</h3>
                                <p class="text-4xl font-bold text-blue-800 mt-2">{{ totalStaffCount }}</p>
                            </div>
                            <i class="fas fa-users text-blue-400 text-5xl opacity-30"></i>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg shadow-md flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-green-700">Toplam Birim</h3>
                                <p class="text-4xl font-bold text-green-800 mt-2">{{ totalUnitCount }}</p>
                            </div>
                            <i class="fas fa-building text-green-400 text-5xl opacity-30"></i>
                        </div>

                        <div class="bg-yellow-50 p-6 rounded-lg shadow-md flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-yellow-700">Onay Bekleyen İzin</h3>
                                <p class="text-4xl font-bold text-yellow-800 mt-2">{{ pendingLeaveRequestsCount }}</p>
                            </div>
                            <i class="fas fa-hourglass-half text-yellow-400 text-5xl opacity-30"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Birimlere Göre Personel Dağılımı</h3>
                            <div v-if="unitStaffDistribution.length > 0" class="relative" style="height:300px;">
                                <Pie :data="pieChartData" :options="pieChartOptions" />
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">Henüz birim veya personel bulunmamaktadır.</p>
                        </div>

                        <div class="bg-red-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Bugün İzinli Olan Personel</h3>
                            <div v-if="staffOnLeaveToday.length > 0">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="leave in staffOnLeaveToday" :key="leave.id" class="py-3 flex items-center justify-between">
                                        <span class="font-medium text-gray-900">{{ leave.user.first_name }} {{ leave.user.last_name }}</span>
                                        <span class="text-sm text-red-600 px-2 py-1 bg-red-100 rounded-full">{{ leave.type }} İzni</span>
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">Bugün izinli personel bulunmamaktadır.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-purple-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Son Eklenen Personel</h3>
                            <div v-if="recentlyHiredStaff.length > 0">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="staff in recentlyHiredStaff" :key="staff.id" class="py-3">
                                        <p class="font-medium text-gray-900">{{ staff.first_name }} {{ staff.last_name }}</p>
                                        <p class="text-sm text-gray-600">{{ staff.email }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Katılma Tarihi: {{ formatDate(staff.created_at) }}</p>
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">Henüz yeni personel eklenmemiştir.</p>
                        </div>

                        <div class="bg-teal-50 p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">Yaklaşan Doğum Günleri</h3>
                            <div v-if="upcomingBirthdays.length > 0">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="person in upcomingBirthdays" :key="person.id" class="py-3 flex items-center justify-between">
                                        <p class="font-medium text-gray-900">{{ person.first_name }} {{ person.last_name }}</p>
                                        <span class="text-sm text-teal-700 px-2 py-1 bg-teal-100 rounded-full">{{ formatMonthDay(person.birth_date) }}</span>
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="text-gray-500 text-center py-4">Yaklaşan doğum günü bulunmamaktadır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
