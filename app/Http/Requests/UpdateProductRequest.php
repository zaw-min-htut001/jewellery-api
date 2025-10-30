<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Variant;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'slug' => [
                'required',
                'string',
                Rule::unique('products', 'slug')->ignore($this->product->id),
            ],
            'variants' => 'nullable|array',
        ];

        // Validate each variant
        foreach ($this->input('variants', []) as $index => $variantData) {
            $variantId = $variantData['id'] ?? null;

            $rules["variants.$index.carat"] = 'required|string';
            $rules["variants.$index.metal_type"] = 'required|in:gold,white_gold,platinum';
            $rules["variants.$index.price"] = 'required|numeric|min:0';
            $rules["variants.$index.stock"] = 'required|integer|min:0';

            $rules["variants.$index.sku"] = [
                'required',
                'string',
                'distinct',
                Rule::unique('variants', 'sku')
                    ->ignore($variantId)
                    ->whereNull('deleted_at'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'variants.*.sku.unique' => 'This SKU is already used by another variant.',
            'variants.*.metal_type.in' => 'The metal type must be gold, white_gold, or platinum.',
        ];
    }
}
