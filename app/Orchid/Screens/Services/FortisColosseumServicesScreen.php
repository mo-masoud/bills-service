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

class FortisColosseumServicesScreen extends Screen
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
        return $this->option?->name . ' Character Types';
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
                                ->modalTitle('Edit magic ' . $magic->name)
                                ->asyncParameters([
                                    'magic' => $magic->id,
                                ])
                                ->icon('pencil'),


                            Button::make('Delete')
                                ->confirm('Are you sure you want to delete this magic?')
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

                    Matrix::make('createOptions')->columns([
                        'name', 'price'
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
                        ])
                        ->title('Options')
                ]),
            ])->title('Create Character Type')
                ->applyButton('Create'),

            Layout::modal('editModal', [
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

                    Matrix::make('updateOptions')->columns([
                        'name', 'price'
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
                        ])
                        ->title('Options')
                ]),
            ])->async('asyncEdit')
                ->title('Edit Character Type')
                ->applyButton('Update'),
        ];
    }

    public function delete(ServiceOption $magic)
    {
        $magic->delete();

        Toast::success('Character Type deleted successfully.');
    }

    public function asyncEdit(ServiceOption $magic)
    {
        return [
            'name' => $magic->name,
            'price' => $magic->price,
            'updateOptions' => $magic->children->map(function ($option) {
                return [
                    'name' => $option->name,
                    'price' => $option->price,
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
            'createOptions' => 'required|array',
            'createOptions.*.name' => 'required|string',
            'createOptions.*.price' => 'required|numeric',
        ]);

        $magic = $option->children()->create([
            'name' => request('name'),
            'price' => request('price'),
            'service' => $option->service,
        ]);

        foreach (request('createOptions') as $option) {
            $magic->children()->create([
                'name' => $option['name'],
                'price' => $option['price'],
                'type' => 'checkbox',
                'service' => $magic->service,
            ]);
        }

        Toast::success('The Character Type created successfully');
    }

    public function edit(ServiceOption $magic)
    {
        // Validate the request
        request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric:gt:0',
            'updateOptions' => 'required|array',
            'updateOptions.*.name' => 'required|string',
            'updateOptions.*.price' => 'required|numeric',
        ]);

        $magic->update([
            'name' => request('name'),
            'price' => request('price'),
        ]);

        $magic->children->each(function ($option) {
            $option->delete();
        });

        foreach (request('updateOptions') as $option) {
            $magic->children()->create([
                'name' => $option['name'],
                'price' => $option['price'],
                'type' => 'checkbox',
                'service' => $magic->service,
            ]);
        }

        Toast::success('The Character Type updated successfully');
    }
}
