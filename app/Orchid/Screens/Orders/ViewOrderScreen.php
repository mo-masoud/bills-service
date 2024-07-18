<?php

namespace App\Orchid\Screens\Orders;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceOption;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ViewOrderScreen extends Screen
{
    public $order;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Order $order): iterable
    {
        return [
            'order' => $order
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Order: #' . $this->order->id;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Complete the Order'))
                ->icon('bs.check')
                ->method('complete')
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
            Layout::block([
                Layout::legend('order', [
                    Sight::make('ID - Ref')->render(fn () => '<b>#' . $this->order->id . '</b>'),
                    Sight::make('User')->render(fn () => Link::make($this->order->user->name . ' - ' . $this->order->user->email)
                        ->route('platform.systems.users.edit', $this->order->user)),
                    Sight::make('original_price', 'Original Price'),
                    Sight::make('discount_price', 'Discount Price'),
                    Sight::make('total_price', 'Total Price'),
                    Sight::make('Status')->render(fn () => $this->renderStatus($this->order->status)),
                    Sight::make('created_at', 'Date of creation')
                        ->usingComponent(DateTimeSplit::class),
                ])
            ])->title(__('General Information'))
                ->vertical(),

            Layout::block([
                Layout::table('order.skillItems', [
                    TD::make('Skill')->render(fn ($skillItem) => $skillItem->skill->name),
                    TD::make('Boost Method')->render(fn ($skillItem) => $skillItem->boostMethod->name),
                    TD::make('Current Level')->render(fn ($skillItem) => $skillItem->min_level),
                    TD::make('Desired Level')->render(fn ($skillItem) => $skillItem->max_level),
                    TD::make('express')->render(fn ($skillItem) => $skillItem->express ? 'Yes' : 'No'),
                    TD::make('Price')->render(fn ($skillItem) => $skillItem->price),
                ]),
            ])->title(__('Skills'))
                ->vertical(),

            Layout::block([
                Layout::table('order.questItems', [
                    TD::make('Quest')->render(fn ($skillItem) => $skillItem->quest->name),
                    TD::make('Price')->render(fn ($skillItem) => $skillItem->price),
                ]),
            ])->title(__('Quests'))
                ->vertical(),

            Layout::block($this->order->serviceItems->map(function ($serviceItem) {
                return [
                    Layout::legend('order', [
                        Sight::make('Service Name')->render(function () use ($serviceItem) {
                            return $serviceItem->service->name . ' - ' . implode(' - ', $serviceItem->service->allParents()->pluck('name')->toArray());
                        }),
                        Sight::make('Options')->render(function () use ($serviceItem) {
                            return collect($serviceItem->checkboxes)->map(function ($option) {
                                return ServiceOption::find($option)->name;
                            })->implode(' - ');
                        }),
                        Sight::make('Price')->render(fn () => $serviceItem->price),
                    ]),
                ];
            }))->title(__('Services'))
                ->vertical(),
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

    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);
        return redirect()->back();
    }
}
