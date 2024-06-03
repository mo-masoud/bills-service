<?php

namespace App\Orchid\Screens;

use App\Models\Game;
use App\Models\Quest;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class QuestsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'quests' => Quest::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Quests';
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
            Layout::table('quests', [
                TD::make('name'),
                TD::make('game')->render(fn ($quest) => Link::make($quest->game?->name)->route('platform.games.view', $quest->game)),
                TD::make('easy_price'),
                TD::make('medium_price'),
                TD::make('hard_price'),
                TD::make('Actions')
                    ->alignRight()
                    ->cantHide()
                    ->render(function (Quest $quest) {
                        return Group::make([
                            ModalToggle::make('Update')
                                ->modal('updateModal')
                                ->method('update')
                                ->icon('pencil')
                                ->modalTitle('Update Quest ' . $quest->name)
                                ->asyncParameters([
                                    'id' => $quest->id
                                ]),
                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('After deleting, the quest will be gone forever.')
                                ->method('delete', ['quest' => $quest->id]),
                        ])->set('align', 'justify-content-end align-items-center')
                            ->autoWidth();
                    }),

            ]),

            Layout::modal('createModal', Layout::rows([
                Relation::make('quest.game_id')
                    ->required()
                    ->title('Game')
                    ->fromModel(Game::class, 'name'),
                Input::make('quest.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),

                Input::make('quest.easy_price')
                    ->title('Easy Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('quest.medium_price')
                    ->title('Medium Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('quest.hard_price')
                    ->title('Hard Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
            ]))
                ->title('Create Quest')
                ->applyButton('Create'),

            Layout::modal('updateModal', Layout::rows([
                Relation::make('quest.game_id')
                    ->required()
                    ->title('Game')
                    ->fromModel(Game::class, 'name'),
                Input::make('quest.name')
                    ->title('Name')
                    ->type('text')
                    ->required(),

                Input::make('quest.easy_price')
                    ->title('Easy Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('quest.medium_price')
                    ->title('Medium Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),

                Input::make('quest.hard_price')
                    ->title('Hard Price')
                    ->type('number')
                    ->step('0.01')
                    ->required(),
            ]))->async('asyncLoadQuest')
                ->applyButton('Update'),
        ];
    }


    public function asyncLoadQuest(string $id): array
    {
        $quest = Quest::findOrFail($id);
        return [
            'quest' => $quest
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            'quest.game_id' => 'required|exists:games,id',
            'quest.name' => 'required|string',
            'quest.easy_price' => 'required|numeric',
            'quest.medium_price' => 'required|numeric',
            'quest.hard_price' => 'required|numeric',
        ]);

        Quest::create($request->get('quest'));


        Toast::success('Quest created successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'quest.game_id' => 'required|exists:games,id',
            'quest.name' => 'required|string',
            'quest.easy_price' => 'required|numeric',
            'quest.medium_price' => 'required|numeric',
            'quest.hard_price' => 'required|numeric',
        ]);

        $quest = Quest::findOrFail($request->get('id'));
        $quest->update($request->get('quest'));

        Toast::success('Quest updated successfully');
    }

    public function delete(Quest $quest)
    {
        $quest->delete();
        Toast::success('Quest deleted successfully');
    }
}
