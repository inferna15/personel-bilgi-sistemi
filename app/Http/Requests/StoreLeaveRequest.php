<?php

namespace App\Http\Requests;

use App\Enum\LeaveStatus;
use App\Enum\LeaveType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator; // Validator sınıfını kullanıyorsak import edelim

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Yetkilendirme mantığınızı buraya yazın.
        // Örneğin, sadece kimliği doğrulanmış kullanıcılar izin talebi oluşturabilir.
        // return auth()->check();
        return true; // Geçici olarak true bırakıldı
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', new Enum(LeaveType::class)],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'], // 'before' yerine 'before_or_equal' daha mantıklı olabilir
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],   // 'after' yerine 'after_or_equal' daha mantıklı olabilir
            'days_count' => ['required', 'integer', 'min:1'], // Gün sayısı en az 1 olmalı
            'reason' => ['required', 'string', 'max:1000'], // Sebep için max uzunluk ve string kuralı ekledik
            'status' => ['required', new Enum(LeaveStatus::class)],
            'reviewed_by' => ['nullable', 'exists:users,id'],
            'reviewed_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Bu metod, doğrulama kuralları çalıştırılmadan hemen önce çalışır.
     * Gelen veriyi temizlemek ve standartlaştırmak için idealdir.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Tarih alanlarını temizle (boşlukları sil)
            'start_date' => trim($this->input('start_date')),
            'end_date' => trim($this->input('end_date')),

            // Sebep alanını temizle
            'reason' => trim($this->input('reason')),

            // Eğer user_id null gelebiliyorsa ve auth()->id() ile doldurulacaksa
            // 'user_id' => $this->input('user_id') ?? auth()->id(),
            // Bu Form Request StoreLeaveRequest olduğu için user_id'nin dışarıdan gelmesi beklenir.
            // Eğer kullanıcı kendi adına talep oluşturuyorsa, controller'da user_id'yi auth()->id() olarak ayarlamak daha güvenli olabilir.
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Eğer validasyon zaten başarısız olduysa, bu kontrolü atlayabiliriz
            if ($validator->errors()->hasAny(['start_date', 'end_date', 'user_id'])) {
                return;
            }

            $userId = $this->user_id; // prepareForValidation'dan sonra temizlenmiş user_id
            $start = $this->input('start_date');
            $end = $this->input('end_date');

            // Eğer bu bir güncelleme isteğiyse, mevcut izin kaydının ID'sini al.
            // StoreLeaveRequest olduğu için genellikle null olacaktır, ancak UpdateLeaveRequest'te kullanılır.
            $leaveId = $this->route('leave')?->id;

            $hasConflict = \App\Models\Leave::where('user_id', $userId)
                ->when($leaveId, function ($query) use ($leaveId) {
                    $query->where('id', '!=', $leaveId);
                })
                ->where(function ($query) use ($start, $end) {
                    // Çakışma kontrolü:
                    // 1. Yeni izin başlangıcı mevcut izin aralığında
                    // 2. Yeni izin bitişi mevcut izin aralığında
                    // 3. Mevcut izin yeni izin aralığını tamamen kapsıyor
                    $query->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->exists();

            if ($hasConflict) {
                $validator->errors()->add('start_date', 'Bu tarihler arasında zaten başka bir izin talebiniz mevcut.');
                // İsteğe bağlı: end_date'e de hata ekleyebilirsiniz
                // $validator->errors()->add('end_date', 'Bu tarihler arasında zaten başka bir izin talebiniz mevcut.');
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // user_id
            'user_id.required' => 'Kullanıcı ID\'si zorunludur.',
            'user_id.exists' => 'Geçersiz bir kullanıcı seçildi.',

            // type
            'type.required' => 'İzin tipi zorunludur.',
            'type.Illuminate\Validation\Rules\Enum' => 'Geçersiz bir izin tipi seçildi.', // Enum kuralı için varsayılan mesaj
            // veya daha spesifik: 'type.App\Enum\LeaveType' => 'Geçersiz bir izin tipi seçildi.',

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
            'status.required' => 'Durum alanı zorunludur.',
            'status.Illuminate\Validation\Rules\Enum' => 'Geçersiz bir durum seçildi.',

            // reviewed_by
            'reviewed_by.exists' => 'İnceleyen kullanıcı geçersizdir.',

            // reviewed_at
            'reviewed_at.date' => 'İnceleme tarihi geçerli bir tarih formatında olmalıdır.',

            // withValidator içindeki özel mesaj
            'start_date.after' => 'Bu tarihler arasında zaten başka bir izin talebiniz mevcut.', // withValidator hatası için de bir mesaj
        ];
    }
}
