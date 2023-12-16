<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.game_id' => 'required|numeric|exists:games,id',
            'items.*.skill_id' => 'required|numeric|exists:skills,id',
            'items.*.current_level' => 'required|numeric|min:1',
            'items.*.desired_level' => 'required|numeric|min:1',
            'items.*.boost_method_id' => 'required|numeric|exists:boot_methods,id',
        ];
    }
}
