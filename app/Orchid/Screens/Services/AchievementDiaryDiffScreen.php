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

class AchievementDiaryDiffScreen extends Screen
{
    public $option;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(ServiceOption $option): iterable
    {
        $this->option = $option;

        return [
            'options' => $this->option->children,
        ];
    }
    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->option?->name . ' Difficulties';
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
                TD::make('price'),

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
                    Input::make('price')->title('Price')->type('number')->step('0.01'),
                    Input::make('express_price')->title('Express Price')->type('number')->step('0.01'),

                    Matrix::make('createOptions')
                        ->title('Options')
                        ->columns([
                            'name', 'price',
                        ])->fields([
                            'name' => Input::make('name'),
                            'price' => Input::make('price')->type('number')->step('0.01'),
                        ]),
                ]),
            ])->title('Create')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name'),
                    Input::make('price')->title('Price')->type('number')->step('0.01'),
                    Input::make('express_price')->title('Express Price')->type('number')->step('0.01'),

                    Matrix::make('updateOptions')
                        ->title('Options')
                        ->columns([
                            'name', 'price',
                        ])->fields([
                            'name' => Input::make('name'),
                            'price' => Input::make('price')->type('number')->step('0.01'),
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
            'price' => $option->price,
            'express_price' => $option->children->where('name', 'Express Service')->first()?->price,
            'updateOptions' => $option->children->where('name', '!=', 'Express Service')->map(function ($option) {
                return [
                    'name' => $option->name,
                    'price' => $option->price,
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
            'price' => 'required|numeric',
            'express_price' => 'nullable|numeric',
            'updateOptions' => 'nullable|array',
            'updateOptions.*.name' => 'required|string',
            'updateOptions.*.price' => 'required|numeric',
        ]);

        $service->update([
            'name' => $request->get('name'),
            'price' => $request->get('price'),
        ]);

        if ($request->has('createOptions')) {
            $service->children()->delete();

            foreach ($request->get('updateOptions') as $op) {
                $service->children()->updateOrCreate([
                    'name' => $op['name'],
                ], [
                    'price' => $op['price'],
                    'service' => 'achievement-diary',
                    'type' => 'checkbox'
                ]);
            }
        }

        if ($request->has('express_price')) {
            $service->children()->updateOrCreate([
                'name' => 'Express Service',
            ], [
                'price' => $request->get('express_price'),
                'service' => 'achievement-diary',
                'type' => 'checkbox'

            ]);
        }

        Toast::success('Service updated successfully.');
    }

    public function create(Request $request, ServiceOption $option)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'createOptions' => 'nullable|array',
            'createOptions.*.name' => 'required|string',
            'createOptions.*.price' => 'required|numeric',
        ]);

        $service = ServiceOption::create([
            'service' => 'achievement-diary',
            'name' => $request->get('name'),
            'price' => $request->get('price'),
            'parent_id' => $option->id,
        ]);

        if ($request->has('createOptions')) {
            foreach ($request->get('createOptions') as $op) {
                $service->children()->create([
                    'name' => $op['name'],
                    'price' => $op['price'],
                    'service' => 'achievement-diary',
                    'type' => 'checkbox'
                ]);
            }
        }

        if ($request->has('express_price')) {
            $service->children()->updateOrCreate([
                'name' => 'Express Service',
            ], [
                'price' => $request->get('express_price'),
                'service' => 'achievement-diary',
                'type' => 'checkbox'

            ]);
        }

        Toast::success('Service created successfully.');
    }
}
