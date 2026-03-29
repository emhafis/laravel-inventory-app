<?php

namespace App\Http\Requests;

use App\Support\BusinessContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && BusinessContext::id() !== null;
    }

    public function rules(): array
    {
        $bid = BusinessContext::id();

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('business_id', $bid),
            ],
            'unit_id' => [
                'required',
                Rule::exists('units', 'id')->where('business_id', $bid),
            ],
            'sku' => [
                'required',
                'string',
                'max:64',
                Rule::unique('products', 'sku')->where('business_id', $bid),
            ],
            'barcode' => ['nullable', 'string', 'max:128'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'min_stock_level' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
