<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffCardUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Bu isteği yapmaya yetkili olup olmadığını belirler.
     * Genellikle, kullanıcının kendi kartını güncelleme yetkisi kontrol edilir.
     */
    public function authorize(): bool
    {
        // Örneğin, sadece kimliği doğrulanmış kullanıcılar kendi kartlarını güncelleyebilir.
        return true;
        // Eğer bu request bir kart ID'si ile geliyorsa ve o kartın kullanıcısı auth()->id() ise:
        // $card = $this->route('card'); // route model binding ile gelen Card modeli
        // return $card && $card->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     * İstek için geçerli doğrulama kurallarını döndürür.
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10', 'max:500', 'decimal:0,2'], // decimal kuralı ekledik
        ];
    }

    /**
     * Prepare the data for validation.
     * Doğrulama kuralları çalıştırılmadan önce veriyi hazırlar ve temizler.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // 'amount' alanı: Başındaki/sonundaki boşlukları temizle
            // Sayısal bir değer olduğu için strtolower/ucwords'a gerek yok.
            'amount' => trim($this->input('amount')),
        ]);
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
            // amount
            'amount.required' => 'Yüklenecek miktar alanı zorunludur.',
            'amount.numeric' => 'Yüklenecek miktar sayısal bir değer olmalıdır.',
            'amount.min' => 'Yüklenecek miktar en az 10 TL olmalıdır.',
            'amount.max' => 'Yüklenecek miktar en fazla 500 TL olmalıdır.',
            'amount.decimal' => 'Yüklenecek miktar en fazla 2 ondalık basamak içermelidir.',
        ];
    }
}
