<?php

namespace App\Http\Resources;

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
            'cancellation_reason' => $this->cancellation_reason,
            'items' => $this->transformItems(),
        ];
    }

    protected function transformItems()
    {
        $items = [];
        foreach ($this->powerlevelItems as $item) {
            $items[] = [
                'game' => new BasicGameResource($item->game),
                'skill' => [
                    'id' => $item->skill_id,
                    'name' => $item->skill->name ?? null,
                ],
                'boost_method' => [
                    'id' => $item->boot_method_id,
                    'name' => $item->bootMethod->name ?? null,
                ],
                'current_level' => $item->current_level,
                'desired_level' => $item->desired_level,
                'price' => $item->price,
            ];
        }
        return $items;
    }
}
