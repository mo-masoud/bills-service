<?php

namespace App\Orchid\Screens\Game;

use App\Models\Game;
use App\Orchid\Layouts\Game\GameFormLayout;
use App\Orchid\Layouts\Game\GamePowerlevelFormLayout;
use App\Orchid\Layouts\Game\GameSkillSectionsFormLayout;
use App\Orchid\Layouts\Game\QuestsFormLayout;
use App\Orchid\Layouts\Game\ServicesFormLayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class GameFormScreen extends Screen
{
    /**
     * @var Game
     */
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
        return 'Games';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Delete Game'))
                ->icon('bs.trash3')
                ->confirm(__('Once the game is deleted, all of its resources and data will be permanently deleted.'))
                ->method('remove')
                ->canSee($this->game->exists),
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
                'General information' => Layout::block(GameFormLayout::class)
                    ->title(__('Game general information'))
                    ->description('Save your game\'s general data for quick and convenient access')
                    ->commands(
                        Button::make($this->game->exists ? 'Update Game' : 'Create Game')
                            ->icon('plus-circle')
                            ->type(Color::BASIC)
                            ->method('saveGame'),
                    ),

                'Powerlevel' => Layout::block(GamePowerlevelFormLayout::class)
                    ->title(__('Powerlevel'))
                    ->description('Save your game\'s powerlevel to allow users to create orders')
                    ->commands(
                        Button::make($this->game->exists ? 'Update Powerlevel' : 'Create Powerlevel')
                            ->icon('plus-circle')
                            ->type(Color::BASIC)
                            ->method('savePowerLevel'),
                    ),

                'Skill Sections' => Layout::block(GameSkillSectionsFormLayout::class)
                    ->title(__('Skill Section'))
                    ->description('Save your game\'s skill sections')
                    ->commands(
                        Button::make($this->game->exists ? 'Update Sections' : 'Create Sections')
                            ->icon('plus-circle')
                            ->type(Color::BASIC)
                            ->method('saveSections'),
                    ),

                'Quests' => Layout::block(QuestsFormLayout::class)
                    ->title(__('Quests'))
                    ->description('Control your game\'s quests')
                    ->commands(
                        Button::make($this->game->exists ? 'Update Quests' : 'Create Quests')
                            ->icon('plus-circle')
                            ->type(Color::BASIC)
                            ->method('saveQuests'),
                    ),

                'Services' => Layout::block(ServicesFormLayout::class)
                    ->title(__('Services'))
                    ->description('Control your game\'s services')
                    ->commands(
                        Button::make($this->game->exists ? 'Update Services' : 'Create Services')
                            ->icon('plus-circle')
                            ->type(Color::BASIC)
                            ->method('saveServices'),
                    ),
            ]),
        ];
    }

    public function saveGame(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game.name' => 'required|string|max:255',
            'game.description' => 'required|string',
            'game.image' => 'required|string',
        ]);

        if (isset($data['game']['powerlevel'])) {
            unset($data['game']['powerlevel']);
        }

        if ($this->game->exists) {
            $this->game->update($data['game']);
            $game = $this->game;

            Toast::success("Game {$data['game']['name']} was updated successfully.");
        } else {
            $game = Game::create($data['game']);

            Toast::success("Game {$data['game']['name']} was created successfully.");
        }

        return redirect()->route('platform.games.edit', $game);
    }

    public function savePowerLevel(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game.powerlevel.description' => 'required|string',
            'game.powerlevel.image' => 'required|string',
            'game.powerlevel.levels' => 'required|numeric|min:1',
            'game.powerlevel.price' => 'required|numeric|min:0',
        ]);

        if (!$this->game->exists) {
            Toast::error('You must create your game first');
            return redirect()->back();
        }

        $this->game->powerlevel()->updateOrCreate([
            'game_id' => $this->game->id,
        ], $data['game']['powerlevel']);

        Toast::success("Powerlevel for game {$this->game->name} was saved successfully.");
        return redirect()->back();
    }

    public function saveSections(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game.sections' => 'nullable|array',
            'game.sections.*.name' => 'required|string',
        ]);

        if (!$this->game->exists) {
            Toast::error('You must create your game first');
            return redirect()->back();
        }

        $savedSections = [];
        foreach ($data['game']['sections'] as $section) {
            $this->game->sections()->updateOrCreate($section, $section);
            $savedSections[] = $section['name'];
        }
        $this->game->sections()->whereNotIn('name', $savedSections)->delete();

        Toast::success("Sections for game {$this->game->name} was saved successfully.");
        return redirect()->back();
    }

    public function saveQuests(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game.quests' => 'nullable|array',
            'game.quests.*.name' => 'required|string',
            'game.quests.*.difficulty' => 'required|string',
            'game.quests.*.price' => 'required|numeric|min:0',
        ]);

        if (!$this->game->exists) {
            Toast::error('You must create your game first');
            return redirect()->back();
        }

        $savedQuests = [];
        foreach ($data['game']['quests'] as $quest) {
            $this->game->quests()->updateOrCreate([
                'game_id' => $this->game->id,
                'name' => $quest['name'],
            ], $quest);
            $savedQuests[] = $quest['name'];
        }
        $this->game->quests()->whereNotIn('name', $savedQuests)->delete();

        Toast::success("Quests for game {$this->game->name} was saved successfully.");
        return redirect()->back();
    }

    public function saveServices(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'game.services' => 'nullable|array',
            'game.services.*.name' => 'required|string',
            'game.services.*.price' => 'required|numeric|min:0',
        ]);

        if (!$this->game->exists) {
            Toast::error('You must create your game first');
            return redirect()->back();
        }

        $savedServices = [];
        foreach ($data['game']['services'] as $quest) {
            $this->game->services()->updateOrCreate([
                'game_id' => $this->game->id,
                'name' => $quest['name'],
            ], $quest);
            $savedServices[] = $quest['name'];
        }
        $this->game->services()->whereNotIn('name', $savedServices)->delete();

        Toast::success("Services for game {$this->game->name} was saved successfully.");
        return redirect()->back();
    }

    public function remove(Game $game): RedirectResponse
    {
        $game->delete();

        Toast::success("Game {$game->name} was deleted successfully.");

        return redirect()->route('platform.games');
    }
}
