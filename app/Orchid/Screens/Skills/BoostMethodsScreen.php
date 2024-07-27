<?php

namespace App\Orchid\Screens\Skills;

use App\Models\BootMethod;
use App\Models\Skill;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BoostMethodsScreen extends Screen
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
        return $this->skill?->name . ' Boost Methods';
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
            Layout::table('skill.bootMethods', [
                TD::make('id'),
                TD::make('name'),
                TD::make('price'),

                TD::make('Actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (BootMethod $bootMethod) {
                        return Group::make([
                            ModalToggle::make('Update')
                                ->modal('update')
                                ->method('update')
                                ->icon('pencil')
                                ->modalTitle('Update Boost Method ' . $bootMethod->name)
                                ->asyncParameters([
                                    'id' => $bootMethod->id
                                ]),

                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the boost method will be gone forever.')
                                ->method('delete', ['bootMethod' => $bootMethod->id]),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('create', Layout::rows([
                Input::make('boostMethod.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),

                Input::make('boostMethod.price')
                    ->title('Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
            ]))
                ->title('Create Boost Method')
                ->applyButton('Create'),

            Layout::modal('update', Layout::rows([
                Input::make('boostMethod.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),

                Input::make('boostMethod.price')
                    ->title('Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
            ]))
                ->async('asyncGetBoostMethod')
                ->applyButton('Update'),
        ];
    }

    public function asyncGetBoostMethod(string $id): array
    {
        $boostMethod = BootMethod::findOrFail($id);

        return [
            'boostMethod' => $boostMethod,
        ];
    }

    public function create(Request $request): void
    {
        $data = $request->validate([
            'boostMethod.name' => 'required|string',
            'boostMethod.price' => 'required|numeric|min:0',
        ]);

        BootMethod::create([
            'name' => $data['boostMethod']['name'],
            'price' => $data['boostMethod']['price'],
            'skill_id' => $this->skill->id,
        ]);

        Toast::success('Boost Method created successfully.');
    }

    public function update(Request $request): void
    {
        $data = $request->validate([
            'boostMethod.name' => 'required|string',
            'boostMethod.price' => 'required|numeric|min:0',
        ]);

        $boostMethod = BootMethod::findOrFail($request->id);

        $boostMethod->update($data['boostMethod']);

        Toast::success('Boost Method updated successfully.');
    }

    public function delete(BootMethod $bootMethod): void
    {
        $bootMethod->delete();

        Toast::success('Boost Method deleted successfully.');
    }
}
