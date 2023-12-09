<?php

namespace App\Orchid\Screens\Skills;

use App\Models\Game;
use App\Models\Skill;
use App\Models\SkillSection;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SkillsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'skills' => Skill::latest()->get()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Games Skills';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('skillModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return Layout[]|string[]
     * @throws BindingResolutionException
     */
    public function layout(): iterable
    {
        return [
            Layout::table('skills', [
                TD::make('name'),
                TD::make('price'),
                TD::make('game')->render(fn ($skill) => Link::make($skill->game->name)->route('platform.games.view', $skill->game)),
                TD::make('section')->render(fn ($skill) => $skill->section ? Link::make($skill->section->name)->route('platform.games.edit', $skill->game) : '-'),
                TD::make('Actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (Skill $skill) {
                        return Group::make([
                            ModalToggle::make('Update')
                                ->modal('updateSkillModal')
                                ->method('update')
                                ->icon('pencil')
                                ->modalTitle('Update Skill ' . $skill->name)
                                ->asyncParameters([
                                    'id' => $skill->id
                                ]),
                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the skill will be gone forever.')
                                ->method('delete', ['skill' => $skill->id]),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),
            Layout::modal('skillModal', Layout::rows([
                Relation::make('skill.game_id')
                    ->required()
                    ->title('Game')
                    ->fromModel(Game::class, 'name'),
                Input::make('skill.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),
                Relation::make('skill.section_id')
                    ->title('Section')
                    ->fromModel(SkillSection::class, 'name'),

                Matrix::make('skill.prices')->columns(['min_level', 'max_level', 'price'])->fields([
                    'min_level' => Input::make('skill.prices.min_level')
                        ->type('number')
                        ->min(0)->required(),
                    'max_level' => Input::make('skill.prices.max_level')
                        ->type('number')
                        ->min(0)->required(),
                    'price' => Input::make('skill.prices.price')
                        ->type('number')
                        ->min(0)->required(),
                ])->title('Skill Prices'),

                Matrix::make('skill.boot_methods')->columns(['name', 'price'])->fields([
                    'name' => Input::make('skill.boot_methods.name')
                        ->type('text')
                        ->required(),
                    'price' => Input::make('skill.boot_methods.price')
                        ->type('number')
                        ->min(0)->required(),
                ])->title('Boot Methods')
            ]))
                ->title('Create Skill')
                ->applyButton('Create'),

            Layout::modal('updateSkillModal', Layout::rows([
                Relation::make('skill.game_id')
                    ->required()
                    ->title('Game')
                    ->fromModel(Game::class, 'name'),
                Input::make('skill.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),
                Relation::make('skill.section_id')
                    ->title('Section')
                    ->fromModel(SkillSection::class, 'name'),
                Matrix::make('skill.prices')->columns(['min_level', 'max_level', 'price'])->fields([
                    'min_level' => Input::make('skill.prices.min_level')
                        ->type('number')
                        ->min(0)->required(),
                    'max_level' => Input::make('skill.prices.max_level')
                        ->type('number')
                        ->min(0)->required(),
                    'price' => Input::make('skill.prices.price')
                        ->type('number')
                        ->step(0.1)
                        ->min(0)->required(),
                ])->title('Skill Prices'),

                Matrix::make('skill.bootMethods')->columns(['name', 'price'])->fields([
                    'name' => Input::make('skill.bootMethods.name')
                        ->type('text')
                        ->required(),
                    'price' => Input::make('skill.bootMethods.price')
                        ->type('number')
                        ->min(0)->required(),
                ])->title('Boot Methods')
            ]))->async('asyncLoadSkill')
                ->applyButton('Update Skill'),
        ];
    }

    public function asyncLoadSkill(string $id): array
    {
        $skill = Skill::with('bootMethods')->findOrFail($id);
        return [
            'skill' => $skill
        ];
    }

    public function update(Request $request): void
    {
        $data = $request->validate([
            'skill.game_id' => 'required|numeric|exists:games,id',
            'skill.name' => 'required|string|max:191',
            'skill.section_id' => 'nullable|numeric|exists:skill_sections,id',
            'skill.prices' => 'nullable|array',
            'skill.prices.*.min_level' => 'required|numeric|min:0',
            'skill.prices.*.max_level' => 'required|numeric|min:0',
            'skill.prices.*.price' => 'required|numeric|min:0',
            'skill.bootMethods' => 'nullable|array',
            'skill.bootMethods.*.name' => 'required|string',
            'skill.bootMethods.*.price' => 'required|numeric|min:0',
        ]);


        $skill = Skill::where('id', $request->id)->first();

        $skill->update($data['skill']);

        $skill->prices()->delete();

        foreach ($data['skill']['prices'] as $price) {
            $skill->prices()->updateOrCreate([
                'min_level' => $price['min_level'],
                'max_level' => $price['max_level'],
            ], [
                'min_level' => $price['min_level'],
                'max_level' => $price['max_level'],
                'price' => $price['price'],
            ]);
        }

        $bootMethods = [];
        foreach ($data['skill']['bootMethods'] as $bootMethod) {
            $bootMethods[] = $bootMethod['name'];
            $skill->bootMethods()->updateOrCreate(['name' => $bootMethod['name']], [
                'name' => $bootMethod['name'],
                'price' => $bootMethod['price'],
            ]);
        }

        $skill->bootMethods()->whereNotIn('name', $bootMethods)->delete();

        Toast::success("Skill was updated successfully.");
    }

    public function delete(Skill $skill): void
    {
        $skill->delete();

        Toast::success("Skill was deleted successfully.");
    }

    public function create(Request $request): void
    {
        $data = $request->validate([
            'skill.game_id' => 'required|numeric|exists:games,id',
            'skill.name' => 'required|string|max:191',
            'skill.section_id' => 'nullable|numeric|exists:skill_sections,id',
            'skill.prices' => 'nullable|array',
            'skill.prices.*.min_level' => 'required|numeric|min:0',
            'skill.prices.*.max_level' => 'required|numeric|min:0',
            'skill.prices.*.price' => 'required|numeric|min:0',
            'skill.boot_methods' => 'nullable|array',
            'skill.boot_methods.*.name' => 'required|string',
            'skill.boot_methods.*.price' => 'required|numeric|min:0',
        ]);


        $skill = Skill::create($data['skill']);

        foreach ($data['skill']['prices'] as $price) {
            $skill->prices()->create([
                'min_level' => $price['min_level'],
                'max_level' => $price['max_level'],
                'price' => $price['price'],
            ]);
        }

        foreach ($data['skill']['boot_methods'] as $bootMethod) {
            $skill->bootMethods()->create([
                'name' => $bootMethod['name'],
                'price' => $bootMethod['price'],
            ]);
        }

        Toast::success("Skill {$skill->name} was created successfully.");
    }
}
