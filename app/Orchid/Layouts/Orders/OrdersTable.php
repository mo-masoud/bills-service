<?php

namespace App\Orchid\Layouts\Orders;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrdersTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'orders';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('ID - REF')->render(fn ($order) => $order->id),
            TD::make('user')->render(
                fn ($order) =>
                Link::make($order->user->name . ' - ' . $order->user->email)
                    ->route('platform.systems.users.edit', $order->user)
            ),
            TD::make('status')->render(fn ($order) => $this->renderStatus($order->status)),
            TD::make('Items Count')->render(fn ($order) => $order->powerlevel_items_count),
            TD::make('Original Price')->render(fn ($order) => $order->original_price),
            TD::make('Discount Price')->render(fn ($order) => $order->discount_price),
            TD::make('Total Price')->render(fn ($order) => $order->total_price),
            TD::make('Total Price')->render(fn ($order) => $order->total_price),
            TD::make('created_at', 'Date of creation')
                ->usingComponent(DateTimeSplit::class),
        ];
    }

    protected function renderStatus(string $status)
    {
        return match ($status) {
            'pending' => "<div class='text-warning' style='text-transform: capitalize;'>$status</div>",
            'canceled' => "<div class='text-danger' style='text-transform: capitalize;'>$status</div>",
            'completed' => "<div class='text-success' style='text-transform: capitalize;'>$status</div>",
        };
    }
}
