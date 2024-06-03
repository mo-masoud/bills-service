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

class RaidsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'options' => ServiceOption::where('service', 'raids')->whereNull('parent_id')->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Raids';
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
                                ->modalTitle('Edit Raid ' . $option->name)
                                ->asyncParameters([
                                    'raids' => $option->id,
                                ])
                                ->icon('pencil'),

                            Button::make('Delete')
                                ->confirm('After deleting, the raid will be gone forever.')
                                ->method('delete', ['raids' => $option->id])
                                ->icon('trash'),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('createModal', [
                Layout::rows([
                    Input::make('name')->title('Name'),

                    Matrix::make('createOptions')
                        ->columns(['option_name', 'option_price', 'missing_stats_price'])
                    ->fields([
                        'option_name' => Input::make('createOptions.option_name'),
                        'option_price' => Input::make('createOptions.option_price')->type('number')->step('0.01'),
                        'missing_stats_price' => Input::make('createOptions.missing_stats_price')->type('number')->step('0.01'),
                    ]),
                ]),
            ])->title('Create')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name'),

                    Matrix::make('updateOptions')
                        ->columns(['option_name', 'option_price', 'missing_stats_price'])
                        ->fields([
                            'option_name' => Input::make('updateOptions.option_name'),
                            'Option Price' => Input::make('updateOption.option_price')->type('number')->step('0.01'),
                            'missing_stats_price' => Input::make('updateOption.missing_stats_price')->type('number')->step('0.01'),
                        ]),
                ]),
            ])->async('asyncEdit')
                ->applyButton('Update'),
        ];
    }

    public function asyncEdit($raids)
    {
        $option = ServiceOption::findOrFail($raids);
        return [
            'name' => $option->name,
            'updateOptions' => $option->children->map(function ($option) {
                return [
                    'option_name' => $option->name,
                    'option_price' => $option->price,
                    'missing_stats_price' => $option->children->first()->price ?? null,
                ];
            })->toArray(),
        ];
    }

    public function delete(ServiceOption $raids)
    {
        $raids->delete();

        Toast::success('Raids deleted successfully.');
    }

    public function edit(ServiceOption $raids, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'updateOptions' => 'required|array',
            'updateOptions.*.option_name' => 'required|string',
            'updateOptions.*.option_price' => 'required|numeric',
            'updateOptions.*.missing_stats_price' => 'nullable|numeric',
        ]);

        $raids->update([
            'name' => $request->get('name'),
        ]);

        $raids->children()->where('type', 'radio')->delete();

        foreach ($request->get('updateOptions') as $option) {
            $sub = $raids->children()->firstOrCreate([
                'service' => 'raids',
                'name' => $option['option_name'],
            ]);

            $sub->update([
                'price' => $option['option_price'],
            ]);

            if (isset($option['missing_stats_price'])) {
                $sub->children()->firstOrCreate([
                    'price' => $option['missing_stats_price'],
                    'service' => 'raids',
                    'name' => 'wo/ 90+ Stats & All Prayer',
                    'type' => 'checkbox',
                ]);
            }
        }

        Toast::success('Raids updated successfully.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'createOptions' => 'required|array',
            'createOptions.*.option_name' => 'required|string',
            'createOptions.*.option_price' => 'required|numeric',
            'createOptions.*.missing_stats_price' => 'nullable|numeric',
        ]);

        $raids = ServiceOption::create([
            'service' => 'raids',
            'name' => $request->get('name'),
        ]);

        foreach ($request->get('createOptions') as $option) {
            $sub = $raids->children()->create([
                'service' => 'raids',
                'name' => $option['option_name'],
                'price' => $option['option_price'],
            ]);

            if (isset($option['missing_stats_price'])) {
                $sub->children()->create([
                    'price' => $option['missing_stats_price'],
                    'service' => 'raids',
                    'name' => 'wo/ 90+ Stats & All Prayer',
                    'type' => 'checkbox',
                ]);
            }
        }

        Toast::success('Raids created successfully.');
    }
}
