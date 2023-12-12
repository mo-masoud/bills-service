<?php

namespace App\Orchid\Layouts\Home;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class HomeLayout extends Rows
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
            Input::make('home.title')
                ->title('Title')
                ->required()
                ->horizontal()
                ->placeholder('Enter title for home page'),
            Quill::make('home.body')
                ->title('Body')
                ->required()
                ->horizontal()
                ->placeholder('Enter body for home page'),
            Input::make('home.video')
                ->title('Home Video')
                ->required()
                ->horizontal()
                ->placeholder('Enter video url for home page')
                ->help('URL is better for saving server storage'),
        ];
    }
}
