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

class BossingScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'options' => ServiceOption::where('service', 'pvm-bossing')->whereNull('parent_id')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'PvM | Bossing Services';
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

                    Matrix::make('createOptions')
                        ->columns(['option_name', 'option_price',])
                        ->fields([
                            'option_name' => Input::make('createOptions.option_name'),
                            'option_price' => Input::make('createOptions.option_price')->type('number')->step('0.01'),
                        ]),
                ]),
            ])->title('Create')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name'),

                    Matrix::make('updateOptions')
                        ->columns(['option_name', 'option_price',])
                        ->fields([
                            'option_name' => Input::make('updateOptions.option_name'),
                            'Option Price' => Input::make('updateOption.option_price')->type('number')->step('0.01'),
                        ]),
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
            'updateOptions' => $option->children->map(function ($option) {
                return [
                    'option_name' => $option->name,
                    'option_price' => $option->price,
                ];
            })->toArray(),
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
            'updateOptions' => 'required|array',
            'updateOptions.*.option_name' => 'required|string',
            'updateOptions.*.option_price' => 'required|numeric',
        ]);

        $service->update([
            'name' => $request->get('name'),
        ]);

        $service->children()->where('type', 'radio')->delete();

        foreach ($request->get('updateOptions') as $option) {
            $sub = $service->children()->firstOrCreate([
                'service' => 'pvm-bossing',
                'name' => $option['option_name'],
            ]);

            $sub->update([
                'price' => $option['option_price'],
            ]);
        }

        Toast::success('Service updated successfully.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'createOptions' => 'required|array',
            'createOptions.*.option_name' => 'required|string',
            'createOptions.*.option_price' => 'required|numeric',
        ]);

        $service = ServiceOption::create([
            'service' => 'pvm-bossing',
            'name' => $request->get('name'),
        ]);

        foreach ($request->get('createOptions') as $option) {
            $sub = $service->children()->create([
                'service' => 'pvm-bossing',
                'name' => $option['option_name'],
                'price' => $option['option_price'],
            ]);
        }

        Toast::success('Service created successfully.');
    }
}
