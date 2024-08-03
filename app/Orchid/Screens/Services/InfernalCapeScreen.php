<?php

namespace App\Orchid\Screens\Services;

use App\Models\ServiceOption;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Components\Cells\Boolean;
use Orchid\Screen\Fields\CheckBox;

class InfernalCapeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'options' => ServiceOption::where('service', 'infernal-cape')->whereNull('parent_id')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Infernal Cape';
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
                ->modal('createModal')
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
            Layout::table('options', [
                TD::make('id'),
                TD::make('name'),
                TD::make('has_quantity', 'Has Quantity')
                    ->usingComponent(Boolean::class),

                TD::make('actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (ServiceOption $option) {
                        return Group::make([
                            Link::make('Magics')
                                ->route('platform.services.infernal-cape.magics', $option->id)
                                ->icon('magic-wand'),

                            ModalToggle::make('Edit')
                                ->modal('editModal')
                                ->method('edit')
                                ->modalTitle('Edit build' . $option->name)
                                ->asyncParameters([
                                    'option' => $option->id,
                                ])
                                ->icon('pencil'),

                            Button::make('Delete')
                                ->confirm('After deleting, the build will be gone forever.')
                                ->method('delete', ['option' => $option->id])
                                ->icon('trash'),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('createModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Enter the name of the build'),

                    CheckBox::make('has_quantity')
                        ->sendTrueOrFalse()
                        ->title('Has Quantity')
                ]),
            ])->title('Create build')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Enter the name of the build'),

                    CheckBox::make('has_quantity')
                        ->sendTrueOrFalse()
                        ->title('Has Quantity')
                ]),
            ])->async('asyncEdit')
                ->applyButton('Update'),
        ];
    }

    public function asyncEdit(ServiceOption $option)
    {
        return [
            'name' => $option->name,
            'has_quantity' => $option->has_quantity
        ];
    }

    public function edit(ServiceOption $option, Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string',
            'has_quantity' => 'required|boolean'
        ]);

        $option->update([
            'name' => $request->get('name'),
            'has_quantity' => $request->get('has_quantity'),
        ]);

        Toast::success('Infernal Cape type updated successfully.');
    }

    public function delete(ServiceOption $option)
    {
        $option->delete();

        Toast::success('Infernal Cape type deleted successfully.');
    }

    public function create(Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string',
            'has_quantity' => 'required|boolean'
        ]);

        ServiceOption::create([
            'service' => 'infernal-cape',
            'name' => $request->get('name'),
            'has_quantity' => $request->get('has_quantity'),
        ]);

        Toast::success('Infernal Cape type created successfully.');
    }
}
