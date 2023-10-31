<?php

namespace App\Orchid\Screens\Game;

use App\Models\Game;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class GameViewScreen extends Screen
{
    public $game;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Game $game): iterable
    {
        return [
            'game' => $game
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->game->name;
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Edit'))
                ->route('platform.games.edit', $this->game->id)
                ->icon('bs.pencil'),

            Button::make(__('Delete Game'))
                ->icon('bs.trash3')
                ->confirm(__('Once the game is deleted, all of its resources and data will be permanently deleted.'))
                ->method('remove')
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
            Layout::tabs([
                'General Information' => Layout::block([
                    Layout::legend('game', [
                        Sight::make('id', 'ID'),
                        Sight::make('image')
                            ->render(fn(Game $model) => "<img src='{$model->image}'
                              alt='{$model->name}'
                              class='mw-100 d-block img-fluid rounded-1 w-100'>"),
                        Sight::make('name'),
                        Sight::make('description')
                            ->render(fn($game) => $game->description),
                        Sight::make('created_at', 'Date of creation')->usingComponent(DateTimeSplit::class),
                        Sight::make('updated_at', 'Update date')->usingComponent(DateTimeSplit::class),
                    ]),
                ])->title(__('Game general information')),
                'Powerlevel' => Layout::block([
                    Layout::legend('game', [
                        Sight::make('powerlevel.image', 'Image')
                            ->render(fn(Game $model) => "<img src='{$model->powerlevel->image}'
                              alt='{$model->name}'
                              class='mw-100 d-block img-fluid rounded-1 w-100'>"),
                        Sight::make('description')
                            ->render(fn($game) => $game->powerlevel->description),
                        Sight::make('powerlevel.levels', 'Levels'),
                        Sight::make('powerlevel.price', 'Price Per Level'),
                    ]),
                ])->title(__('Powerlevel'))
                    ->canSee($this->game->powerlevel()->exists()),
            ]),
        ];
    }

    public function remove(Game $game)
    {
        $game->delete();

        Toast::success("Game {$game->name} was deleted successfully.");

        return redirect()->route('platform.games');
    }
}
