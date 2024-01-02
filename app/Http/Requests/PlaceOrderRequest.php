<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'coupon' => 'nullable|string',
            'items.*.type' => 'required|string|in:powerlevel,quest,service',
            'items.*.game_id' => 'required|numeric|exists:games,id',
            'items.*.skill_id' => 'required_if:items.*.type,==,powerlevel|numeric|exists:skills,id',
            'items.*.current_level' => 'required_if:items.*.type,==,powerlevel|numeric|min:1',
            'items.*.desired_level' => 'required_if:items.*.type,==,powerlevel|numeric|min:1',
            'items.*.boost_method_id' => 'required_if:items.*.type,==,powerlevel|numeric|exists:boot_methods,id',
            'items.*.quest_id' => 'required_if:items.*.type,==,quest|numeric|exists:quests,id',
            'items.*.service_id' => 'required_if:items.*.type,==,service|numeric|exists:services,id',
        ];
    }
}
