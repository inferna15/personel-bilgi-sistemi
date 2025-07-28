<?php

namespace App\Http\Requests;

use App\Enum\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'identity_number' => ['required', 'string', 'digits:11', Rule::unique('users')->ignore($userId)],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date'],
            'phone' => ['required', 'string', 'digits:11'],
            'gender' => ['required', 'in:Erkek,Kadın'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => ucwords(strtolower(trim($this->input('first_name')))),
            'last_name' => ucwords(strtolower(trim($this->input('last_name')))),
            'email' => trim($this->input('email')),
            'identity_number' => trim($this->input('identity_number')),
            'address' => trim($this->input('address')),
            'phone' => trim($this->input('phone')),
            'position' => ucwords(strtolower(trim($this->input('position')))),
        ]);
    }

    public function messages(): array
    {
        return [
            // first_name
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.string' => 'Ad geçerli bir metin olmalıdır.',
            'first_name.max' => 'Ad en fazla 255 karakter olmalıdır.',

            // last_name
            'last_name.required' => 'Soyad alanı zorunludur.',
            'last_name.string' => 'Soyad geçerli bir metin olmalıdır.',
            'last_name.max' => 'Soyad en fazla 255 karakter olmalıdır.',

            // email
            'email.required' => 'E-posta alanı zorunludur.',
            'email.string' => 'E-posta geçerli bir metin olmalıdır.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi girin.',
            'email.max' => 'E-posta en fazla 255 karakter olmalıdır.',
            'email.unique' => 'Bu e-posta adresi zaten başka bir kullanıcı tarafından kullanılıyor.',

            // identity_number
            'identity_number.required' => 'Kimlik numarası alanı zorunludur.',
            'identity_number.string' => 'Kimlik numarası geçerli bir metin olmalıdır.',
            'identity_number.digits' => 'Kimlik numarası 11 haneli olmalıdır.',
            'identity_number.unique' => 'Bu kimlik numarası zaten kayıtlı.',

            // address
            'address.required' => 'Adres alanı zorunludur.',
            'address.string' => 'Adres geçerli bir metin olmalıdır.',
            'address.max' => 'Adres en fazla 500 karakter olmalıdır.',

            // birth_date
            'birth_date.required' => 'Doğum tarihi alanı zorunludur.',
            'birth_date.date' => 'Doğum tarihi geçerli bir tarih formatında olmalıdır.',

            // phone
            'phone.required' => 'Telefon numarası alanı zorunludur.',
            'phone.string' => 'Telefon numarası geçerli bir metin olmalıdır.',
            'phone.digits' => 'Telefon numarası 11 haneli olmalıdır.',

            // gender
            'gender.required' => 'Cinsiyet alanı zorunludur.',
            'gender.in' => 'Cinsiyet sadece "Erkek" veya "Kadın" olabilir.',

            // unit_id
            'unit_id.exists' => 'Seçilen birim geçersizdir.', // nullable olduğu için required mesajı yok

            // position
            'position.string' => 'Pozisyon geçerli bir metin olmalıdır.',
            'position.max' => 'Pozisyon en fazla 255 karakter olmalıdır.', // nullable olduğu için required mesajı yok

            // role
            'role.required' => 'Rol alanı zorunludur.',
            'role.string' => 'Rol geçerli bir metin olmalıdır.',
            'role.exists' => 'Seçilen rol geçersizdir.',

            // photo
            'photo.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'photo.max' => 'Fotoğraf boyutu en fazla 2 MB olabilir.', // nullable olduğu için required mesajı yok
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $role = $this->input('role');
            $user = auth()->user();

            if (!$user) {
                $validator->errors()->add('role', 'Yetkilendirme bulunamadı.');
                return;
            }

            if ($role === 'admin' && !$user->hasRole('admin')) {
                $validator->errors()->add('role', 'Bu rolü atama yetkiniz yok.');
            }

            if ($role === 'manager' && !$user->hasRole('admin')) {
                $validator->errors()->add('role', 'Bu rolü atama yetkiniz yok.');
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}
