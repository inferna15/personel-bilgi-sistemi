<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3'; // Inertia form yardımcı fonksiyonu
import UserLayout from '@/Layouts/UserLayout.vue';

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
    card: { // Sadece yüklenecek kartın bilgilerini props olarak alıyoruz
        id: number;
        serial_no: string;
        balance: number;
        issue_date: string;
        type: string;
    };
}>();

// Form verileri için Inertia'nın useForm'unu kullanıyoruz
const form = useForm({
    card_id: props.card.id, // Yüklenecek kartın ID'si
    amount: null as number | null,
    payment_method: 'creditCard', // Varsayılan ödeme yöntemi
});

const message = ref('');
const messageClass = ref('');

// Kart numarası input'una başlangıçta kartın seri numarasını doldurmak için
const initialCardNumber = ref(props.card.serial_no);

onMounted(() => {
    // Sayfa yüklendiğinde kart numarasını otomatik doldur
    initialCardNumber.value = props.card.serial_no;
});


const submitTopUp = () => {
    message.value = 'Bakiye yükleme işlemi başlatılıyor... Lütfen bekleyin.';
    messageClass.value = 'bg-blue-100 text-blue-800';

    // Inertia post request ile backend'e gönderim
    form.put(route('cards.update', props.card.id), { // Backend'deki rotayı kendi adlandırmana göre ayarla
        onSuccess: () => {
            message.value = `Tebrikler! ${form.amount} ₺ başarıyla kartınıza yükleme talebiniz alındı.`;
            messageClass.value = 'bg-green-100 text-green-800';
            form.reset('amount'); // Sadece tutar alanını sıfırla
            // Opsiyonel: Yükleme başarılı olduğunda kart bakiyesini güncellemek için sayfayı yeniden yükle veya bir olay gönder
            // Örneğin: Inertia.reload({ only: ['card'] });
        },
        onError: (errors) => {
            console.error('Bakiye yükleme hatası:', errors);
            if (errors && Object.keys(errors).length > 0) {
                // Hataları daha spesifik göstermek için
                message.value = Object.values(errors).join(', ');
            } else {
                message.value = 'Bakiye yüklenirken bir hata oluştu. Lütfen tekrar deneyin.';
            }
            messageClass.value = 'bg-red-100 text-red-800';
        },
        onFinish: () => {
            // Yükleme işlemi tamamlandığında (başarılı veya hatalı fark etmez)
            // İstersen burada ek işlemler yapabilirsin
        }
    });
};
</script>

<template>
    <UserLayout :user="user" :unit="unit">
        <div class="container mx-auto p-6 bg-white/70 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-200 max-w-lg mt-10">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-teal-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-800">Bakiye Yükle</h2>
            </div>

            <p class="text-lg font-semibold text-gray-700 mb-4">
                Kart Numarası: <span class="font-mono text-gray-900">{{ card.serial_no }}</span>
            </p>
            <p class="text-lg font-semibold text-gray-700 mb-6">
                Mevcut Bakiye: <span class="text-emerald-600">{{ card.balance }}₺</span>
            </p>

            <form @submit.prevent="submitTopUp">
                <div class="mb-4">
                    <label for="cardNumberDisplay" class="block text-sm font-medium text-gray-700 mb-1">Kart Numarası (Otomatik Dolu)</label>
                    <input
                        type="text"
                        id="cardNumberDisplay"
                        :value="initialCardNumber"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed font-mono text-gray-800"
                        readonly
                        aria-label="Kart Numarası Görüntüleme"
                    />
                    <p class="text-xs text-gray-500 mt-1">Bu kart numarasına bakiye yüklenecektir.</p>
                </div>

                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Yüklenecek Tutar (₺)</label>
                    <input
                        type="number"
                        id="amount"
                        v-model.number="form.amount"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-bold text-emerald-600 bg-gray-50"
                        placeholder="Minimum 10 ₺"
                        min="10"
                        required
                        aria-label="Yüklenecek Tutar"
                    />
                    <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</p>
                </div>

                <div class="mb-6 text-sm text-gray-600">
                    <p class="font-semibold mb-2">Ödeme Yöntemi Seçin:</p>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" v-model="form.payment_method" value="creditCard" class="form-radio text-blue-600 h-4 w-4" required>
                            <span class="ml-3 font-medium text-gray-800">Kredi/Banka Kartı</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" v-model="form.payment_method" value="eft" class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-3 font-medium text-gray-800">EFT/Havale</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" v-model="form.payment_method" value="mobilePayment" class="form-radio text-blue-600 h-4 w-4">
                            <span class="ml-3 font-medium text-gray-800">Mobil Ödeme</span>
                        </label>
                    </div>
                    <p v-if="form.errors.payment_method" class="text-red-500 text-xs mt-1">{{ form.errors.payment_method }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all duration-300 transform hover:scale-105"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                >
                    {{ form.processing ? 'Yükleniyor...' : 'Bakiyeyi Yükle' }}
                </button>
            </form>

            <div v-if="message" :class="messageClass" class="mt-4 p-3 rounded-lg text-sm text-center">
                {{ message }}
            </div>
        </div>
    </UserLayout>
</template>
