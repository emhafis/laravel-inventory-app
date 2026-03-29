<?php

namespace App\Http\Requests;

use App\Enums\StockTransactionType;
use App\Support\BusinessContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStockTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && BusinessContext::id() !== null;
    }

    public function rules(): array
    {
        $bid = BusinessContext::id();

        return [
            'type' => ['required', Rule::enum(StockTransactionType::class)],
            'occurred_on' => ['required', 'date'],
            'supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')->where('business_id', $bid),
            ],
            'customer_id' => [
                'nullable',
                Rule::exists('customers', 'id')->where('business_id', $bid),
            ],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where('business_id', $bid),
            ],
            'lines.*.quantity' => ['required', 'numeric'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('type');
            if (! $type || ! is_array($this->input('lines'))) {
                return;
            }

            foreach ($this->input('lines', []) as $i => $line) {
                $qty = $line['quantity'] ?? null;
                if ($qty === null || $qty === '') {
                    continue;
                }
                $qty = (float) $qty;

                if (in_array($type, [StockTransactionType::In->value, StockTransactionType::Out->value], true)) {
                    if ($qty <= 0) {
                        $validator->errors()->add("lines.$i.quantity", 'Jumlah harus lebih dari 0 untuk tipe masuk/keluar.');
                    }
                }

                if ($type === StockTransactionType::Adjustment->value) {
                    if ($qty == 0.0) {
                        $validator->errors()->add("lines.$i.quantity", 'Jumlah penyesuaian tidak boleh 0.');
                    }
                }

                if ($type === StockTransactionType::In->value && array_key_exists('unit_cost', $line) && $line['unit_cost'] !== null && $line['unit_cost'] !== '' && (float) $line['unit_cost'] < 0) {
                    $validator->errors()->add("lines.$i.unit_cost", 'Harga tidak boleh negatif.');
                }
            }
        });
    }
}
