<?php

namespace App\Http\Resources;

use App\Models\BootMethod;
use App\Models\Skill;
use App\Models\SkillPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'powerlevel' => $this->powerlevel ? [
                'image' => $this->powerlevel->image,
                'description' => $this->powerlevel->description,
                'levels' => $this->powerlevel->levels,
                'price' => $this->powerlevel->price,
            ] : null,
            'skills' => $this->skills()
        ];
    }

    protected function skills(): array
    {
        $data = [];
        $skills = $this->resource->skills()->with('bootMethods', 'prices')->whereNull('section_id')->get();
        $sections = $this->resource->sections()->with('skills', 'skills.bootMethods', 'skills.prices')->whereHas('skills')->get();

        foreach ($skills as $skill) {
            $data[] = [
                'id' => $skill->id,
                'name' => $skill->name,
                'is_section' => false,
                'prices' => $skill->prices->map(fn (SkillPrice $price) => [
                    'id' => $price->id,
                    'min_level' => $price->min_level,
                    'max_level' => $price->max_level,
                    'price' => $price->price,
                ]),
                'boot_methods' => $skill->bootMethods->map(fn (BootMethod $method) => [
                    'id' => $method->id,
                    'name' => $method->name,
                    'price' => $method->price,
                ]),
            ];
        }

        foreach ($sections as $section) {
            $data[] = [
                'id' => $section->id,
                'name' => $section->name,
                'is_section' => true,
                'skills' => $section->skills->map(fn (Skill $skill) => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'prices' => $skill->prices->map(fn (SkillPrice $price) => [
                        'id' => $price->id,
                        'min_level' => $price->min_level,
                        'max_level' => $price->max_level,
                        'price' => $price->price,
                    ]),
                    'boot_methods' => $skill->bootMethods->map(fn (BootMethod $method) => [
                        'id' => $method->id,
                        'name' => $method->name,
                        'price' => $method->price,
                    ]),
                ])
            ];
        }
        return $data;
    }
}
