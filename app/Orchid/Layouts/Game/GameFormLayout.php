<?php

namespace App\Orchid\Layouts\Game;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Layouts\Rows;

class GameFormLayout extends Rows
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
            Input::make('game.name')
                ->title('Name')
                ->placeholder('Enter name of the game here'),
            Quill::make('game.description')
                ->title('Description')
                ->placeholder('Enter description of the game here'),
            Picture::make('game.image')
                ->title('Image'),
        ];
    }
}
