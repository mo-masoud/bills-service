<?php

namespace App\Http\Resources;

use App\Models\ServiceOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_price' => $this->original_price,
            'discount_price' => $this->discount_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'cancellation_reason' => $this->cancellation_reason,
            'items' => $this->transformItems(),
        ];
    }

    protected function transformItems(): array
    {
        $items = [];
        foreach ($this->skillItems as $item) {
            $items[] = [
                'skill' => [
                    'id' => $item->skill_id,
                    'name' => $item->skill->name ?? null,
                ],
                'boost_method' => [
                    'id' => $item->boostMethod->id ?? null,
                    'name' => $item->boostMethod->name ?? null,
                ],
                'min_level' => $item->min_level,
                'max_level' => $item->max_level,
                'price' => $item->price,
                'express' => (bool) $item->express,
                'type' => 'skill'
            ];
        }

        foreach ($this->questItems as $item) {
            $items[] = [
                'quest' => [
                    'id' => $item->quest_id,
                    'name' => $item->quest->name ?? null,
                    'difficulty' => $item->quest->difficulty ?? null,
                ],
                'price' => $item->price,
                'type' => 'quest'
            ];
        }

        foreach ($this->serviceItems as $item) {
            $items[] = [
                'service' => [
                    'id' => $item->service_option_id,
                    'name' => $item->service->name ?? null,
                    'service' => $item->service->service ?? null,
                    'parent' => $item->service->parent ? [
                        'id' => $item->service->parent->id,
                        'name' => $item->service->parent->name,
                        'price' => $item->service->parent->price,
                    ] : null,
                    'price' => $item->service->price ?? null,
                    'options' => $item->checkboxes ? ServiceOption::whereIn('id', $item->checkboxes)->get()->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                        ];
                    }) : [],
                ],
                'quantity' => $item->quantity,
                'unit_price' => $item->price / $item->quantity,
                'price' => $item->price,
                'type' => 'service'
            ];
        }
        return $items;
    }
}
