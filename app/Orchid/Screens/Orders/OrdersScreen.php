<?php

namespace App\Orchid\Screens\Orders;

use App\Models\Order;
use App\Orchid\Layouts\Orders\OrdersTable;
use Orchid\Screen\Action;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;

class OrdersScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'orders' => Order::with('user')
                ->withCount('powerlevelItems', 'questItems', 'serviceItems')
                ->latest()
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Orders';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            OrdersTable::class,
        ];
    }
}
