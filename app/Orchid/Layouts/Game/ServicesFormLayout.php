<?php

namespace App\Orchid\Layouts\Game;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Layouts\Rows;

class ServicesFormLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [
            Matrix::make('game.services')
                ->columns([
                    'name',
                    'price',
                ])->fields([
                    'name' => Input::make('name')
                        ->type('text')
                        ->required(),
                    'price' => Input::make('price')
                        ->type('number')
                        ->step(0.1)
                        ->min(0),
                ]),
        ];
    }
}
