<?php

namespace App\Orchid\Screens\Services;

use App\Models\ServiceOption;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Screen;


class MinigameTypesScreen extends Screen
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
        return $this->option?->name . ' Types/Q-tys';
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
                    ->render(function ($magic) {
                        return Group::make([
                            ModalToggle::make('Edit')
                                ->modal('editModal')
                                ->method('edit', ['magic' => $magic->id])
                                ->modalTitle('Edit type ' . $magic->name)
                                ->asyncParameters([
                                    'magic' => $magic->id,
                                ])
                                ->icon('pencil'),


                            Button::make('Delete')
                                ->confirm('Are you sure you want to delete this type?')
                                ->method('delete', ['magic' => $magic->id])
                                ->icon('trash'),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),
            ]),

            Layout::modal('createModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Name')
                        ->required(),

                    Input::make('price')
                        ->title('Price')
                        ->type('number')
                        ->step('0.01')
                        ->default(0)
                        ->placeholder('Price')
                        ->required(),

                    Input::make('express_price')
                        ->type('number')
                        ->title('Express Price')
                        ->step('0.01')
                        ->placeholder('Express Price (Optional) Leave blank if not available'),

                    Matrix::make('createOptions')->columns([
                        'name', 'price', 'express_price'
                    ])
                        ->fields([
                            'name' => Input::make('createOptions.name')
                                ->placeholder('Name')
                                ->required(),
                            'price' => Input::make('createOptions.price')
                                ->type('number')
                                ->step('0.01')
                                ->default(0)
                                ->placeholder('Price')
                                ->required(),

                            'express_price' => Input::make('createOptions.express_price')
                                ->type('number')
                                ->step('0.01')
                                ->placeholder('Express Price (Optional) Leave blank if not available'),
                        ])
                        ->title('Options')
                ]),
            ])->title('Create Type')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Name')
                        ->required(),

                    Input::make('price')
                        ->type('number')
                        ->title('Price')
                        ->step('0.01')
                        ->default(0)
                        ->placeholder('Price')
                        ->required(),

                    Input::make('express_price')
                        ->type('number')
                        ->title('Express Price')
                        ->step('0.01')
                        ->placeholder('Express Price (Optional) Leave blank if not available'),

                    Matrix::make('updateOptions')->columns([
                        'name', 'price', 'express_price'
                    ])
                        ->fields([
                            'name' => Input::make('updateOptions.name')
                                ->placeholder('Name')
                                ->required(),
                            'price' => Input::make('updateOptions.price')
                                ->type('number')
                                ->step('0.01')
                                ->default(0)
                                ->placeholder('Price')
                                ->required(),
                            'express_price' => Input::make('createOptions.express_price')
                                ->type('number')
                                ->step('0.01')
                                ->placeholder('Express Price (Optional) Leave blank if not available'),
                        ])
                        ->title('Options')
                ]),
            ])->async('asyncEdit')
                ->title('Edit Type')
                ->applyButton('Update'),
        ];
    }

    public function delete(ServiceOption $magic)
    {
        $magic->delete();

        Toast::success('Type deleted successfully');
    }

    public function asyncEdit($magic)
    {
        $option = ServiceOption::findOrFail($magic);

        return [
            'name' => $option->name,
            'price' => $option->price,
            'express_price' => $option->children()->where('type', 'checkbox')->first()?->price,
            'updateOptions' => $option->children()->where('type', 'radio')->get()->map(function ($o) {
                return [
                    'name' => $o->name,
                    'price' => $o->price,
                    'express_price' => $o->children()->where('type', 'checkbox')->first()?->price,
                ];
            }),
        ];
    }

    public function create(ServiceOption $option)
    {
        // Validate the request
        request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric:gt:0',
            'express_price' => 'nullable|numeric:gt:0',
            'createOptions' => 'nullable|array',
            'createOptions.*.name' => 'required|string',
            'createOptions.*.price' => 'required|numeric',
            'createOptions.*.express_price' => 'nullable|numeric',
        ]);

        $magic = $option->children()->create([
            'name' => request('name'),
            'price' => request('price'),
            'service' => $option->service,
        ]);

        if (request('express_price')) {
            $magic->children()->create([
                'name' => 'Express',
                'price' => request('express_price'),
                'type' => 'checkbox',
                'service' => $magic->service,
            ]);
        }

        if (request('createOptions')) {
            foreach (request('createOptions') as $op) {
                $o = $magic->children()->create([
                    'name' => $op['name'],
                    'price' => $op['price'],
                    'service' => $magic->service,
                ]);

                if ($op['express_price']) {
                    $o->children()->create([
                        'name' => 'Express',
                        'price' => $op['express_price'],
                        'type' => 'checkbox',
                        'service' => $magic->service,
                    ]);
                }
            }
        }

        Toast::success('The Type created successfully');
    }

    public function edit(ServiceOption $magic)
    {
        // Validate the request
        request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric:gt:0',
            'updateOptions' => 'nullable|array',
            'updateOptions.*.name' => 'required|string',
            'updateOptions.*.price' => 'required|numeric',
        ]);

        $magic->update([
            'name' => request('name'),
            'price' => request('price'),
        ]);

        if (request('express_price')) {
            $magic->children()->updateOrCreate([
                'name' => 'Express',
            ], [
                'name' => 'Express',
                'price' => request('express_price'),
                'service' => $magic->service,
                'type' => 'checkbox',
            ]);
        }

        if (!request('updateOptions')) {
            Toast::success('The Type updated successfully');
            return;
        }

        $magic->children()->where('type', 'radio')->delete();

        foreach (request('updateOptions') as $option) {
            $o = $magic->children()->create([
                'name' => $option['name'],
                'price' => $option['price'],
                'service' => $magic->service,
            ]);

            if ($option['express_price']) {
                $o->children()->create([
                    'name' => 'Express',
                    'price' => $option['express_price'],
                    'service' => $magic->service,
                    'type' => 'checkbox',
                ]);
            }
        }

        Toast::success('The Type updated successfully');
    }
}
