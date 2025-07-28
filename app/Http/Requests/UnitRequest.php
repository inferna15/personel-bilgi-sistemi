<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitId = $this->route('unit') ? $this->route('unit')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')->ignore($unitId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => ucwords(strtolower(trim($this->input('name')))),
            'description' => trim($this->input('description')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Birim adı alanı zorunludur.',
            'name.string' => 'Birim adı geçerli bir metin olmalıdır.',
            'name.max' => 'Birim adı en fazla 255 karakter olmalıdır.',
            'name.unique' => 'Bu birim adı zaten mevcut.',

            'description.string' => 'Açıklama geçerli bir metin olmalıdır.',
            'description.max' => 'Açıklama en fazla 255 karakter olmalıdır.',
        ];
    }
}
