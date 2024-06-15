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
            'items.*.type' => 'required|string|in:skill,quest,service',
            'items.*.skill_id' => 'required_if:items.*.type,==,skill|numeric|exists:skills,id',
            'items.*.min_level' => 'required_if:items.*.type,==,skill|numeric|min:1',
            'items.*.max_level' => 'required_if:items.*.type,==,skill|numeric|min:1',
            'items.*.boost_method_id' => 'nullable:numeric|exists:boot_methods,id',
            'items.*.quest_id' => 'required_if:items.*.type,==,quest|numeric|exists:quests,id',
            'items.*.service_id' => 'required_if:items.*.type,==,service|numeric|exists:service_options,id',
            'items.*.checkboxes' => 'nullable|array',
        ];
    }
}
