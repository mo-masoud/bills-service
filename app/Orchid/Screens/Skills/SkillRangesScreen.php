<?php

namespace App\Orchid\Screens\Skills;

use App\Models\Skill;
use App\Models\SkillRange;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SkillRangesScreen extends Screen
{
    public $skill;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Skill $skill): iterable
    {
        return [
            'skill' => $skill
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->skill?->name . ' Calculator for Level Range';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('create')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('skill.skillRanges', [
                TD::make('min', 'Min Level'),
                TD::make('max', 'Max Level'),
                TD::make('gp_xp', 'GP/XP'),

                TD::make('Actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (SkillRange $skillRange) {
                        return Group::make([
                            ModalToggle::make('Update')
                                ->modal('update')
                                ->method('update')
                                ->icon('pencil')
                                ->modalTitle('Update Level Range ' . $skillRange->name)
                                ->asyncParameters([
                                    'id' => $skillRange->id
                                ]),

                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the level range will be gone forever.')
                                ->method('delete', ['skillRange' => $skillRange->id]),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('create', Layout::rows([
                Input::make('skillRange.min')
                    ->title('Min Level')
                    ->type('number')
                    ->step('1')
                    ->required(),

                Input::make('skillRange.max')
                    ->title('Max Level')
                    ->type('number')
                    ->step('1')
                    ->required(),

                Input::make('skillRange.gp_xp')
                    ->title('GP/XP')
                    ->type('number')
                    ->step('1')
                    ->required(),
            ]))
                ->title('Create Level Range')
                ->applyButton('Create'),

            Layout::modal('update', Layout::rows([
                Input::make('skillRange.min')
                    ->title('Min Level')
                    ->type('number')
                    ->step('1')
                    ->required(),

                Input::make('skillRange.max')
                    ->title('Max Level')
                    ->type('number')
                    ->step('1')
                    ->required(),

                Input::make('skillRange.gp_xp')
                    ->title('GP/XP')
                    ->type('number')
                    ->step('1')
                    ->required(),
            ]))
                ->async('asyncGetSkillRange')
                ->applyButton('Update'),
        ];
    }

    public function asyncGetSkillRange(string $id): array
    {
        $skillRange = SkillRange::findOrFail($id);

        return [
            'skillRange' => $skillRange,
        ];
    }

    public function create(Request $request): void
    {
        $data = $request->validate([
            'skillRange.min' => 'required|numeric|min:1|lt:skillRange.max',
            'skillRange.max' => 'required|numeric|max:99|gt:skillRange.min',
            'skillRange.gp_xp' => 'required|numeric|min:0',
        ]);

        SkillRange::create([
            'min' => $data['skillRange']['min'],
            'max' => $data['skillRange']['max'],
            'gp_xp' => $data['skillRange']['gp_xp'],
            'skill_id' => $this->skill->id,
        ]);

        Toast::success('LevelRange created successfully.');
    }

    public function update(Request $request): void
    {
        $data = $request->validate([
            'skillRange.min' => 'required|numeric|min:1|lt:skillRange.max',
            'skillRange.max' => 'required|numeric|max:99|gt:skillRange.min',
            'skillRange.gp_xp' => 'required|numeric|min:0',
        ]);

        $skillRange = SkillRange::findOrFail($request->id);

        $skillRange->update($data['skillRange']);

        Toast::success('LevelRange updated successfully.');
    }

    public function delete(SkillRange $skillRange): void
    {
        $skillRange->delete();

        Toast::success('LevelRange deleted successfully.');
    }
}
