<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Quest;
use App\Models\Service;
use App\Models\ServiceOption;
use Illuminate\Validation\ValidationException;

class PlaceOrderService
{

    /**
     * @throws ValidationException
     */
    public function place(array $items, string $code = null)
    {
        $items = collect($items);
        $skills = $items->where('type', 'skill');
        $quests = $items->where('type', 'quest');
        $services = $items->where('type', 'service');

        $originalPrice = 0;

        $skillItems = [];

        // handle skills
        foreach ($skills as $skill) {
            $price = CalculatorService::calculate(
                $skill['skill_id'],
                $skill['boost_method_id'],
                $skill['min_level'],
                $skill['max_level'],
                $skill['express'],
                1
            )['price'];

            $originalPrice += $price;

            $skillItems[] = [
                'skill_id' => $skill['skill_id'],
                'boost_method_id' => $skill['boost_method_id'],
                'min_level' => $skill['min_level'],
                'max_level' => $skill['max_level'],
                'express' => $skill['express'] ?? false,
                'quantity' => 1,
                'price' => $price,
            ];
        }

        // handle quests
        $questItems = [];
        foreach ($quests as $quest) {
            $questModel = Quest::find($quest['quest_id']);

            $questItems[] = [
                'quest_id' => $quest['quest_id'],
                'game_id' => $questModel->game_id,
                'price' => $questModel->price,
            ];

            $originalPrice += $questModel->price;
        }

        // handle services
        $serviceItems = [];

        foreach ($services as $service) {
            $serviceItem = $this->handleService($service);
            $serviceItems[] = $serviceItem;

            $originalPrice += $serviceItem['price'];
        }

        $totalPrice = $originalPrice;
        $discountPrice = 0;

        // handle coupon
        $coupon = null;
        if ($code) {
            $coupon = Coupon::validAndAvailable()->whereCode(request('coupon'))->first();

            if (!$coupon) {
                throw ValidationException::withMessages([
                    'coupon' => 'The coupon code is invalid or expired.',
                ]);
            }

            $discountPrice = $totalPrice * ($coupon->discount_percentage / 100);
        }

        $totalPrice -= $discountPrice;

        $order = Order::create([
            'user_id' => auth()->id(),
            'original_price' => $originalPrice,
            'discount_price' => $discountPrice,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        $order->skillItems()->createMany($skillItems);
        $order->questItems()->createMany($questItems);
        $order->serviceItems()->createMany($serviceItems);

        return $order;
    }

    private function handleService(array $serviceItem)
    {
        // handle service
        $service = ServiceOption::with('parent')->find($serviceItem['service_id']);

        $checkboxesPrice = ServiceOption::whereIn('id', $serviceItem['checkboxes'])->pluck('price')->sum();

        $price = $service->price;
        foreach ($service->allParents() as $parent) {
            $price += $parent->price;
        }

        $price += $checkboxesPrice;

        return [
            'service_option_id' => $serviceItem['service_id'],
            'checkboxes' => $serviceItem['checkboxes'],
            'price' => $price,
        ];
    }
}
