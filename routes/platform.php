<?php

declare(strict_types=1);

use App\Orchid\Screens\Coupons\CouponsScreen;
use App\Orchid\Screens\Game\GameFormScreen;
use App\Orchid\Screens\Game\GameListScreen;
use App\Orchid\Screens\Game\GameViewScreen;
use App\Orchid\Screens\Home\HomeScreen;
use App\Orchid\Screens\Orders\OrdersScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\QuestsScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Services\AchievementDiaryDiffScreen;
use App\Orchid\Screens\Services\AchievementDiaryScreen;
use App\Orchid\Screens\Services\BossingScreen;
use App\Orchid\Screens\Services\CombatAchievementsMonstersScreen;
use App\Orchid\Screens\Services\CombatAchievementsScreen;
use App\Orchid\Screens\Services\FireCapeBuildsScreen;
use App\Orchid\Screens\Services\FireCapeScreen;
use App\Orchid\Screens\Services\FortisColosseumScreen;
use App\Orchid\Screens\Services\FortisColosseumServicesScreen;
use App\Orchid\Screens\Services\InfernalCapeMagicsScreen;
use App\Orchid\Screens\Services\InfernalCapeScreen;
use App\Orchid\Screens\Services\IronmanCollectingResourcesScreen;
use App\Orchid\Screens\Services\IronmanCollectingScreen;
use App\Orchid\Screens\Services\MinigameScreen;
use App\Orchid\Screens\Services\MinigameTypesScreen;
use App\Orchid\Screens\Services\RaidsScreen;
use App\Orchid\Screens\Skills\SkillsScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

//Route::screen('idea', Idea::class, 'platform.screens.idea');

Route::screen('games/{game}/edit', GameFormScreen::class)
    ->name('platform.games.edit')
    ->breadcrumbs(fn (Trail $trail, $game) => $trail
        ->parent('platform.games')
        ->push($game->name, route('platform.games.edit', $game)));

Route::screen('games/create', GameFormScreen::class)
    ->name('platform.games.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.games')
        ->push(__('New Game'), route('platform.games.create')));

Route::screen('games/{game}', GameViewScreen::class)
    ->name('platform.games.view')
    ->breadcrumbs(fn (Trail $trail, $game) => $trail
        ->parent('platform.games')
        ->push($game->name, route('platform.games.view', $game)));

Route::screen('games', GameListScreen::class)
    ->name('platform.games')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Games'), route('platform.games')));

Route::screen('skills', SkillsScreen::class)->name('platform.skills')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Skills'), route('platform.skills')));


Route::screen('quests', QuestsScreen::class)->name('platform.quests')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Quests'), route('platform.quests')));

Route::screen('services/fire-cape', FireCapeScreen::class)->name('platform.services.fire-cape')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Fire Cape'), route('platform.services.fire-cape')));

Route::screen('services/fire-cape/builds/{option}', FireCapeBuildsScreen::class)->name('platform.services.fire-cape.builds')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.fire-cape')
        ->push($option->name . ' ' . __('Builds'), route('platform.services.fire-cape.builds', $option->id)));

Route::screen('services/infernal-cape', InfernalCapeScreen::class)->name('platform.services.infernal-cape')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Infernal Cape'), route('platform.services.infernal-cape')));

Route::screen('services/infernal-cape/magics/{option}', InfernalCapeMagicsScreen::class)->name('platform.services.infernal-cape.magics')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.infernal-cape')
        ->push($option->name . ' ' . __('Magics'), route('platform.services.infernal-cape.magics', $option->id)));

Route::screen('services/fortis-colosseum', FortisColosseumScreen::class)->name('platform.services.fortis-colosseum')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Fortis Colosseum'), route('platform.services.fortis-colosseum')));

Route::screen('services/fortis-colosseum/services/{option}', FortisColosseumServicesScreen::class)->name('platform.services.fortis-colosseum.services')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.fortis-colosseum')
        ->push($option->name . ' ' . __('Character Types'), route('platform.services.fortis-colosseum.services', $option->id)));

Route::screen('services/minigames', MinigameScreen::class)->name('platform.services.minigames')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Minigames'), route('platform.services.minigames')));

Route::screen('services/minigames/types/{option}', MinigameTypesScreen::class)->name('platform.services.minigames.types')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.minigames')
        ->push($option->name . ' ' . __('Types/Q-tys'), route('platform.services.minigames.types', $option->id)));


Route::screen('services/raids', RaidsScreen::class)->name('platform.services.raids')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Raids'), route('platform.services.raids')));

Route::screen('services/pvm-bossing', BossingScreen::class)->name('platform.services.pvm-bossing')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('PvM | Bossing'), route('platform.services.pvm-bossing')));

Route::screen('services/combat-achievements', CombatAchievementsScreen::class)->name('platform.services.combat-achievements')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Combat Achievements / Difficulties'), route('platform.services.combat-achievements')));

Route::screen('services/combat-achievements/{option}', CombatAchievementsMonstersScreen::class)->name('platform.services.combat-achievements.monsters')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.combat-achievements')
        ->push($option->name . ' / ' . __('Monsters / Games'), route('platform.services.combat-achievements.monsters', $option->id)));

Route::screen('services/achievement-diary', AchievementDiaryScreen::class)->name('platform.services.achievement-diary')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Combat Achievements / Difficulties'), route('platform.services.achievement-diary')));

Route::screen('services/achievement-diary/{option}', AchievementDiaryDiffScreen::class)->name('platform.services.achievement-diary.difficulties')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.achievement-diary')
        ->push($option->name . ' / ' . __('Difficulties'), route('platform.services.achievement-diary.difficulties', $option->id)));

Route::screen('services/ironman-collecting', IronmanCollectingScreen::class)->name('platform.services.ironman-collecting')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Ironman Collecting / Types'), route('platform.services.ironman-collecting')));

Route::screen('services/ironman-collecting/{option}', IronmanCollectingResourcesScreen::class)->name('platform.services.ironman-collecting.resources')
    ->breadcrumbs(fn (Trail $trail, $option) => $trail
        ->parent('platform.services.ironman-collecting')
        ->push($option->name . ' / ' . __('Resources'), route('platform.services.ironman-collecting.resources', $option->id)));

Route::screen('home', HomeScreen::class)
    ->name('platform.home')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Home'), route('platform.home')));

Route::screen('orders', OrdersScreen::class)
    ->name('platform.orders')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Orders'), route('platform.orders')));

Route::screen('coupons', CouponsScreen::class)
    ->name('platform.coupons')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Coupons'), route('platform.coupons')));
