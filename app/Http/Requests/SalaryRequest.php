<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Rule sınıfını import ediyoruz

class SalaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Yetkilendirme mantığınızı buraya yazın.
        // Örneğin, sadece kimliği doğrulanmış kullanıcılar veya belirli bir role sahip olanlar.
        return true; // Örneğin, oturum açmış kullanıcılar izinli.
        // return auth()->user()->can('manage salaries'); // Daha spesifik bir yetkilendirme
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Eğer bu bir 'güncelleme' isteğiyse (yani route'da bir 'salary' modeli varsa), o maaş kaydının ID'sini al.
        // Aksi takdirde (yani bir 'kayıt' isteğiyse), null olarak bırak.
        $salaryId = $this->route('salary') ? $this->route('salary')->id : null;

        return [
            'user_id' => [
                'required',
                'exists:users,id',
                // user_id ve pay_date kombinasyonunun benzersizliğini kontrol et.
                // Güncelleme yapılıyorsa, mevcut maaş kaydını hariç tut.
                Rule::unique('salaries')->where(function ($query) {
                    return $query->where('user_id', $this->user_id)
                        ->where('pay_date', $this->pay_date);
                })->ignore($salaryId), // <-- Burası hem store hem update için ayarlandı
            ],
            'pay_date' => ['required', 'date'],
            'net_salary' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'gross_salary' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'payroll_file' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
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
            'pay_date' => trim($this->input('pay_date')),

            // Notlar alanını temizle
            'notes' => trim($this->input('notes')),

            // Sayısal alanlar için trim yapmak isteğe bağlıdır, genellikle otomatik dönüşürler.
            // Ancak string olarak gelip boşluk içeriyorsa sorun yaratabilir.
            // 'net_salary' => trim($this->input('net_salary')),
            // 'gross_salary' => trim($this->input('gross_salary')),
        ]);
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
            'user_id.required' => 'Personel seçimi zorunludur.',
            'user_id.exists' => 'Seçilen personel geçersizdir.',
            'user_id.unique' => 'Bu personel için belirtilen ödeme tarihinde zaten bir maaş kaydı bulunmaktadır.',

            // pay_date
            'pay_date.required' => 'Ödeme tarihi zorunludur.',
            'pay_date.date' => 'Ödeme tarihi geçerli bir tarih formatında olmalıdır.',

            // net_salary
            'net_salary.required' => 'Net maaş alanı zorunludur.',
            'net_salary.numeric' => 'Net maaş sayısal bir değer olmalıdır.',
            'net_salary.min' => 'Net maaş 0 veya daha büyük bir değer olmalıdır.',
            'net_salary.decimal' => 'Net maaş en fazla 2 ondalık basamak içermelidir.',

            // gross_salary
            'gross_salary.numeric' => 'Brüt maaş sayısal bir değer olmalıdır.',
            'gross_salary.min' => 'Brüt maaş 0 veya daha büyük bir değer olmalıdır.',
            'gross_salary.decimal' => 'Brüt maaş en fazla 2 ondalık basamak içermelidir.',

            // payroll_file
            'payroll_file.file' => 'Maaş bordrosu geçerli bir dosya olmalıdır.',
            'payroll_file.mimes' => 'Maaş bordrosu PDF formatında olmalıdır.',
            'payroll_file.max' => 'Maaş bordrosu en fazla 2 MB boyutunda olabilir.',

            // notes
            'notes.string' => 'Notlar geçerli bir metin olmalıdır.',
            'notes.max' => 'Notlar en fazla 500 karakter olmalıdır.',
        ];
    }
}
