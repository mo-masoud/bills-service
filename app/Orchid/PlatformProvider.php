<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make(__('Home'))
                ->icon('orc.home')
                ->route('platform.home')
                ->permission('platform.home')
                ->title(__('System'))
                ->divider(),

            Menu::make(__('Games'))
                ->icon('bs.controller')
                ->route('platform.games')
                ->permission('platform.games')
                ->title(__('Your Games')),

            Menu::make(__('Skills'))
                ->icon('bs.heart')
                ->route('platform.skills')
                ->permission('platform.skills')
                ->divider(),

            Menu::make(__('Fire Cape'))
                ->icon('bs.layers')
                ->title(__('Services'))
                ->route('platform.services.fire-cape'),

            Menu::make(__('Infernal Cape'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('Fortis Colosseum'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),
            Menu::make(__('Quests'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),
            Menu::make(__('Minigames'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('Raids'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('PvM | Bossing'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('Combat Achievements'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('Achievement Diary'))
                ->icon('bs.layers')
                ->route('platform.services.fire-cape'),

            Menu::make(__('Ironman Collecting'))
                ->icon('bs.layers')
                ->divider()
                ->route('platform.services.fire-cape'),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make(__('Orders'))
                ->icon('bs.cart')
                ->route('platform.orders')
                ->permission('platform.orders')
                ->title(__('Orders Management')),

            Menu::make(__('Coupons'))
                ->icon('bs.gift')
                ->route('platform.coupons')
                ->permission('platform.coupons')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users'))
                ->addPermission('platform.home', __('Home')),

            ItemPermission::group(__('Games'))
                ->addPermission('platform.games', __('Games'))
                ->addPermission('platform.skills', __('Skills')),

            ItemPermission::group(__('Orders'))
                ->addPermission('platform.orders', __('Orders'))
                ->addPermission('platform.coupons', __('Coupons')),
        ];
    }
}
