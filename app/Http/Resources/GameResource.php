<?php

namespace App\Http\Resources;

use App\Models\BootMethod;
use App\Models\Skill;
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
            'powerlevel' => [
                'image' => $this->powerlevel->image,
                'description' => $this->powerlevel->description,
                'levels' => $this->powerlevel->levels,
                'price' => $this->powerlevel->price,
            ],
            'skills' => $this->skills()
        ];
    }

    protected function skills(): array
    {
        $data = [];
        $skills = $this->resource->skills()->with('bootMethods')->whereNull('section_id')->get();
        $sections = $this->resource->sections()->with('skills', 'skills.bootMethods')->whereHas('skills')->get();

        foreach ($skills as $skill) {
            $data[] = [
                'id' => $skill->id,
                'name' => $skill->name,
                'is_section' => false,
                'price' => $skill->price,
                'boot_methods' => $skill->bootMethods->map(fn(BootMethod $method) => [
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
                'skills' => $section->skills->map(fn(Skill $skill) => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'price' => $skill->price,
                    'boot_methods' => $skill->bootMethods->map(fn(BootMethod $method) => [
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
