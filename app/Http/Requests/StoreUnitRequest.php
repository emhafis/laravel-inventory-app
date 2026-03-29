<?php

namespace App\Http\Requests;

use App\Support\BusinessContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && BusinessContext::id() !== null;
    }

    public function rules(): array
    {
        $bid = BusinessContext::id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:32',
                Rule::unique('units', 'code')->where('business_id', $bid),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'code' => $this->filled('code') ? strtoupper($this->input('code')) : null,
        ]);
    }
}
