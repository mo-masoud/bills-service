<?php

namespace App\Orchid\Screens\Services;

use App\Models\ServiceOption;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FireCapeBuildsScreen extends Screen
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
        return $this->option?->name . ' Builds';
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
                TD::make('Express Price')
                    ->render(function (ServiceOption $option) {
                        return $option->children->first()?->price ?? 'N/A';
                    }),
                TD::make('actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function ($build) {
                        return Group::make([
                            ModalToggle::make('Edit')
                                ->modal('editModal')
                                ->method('edit', ['build' => $build->id])
                                ->modalTitle('Edit Build ' . $build->name)
                                ->asyncParameters([
                                    'build' => $build->id,
                                ])
                                ->icon('pencil'),


                            Button::make('Delete')
                                ->confirm('Are you sure you want to delete this build?')
                                ->method('delete', ['build' => $build->id])
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
                        ->type('number')
                        ->step('0.01')
                        ->title('Price')
                        ->placeholder('Price')
                        ->required(),

                    Input::make('express_price')
                        ->type('number')
                        ->title('Express Price')
                        ->step('0.01')
                        ->placeholder('Express Price (Optional) Leave blank if not available'),
                ]),
            ])->title('Create Build')
                ->applyButton('Create'),

            Layout::modal('editModal', [
                Layout::rows([
                    Input::make('name')
                        ->title('Name')
                        ->placeholder('Name')
                        ->required(),

                    Input::make('price')
                        ->type('number')
                        ->step('0.01')
                        ->title('Price')
                        ->placeholder('Price')
                        ->required(),

                    Input::make('express_price')
                        ->type('number')
                        ->title('Express Price')
                        ->step('0.01')
                        ->placeholder('Express Price (Optional) Leave blank if not available'),
                ]),
            ])->async('asyncEdit')
                ->title('Edit Build')
                ->applyButton('Update'),
        ];
    }

    public function asyncEdit($build)
    {
        $option = ServiceOption::findOrFail($build);

        return [
            'name' => $option->name,
            'price' => $option->price,
            'express_price' => $option->children->first()?->price,
        ];
    }

    public function delete(ServiceOption $build)
    {
        $build->delete();

        Toast::success('Build deleted successfully');
    }

    public function create(ServiceOption $option)
    {
        // Validate the request
        request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'express_price' => 'nullable|numeric|gt:0',
        ]);

        $build = $option->children()->create([
            'name' => request('name'),
            'price' => request('price'),
            'service' => $option->service,
        ]);

        if (request('express_price')) {
            $build->children()->create([
                'name' => 'Express',
                'price' => request('express_price'),
                'service' => $option->service,
                'type' => 'checkbox'
            ]);
        }

        Toast::success('Build created successfully');
    }

    public function edit(ServiceOption $build)
    {
        // Validate the request
        request()->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'express_price' => 'nullable|numeric|gt:0',
        ]);

        $build->update([
            'name' => request('name'),
            'price' => request('price'),
        ]);

        if (request('express_price')) {
            $build->children()->updateOrCreate([
                'name' => 'Express',
            ], [
                'name' => 'Express',
                'price' => request('express_price'),
                'service' => $build->service,
                'type' => 'checkbox'
            ]);
        } else {
            $build->children()->where('name', 'Express')->delete();
        }

        Toast::success('Build updated successfully');
    }
}
