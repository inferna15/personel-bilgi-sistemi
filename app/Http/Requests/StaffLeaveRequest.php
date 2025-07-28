<?php

namespace App\Http\Requests;

use App\Enum\LeaveStatus; // Enum'ları kullanıyorsanız import edin
use App\Enum\LeaveType;   // Enum'ları kullanıyorsanız import edin
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum; // Enum kuralını kullanıyorsanız import edin
use Illuminate\Validation\Validator;

class StaffLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Bu isteği yapmaya yetkili olup olmadığını belirler.
     * Genellikle, kullanıcının kendi izin talebini yönetme yetkisi kontrol edilir.
     */
    public function authorize(): bool
    {
        // Personelin sadece kendi izin taleplerini yönetmesine izin veriyorsanız:
        // Eğer bu bir güncelleme isteğiyse ve rota model binding ile 'leave' modeli geliyorsa:
        // if ($this->route('leave')) {
        //     return $this->route('leave')->user_id === auth()->id();
        // }
        // Yeni bir talep oluşturuyorsa, sadece oturum açmış olması yeterli olabilir.
        // return auth()->check();

        return true; // Geçici olarak true bırakıldı, gerçek uygulamada yetkilendirme ekleyin.
    }

    /**
     * Get the validation rules that apply to the request.
     * İstek için geçerli doğrulama kurallarını döndürür.
     */
    public function rules(): array
    {
        return [
            // 'type' alanı için Enum kuralı ekledik
            'type' => ['required', new Enum(LeaveType::class)],
            // Tarih kontrolü: başlangıç bitişten önce veya eşit olmalı
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            // Tarih kontrolü: bitiş başlangıçtan sonra veya eşit olmalı
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            // Gün sayısı: zorunlu, tam sayı ve en az 1 olmalı
            'days_count' => ['required', 'integer', 'min:1'],
            // Sebep: zorunlu, metin ve maksimum 1000 karakter
            'reason' => ['required', 'string', 'max:1000'],
            // 'status', 'reviewed_by', 'reviewed_at' alanları genellikle personel kendi talebini oluştururken gönderilmez,
            // ancak eğer formda varsa veya varsayılan değerler atanıyorsa kurallar burada kalabilir.
            // Personel kendi talebini oluştururken status genellikle 'Beklemede' olur ve reviewed_by/at null olur.
            // Eğer bu request sadece personel tarafından oluşturulan talepler içinse, bu alanlar 'nullable' olabilir.
            'status' => ['nullable', new Enum(LeaveStatus::class)], // Personel oluştururken null olabilir
            'reviewed_by' => ['nullable', 'exists:users,id'],
            'reviewed_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Doğrulama kuralları çalıştırılmadan önce veriyi hazırlar ve temizler.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Tarih alanlarının başındaki/sonundaki boşlukları temizle
            'start_date' => trim($this->input('start_date')),
            'end_date' => trim($this->input('end_date')),

            // Sebep metninin başındaki/sonundaki boşlukları temizle
            'reason' => trim($this->input('reason')),

            // Eğer user_id formdan gelmiyorsa ve auth()->id() ile doldurulacaksa,
            // bu request'in controller'da user_id'yi ayarlamasını bekleriz
            // veya burada varsayılan olarak auth()->id() kullanabiliriz.
            // 'user_id' => $this->input('user_id') ?? auth()->id(),
        ]);
    }

    /**
     * Configure the validator instance.
     * Validator örneğini yapılandırır ve özel çakışma kontrolünü ekler.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Eğer temel tarih validasyonları zaten başarısız olduysa, çakışma kontrolünü atla.
            if ($validator->errors()->hasAny(['start_date', 'end_date'])) {
                return;
            }

            // user_id'yi, eğer formda varsa oradan, yoksa oturum açmış kullanıcıdan al.
            // Bu request personel kendi iznini oluşturduğu için user_id genelde auth()->id() olacaktır.
            $userId = $this->user_id ?? auth()->id();
            $start = $this->input('start_date');
            $end = $this->input('end_date');

            // Eğer bu bir güncelleme isteğiyse (rota model binding ile 'leave' modeli geliyorsa), ID'sini al.
            $leaveId = $this->route('leave')?->id;

            // Veritabanında çakışan izin talebi olup olmadığını kontrol et.
            $hasConflict = \App\Models\Leave::where('user_id', $userId)
                ->when($leaveId, function ($query) use ($leaveId) {
                    $query->where('id', '!=', $leaveId); // Güncellenen kaydın kendisini dışarıda bırak
                })
                ->where(function ($query) use ($start, $end) {
                    // Çakışma koşulları:
                    // 1. Yeni izin aralığı, mevcut bir iznin başlangıcını veya bitişini içeriyor.
                    // 2. Mevcut bir izin aralığı, yeni izin aralığını tamamen kapsıyor.
                    $query->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->exists();

            if ($hasConflict) {
                // Çakışma varsa hata mesajı ekle.
                $validator->errors()->add('start_date', 'Bu tarihler arasında zaten başka bir izin talebiniz mevcut.');
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     * Tanımlanan doğrulama kuralları için hata mesajlarını döndürür.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // type
            'type.required' => 'İzin tipi zorunludur.',
            'type.Illuminate\Validation\Rules\Enum' => 'Geçersiz bir izin tipi seçildi.',

            // start_date
            'start_date.required' => 'Başlangıç tarihi zorunludur.',
            'start_date.date' => 'Başlangıç tarihi geçerli bir tarih formatında olmalıdır.',
            'start_date.before_or_equal' => 'Başlangıç tarihi, bitiş tarihinden sonra olamaz.',

            // end_date
            'end_date.required' => 'Bitiş tarihi zorunludur.',
            'end_date.date' => 'Bitiş tarihi geçerli bir tarih formatında olmalıdır.',
            'end_date.after_or_equal' => 'Bitiş tarihi, başlangıç tarihinden önce olamaz.',

            // days_count
            'days_count.required' => 'Gün sayısı zorunludur.',
            'days_count.integer' => 'Gün sayısı tam sayı olmalıdır.',
            'days_count.min' => 'Gün sayısı en az 1 olmalıdır.',

            // reason
            'reason.required' => 'İzin nedeni zorunludur.',
            'reason.string' => 'İzin nedeni geçerli bir metin olmalıdır.',
            'reason.max' => 'İzin nedeni en fazla 1000 karakter olmalıdır.',

            // status
            'status.Illuminate\Validation\Rules\Enum' => 'Geçersiz bir durum seçildi.',

            // reviewed_by
            'reviewed_by.exists' => 'İnceleyen kullanıcı geçersizdir.',

            // reviewed_at
            'reviewed_at.date' => 'İnceleme tarihi geçerli bir tarih formatında olmalıdır.',

            // withValidator içindeki özel mesaj
            'start_date.after' => 'Bu tarihler arasında zaten başka bir izin talebiniz mevcut.',
        ];
    }
}
