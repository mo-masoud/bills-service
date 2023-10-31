<?php

namespace App\Orchid\Screens\Game;

use App\Models\Game;
use App\Orchid\Layouts\Game\GamesTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class GameListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'games' => Game::paginate()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Games';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Game')
                ->route('platform.games.create')
                ->icon('plus-circle')
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
            GamesTable::class,
        ];
    }
}
