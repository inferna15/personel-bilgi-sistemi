<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';

// Prop tiplerini tanımla
interface LeaveData {
    id: number;
    user: string | null; // Kullanıcı adı artık doğrudan string olarak geliyor
    type: string; // Enum değeri (string olarak gelir)
    start_date: string; // GG.AA.YYYY formatında string
    end_date: string; // GG.AA.YYYY formatında string
    days_count: number;
    reason: string | null;
    status: string; // Enum değeri (string olarak gelir)
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    reviews: { // LeaveIndexResource::collection($reviews) yapısı
        data: LeaveData[];
        links: { // root links (first, last, prev, next)
            first: string | null;
            last: string | null;
            prev: string | null;
            next: string | null;
        };
        meta: { // pagination meta information
            current_page: number;
            from: number | null;
            last_page: number;
            links: PaginationLink[]; // Sayfa numarası linkleri
            path: string;
            per_page: number;
            to: number | null;
            total: number;
        };
    };
    filters: {
        search: string | null;
    };
}

const props = defineProps<Props>();
console.log(props)

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('management.leaves.review'), { search: value }, { // leaves.review rotasına gönderiyoruz
        preserveState: true,
        replace: true,
    });
}, 300));

// GG.AA.YYYY formatındaki stringi Date objesine dönüştüren güvenli fonksiyon
const parseDateString = (dateString: string): Date | null => {
    if (!dateString) return null;
    const parts = dateString.split('.'); // GG.AA.YYYY
    if (parts.length === 3) {
        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1; // Ay 0-indekslidir
        const year = parseInt(parts[2], 10);
        const date = new Date(year, month, day);
        // Oluşturulan tarihin orijinal değerlerle eşleştiğini kontrol et (geçersiz tarihler için)
        if (date.getFullYear() === year && date.getMonth() === month && date.getDate() === day) {
            return date;
        }
    }
    return null; // Geçersiz format
};

// Tarih formatlama fonksiyonu (GG.AA.YYYY)
const formatDate = (dateString: string) => {
    const date = parseDateString(dateString);
    if (!date) return dateString; // Geçersiz tarihse orijinali döndür
    return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'numeric', day: 'numeric' });
};

// Onaylama işlemi
const approveLeave = (leaveId: number) => {
    if (confirm('Bu izin talebini onaylamak istediğinizden emin misiniz?')) {
        router.post(route('management.leaves.approve', leaveId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Başarılı olursa sayfa otomatik olarak yenilenir (redirect ile)
            },
            onError: (errors) => {
                alert('Onaylama sırasında bir hata oluştu: ' + Object.values(errors).join(', '));
            }
        });
    }
};

// Reddetme işlemi
const rejectLeave = (leaveId: number) => {
    if (confirm('Bu izin talebini reddetmek istediğinizden emin misiniz?')) {
        router.post(route('management.leaves.reject', leaveId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Başarılı olursa sayfa otomatik olarak yenilenir (redirect ile)
            },
            onError: (errors) => {
                alert('Reddetme sırasında bir hata oluştu: ' + Object.values(errors).join(', '));
            }
        });
    }
};

// Sayfalama butonları için metodlar
const goToPage = (url: string | null) => {
    if (url) {
        router.get(url, { search: search.value }, {
            preserveState: true,
            replace: true,
        });
    }
};
</script>

<template>
    <Head title="İzin Taleplerini İncele" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">İzin Taleplerini İncele</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Beklemedeki İzin Talepleri</h3>
                            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                                <TextInput
                                    type="text"
                                    v-model="search"
                                    placeholder="İzinlerde ara..."
                                    class="w-full md:w-auto"
                                />
                                <!-- Geri Dön Butonu -->
                                <Link :href="route('management.leaves.index')"
                                      class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Geri Dön
                                </Link>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İzin Tipi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlangıç</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gün Sayısı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talep Nedeni</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="props.reviews.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Beklemede hiç izin talebi bulunamadı.
                                    </td>
                                </tr>
                                <tr v-for="leave in props.reviews.data" :key="leave.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ leave.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <!-- BURADA GÜNCELLEME YAPILDI -->
                                        {{ leave.user || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ leave.type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <!-- BURADA GÜNCELLEME YAPILDI -->
                                        {{ formatDate(leave.start_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <!-- BURADA GÜNCELLEME YAPILDI -->
                                        {{ formatDate(leave.end_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ leave.days_count }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ leave.reason || '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="approveLeave(leave.id)"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                                            Onayla
                                        </button>
                                        <button @click="rejectLeave(leave.id)"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Reddet
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Sayfalama Navigasyonu -->
                        <div v-if="props.reviews.meta.links.length > 3" class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Button -->
                                <Link
                                    :href="props.reviews.links.prev || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.reviews.links.prev ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.reviews.links.prev" >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.56 10l3.23 3.71a.75.75 0 11-1.06 1.06l-3.75-4.25a.75.75 0 010-1.06l3.75-4.25a.75.75 0 011.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>

                                <!-- Page Number Links -->
                                <Link
                                    v-for="(link, key) in props.reviews.meta.links.slice(1, -1)" :key="key"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    :disabled="!link.url"
                                />

                                <!-- Next Button -->
                                <Link
                                    :href="props.reviews.links.next || '#'" :class="[
                                        'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50',
                                        !props.reviews.links.next ? 'opacity-50 cursor-not-allowed' : '' ]"
                                    :disabled="!props.reviews.links.next" >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.44 10 7.21 6.29a.75.75 0 111.06-1.06l3.75 4.25a.75.75 0 010 1.06l-3.75 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                                    </svg>
                                </Link>
                            </nav>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            {{ props.reviews.meta.from || 0 }}-{{ props.reviews.meta.to || 0 }} arası gösteriliyor, toplam {{ props.reviews.meta.total }} izin talebi.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
