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
use Orchid\Screen\Components\Cells\Boolean;
use Orchid\Screen\Fields\CheckBox;

class AchievementDiaryScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'options' => ServiceOption::where('service', 'achievement-diary')->whereNull('parent_id')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Achievement Diary | Areas';
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
                            Link::make('Difficulties')
                                ->route('platform.services.achievement-diary.difficulties', $option->id)
                                ->icon('dollar'),

                            ModalToggle::make('Edit')
                                ->modal('editModal')
                                ->method('edit')
                                ->asyncParameters([
                                    'service' => $option->id,
                                ])
                                ->icon('pencil'),

                            Button::make('Delete')
                                ->confirm('After deleting, the service will be gone forever.')
                                ->method('delete', ['service' => $option->id])
                                ->icon('trash'),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('createModal', [
                Layout::rows([
                    Input::make('name')->title('Name'),
                    CheckBox::make('has_quantity')
                        ->sendTrueOrFalse()
                        ->title('Has Quantity')
                ]),
            ])->title('Create')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name'),

                    CheckBox::make('has_quantity')
                        ->sendTrueOrFalse()
                        ->title('Has Quantity')
                ]),
            ])->async('asyncEdit')
                ->applyButton('Update'),
        ];
    }

    public function asyncEdit($service)
    {
        $option = ServiceOption::findOrFail($service);
        return [
            'name' => $option->name,
            'has_quantity' => $option->has_quantity,
        ];
    }

    public function delete(ServiceOption $service)
    {
        $service->delete();

        Toast::success('Service deleted successfully.');
    }

    public function edit(ServiceOption $service, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'has_quantity' => 'required|boolean',
        ]);

        $service->update([
            'name' => $request->get('name'),
            'has_quantity' => $request->get('has_quantity'),
        ]);

        Toast::success('Service updated successfully.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'has_quantity' => 'required|boolean',
        ]);

        ServiceOption::create([
            'service' => 'achievement-diary',
            'name' => $request->get('name'),
            'has_quantity' => $request->get('has_quantity'),
        ]);

        Toast::success('Service created successfully.');
    }
}
