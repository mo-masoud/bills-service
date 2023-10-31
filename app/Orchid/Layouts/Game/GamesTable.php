<?php

namespace App\Orchid\Layouts\Game;

use App\Models\Game;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class GamesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'games';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id'),

            TD::make('image')
                ->width('100')
                ->render(fn (Game $model) =>
                "<img src='{$model->image}'
                          alt='{$model->name}'
                          class='mw-100 d-block img-fluid rounded-1 w-100'>"),

            TD::make('name'),

            TD::make('created_at', 'Date of creation')
                ->usingComponent(DateTimeSplit::class),

            TD::make('updated_at', 'Update date')
                ->usingComponent(DateTimeSplit::class),

            TD::make('Actions')
                ->alignRight()
                ->cantHide()
                ->render(fn (Game $game) =>  Group::make([
                    Link::make(__('View'))
                        ->route('platform.games.view', $game->id)
                        ->icon('bs.eye'),

                    Link::make(__('Edit'))
                        ->route('platform.games.edit', $game->id)
                        ->icon('bs.pencil'),
                ])->set('align', 'justify-content-end align-items-center')
                    ->autoWidth())
        ];
    }
}
