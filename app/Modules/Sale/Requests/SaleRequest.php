<?php

namespace App\Modules\Sale\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale');

        if (is_object($saleId)) {
            $saleId = $saleId->id;
        }

        return self::rulesFor(is_numeric($saleId) ? (int) $saleId : null);
    }

    public static function rulesFor(?int $saleId = null): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'sale_number' => ['required', 'string', 'max:64', Rule::unique('sales', 'sale_number')->ignore($saleId)],
            'sale_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
