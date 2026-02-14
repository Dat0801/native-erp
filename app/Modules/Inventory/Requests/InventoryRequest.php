<?php

namespace App\Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return self::rulesFor();
    }

    public static function rulesFor(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'quantity_reserved' => ['required', 'integer', 'min:0'],
        ];
    }
}
