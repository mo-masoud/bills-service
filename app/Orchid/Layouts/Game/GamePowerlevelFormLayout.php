<?php

namespace App\Orchid\Layouts\Game;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layouts\Rows;

class GamePowerlevelFormLayout extends Rows
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
            Picture::make('game.powerlevel.image')
                ->title('Image'),
            Quill::make('game.powerlevel.description')
                ->title('Description')
                ->placeholder('Enter description of the game powerlevel here'),
            Input::make('game.powerlevel.levels')
                ->title('Levels')
                ->type('number')
                ->value(1)
                ->min(1),
            Input::make('game.powerlevel.price')
                ->title('Price Per Level')
                ->type('number')
                ->step(0.1)
                ->min(0),
        ];
    }
}
