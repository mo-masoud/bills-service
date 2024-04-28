<?php

namespace App\Orchid\Screens\Services;

use App\Models\ServiceOption;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FireCapeScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'options' => ServiceOption::where('service', 'fire-cape')->whereNull('parent_id')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Services - Fire Cape';
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

                TD::make('actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (ServiceOption $option) {
                        return Group::make([
                            Link::make('Builds')
                                ->route('platform.services.fire-cape.builds', $option->id)
                                ->icon('dollar'),

                            ModalToggle::make('Edit')
                                ->modal('editModal')
                                ->method('edit')
                                ->modalTitle('Edit Type' . $option->name)
                                ->asyncParameters([
                                    'option' => $option->id,
                                ])
                                ->icon('pencil'),

                            Button::make('Delete')
                                ->confirm('After deleting, the type will be gone forever.')
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
                        ->placeholder('Enter the name of the type'),
                ]),
            ])->title('Create Type')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Enter the name of the type'),
                ]),
            ])->async('asyncEdit')
                ->applyButton('Update'),
        ];
    }

    public function asyncEdit(ServiceOption $option)
    {
        return [
            'name' => $option->name,
        ];
    }

    public function edit(ServiceOption $option, Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string',
        ]);

        $option->update([
            'name' => $request->get('name'),
        ]);

        Toast::success('Fire Cape type updated successfully.');
    }

    public function delete(ServiceOption $option)
    {
        $option->delete();

        Toast::success('Fire Cape type deleted successfully.');
    }

    public function create(Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string',
        ]);

        ServiceOption::create([
            'service' => 'fire-cape',
            'name' => $request->get('name'),
        ]);

        Toast::success('Fire Cape type created successfully.');
    }
}
