<?php

namespace App\Http\Requests;

use App\Enum\LeaveStatus;
use App\Enum\LeaveType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class UpdateLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Yetkilendirme mantığınızı buraya yazın.
        // Örneğin, sadece kendi izin talebini güncelleyebilir veya adminler tüm izinleri güncelleyebilir.
        // Eğer route model binding ile 'leave' modeli geliyorsa:
        // return $this->user()->can('update', $this->route('leave'));
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
            // user_id burada genellikle route model binding ile geldiği için required olmaz,
            // ancak eğer formdan da geliyorsa ve güncellenecekse eklenmeliydi.
            // Mevcut yapıda user_id'nin Leave modelinden alındığı varsayılıyor.

            'type' => ['required', new Enum(LeaveType::class)],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'], // 'before' yerine 'before_or_equal'
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],   // 'after' yerine 'after_or_equal'
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
            if ($validator->errors()->hasAny(['start_date', 'end_date'])) {
                return;
            }

            // user_id'yi route model binding ile gelen leave objesinden alıyoruz
            // Çünkü bu bir UpdateLeaveRequest ve Leave modeli zaten yüklenmiş olmalı.
            $userId = $this->route('leave')->user_id;
            $start = $this->input('start_date');
            $end = $this->input('end_date');

            // Güncellenen izin kaydının ID'sini alıyoruz
            $leaveId = $this->route('leave')->id;

            $hasConflict = \App\Models\Leave::where('user_id', $userId)
                ->where('id', '!=', $leaveId) // Kendisini dışarıda bırak
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
            'status.required' => 'Durum alanı zorunludur.',
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
