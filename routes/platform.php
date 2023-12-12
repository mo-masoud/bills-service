<?php

declare(strict_types=1);

use App\Orchid\Screens\Game\GameFormScreen;
use App\Orchid\Screens\Game\GameListScreen;
use App\Orchid\Screens\Game\GameViewScreen;
use App\Orchid\Screens\Home\HomeScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
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

Route::screen('home', HomeScreen::class)
    ->name('platform.home')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Home'), route('platform.home')));
