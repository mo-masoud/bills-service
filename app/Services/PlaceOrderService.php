<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrderService
{
    // place function 
    public function place(array $items, string $code = null)
    {

        $items = $this->handleOrderItems($items);

        $originalPrice = 0;
        foreach ($items as $item) {
            $originalPrice += $item['price'];
        }

        $discountPrice = 0;

        if ($code) {
            $coupon = Coupon::validAndAvailable()->whereCode($code)->first();

            // if coupon exists and valid then calculate the discount.
            if ($coupon) {
                $discountPrice = $originalPrice * ($coupon->discount_percentage / 100);

                if ($coupon->maximum_discount_amount) {
                    $discountPrice = min($discountPrice, $coupon->maximum_discount_amount);
                }

                $coupon->increment('number_of_used');
            }
        }

        try {
            DB::beginTransaction();

            $order = request()->user()->orders()->create([
                'original_price' => $originalPrice,
                'discount_price' => $discountPrice,
                'total_price' => $originalPrice - $discountPrice,
                'status' => 'pending',
            ]);

            $order->powerlevelItems()->createMany($items);

            DB::commit();

            return $order;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw ValidationException::withMessages([
                "items" => $th->getMessage(),
            ]);
        }

        return $items;
    }

    private function handleOrderItems($items)
    {
        $data = [];

        foreach ($items as $index => $item) {
            // check on type of item.
            if ($item['type'] === 'powerlevel') {
                $data[] = $this->handlePowerlevelItem($item, $index);
            }
        }

        return $data;
    }

    private function handlePowerlevelItem($item, $index)
    {
        try {
            // arrange
            $game = $this->loadGameWithRelations($item);

            $this->validateGameAndSkills($game, $item, $index);

            $skill = $game->skills->first();

            $priceRange = $this->getPriceRangeForSkill($skill, $item, $index);

            $levels = abs($item['desired_level'] - $item['current_level']);
            $item['price'] = $priceRange->price * $levels;

            $item['boot_method_id'] = $item['boost_method_id'];
            unset($item['boost_method_id']);

            return $item;
        } catch (\Throwable $th) {
            throw ValidationException::withMessages([
                "items" => $th->getMessage(),
            ]);
        }
    }

    private function loadGameWithRelations($item)
    {
        return Game::with([
            'powerlevel',
            'skills' => function ($q) use ($item) {
                $q->whereId($item['skill_id']);
            },
            'skills.prices',
            'skills.bootMethods' => function ($q) use ($item) {
                $q->where([
                    'skill_id' => $item['skill_id'],
                    'id' => $item['boost_method_id']
                ]);
            },
        ])
            ->whereHas('powerlevel')
            ->whereHas('skills')
            ->findOrFail($item['game_id']);
    }

    private function validateGameAndSkills($game, $item, $index)
    {
        if (!isset($game->skills[0]->bootMethods[0])) {
            throw ValidationException::withMessages([
                "items.$index.boost_method_id" => 'The boost method id field is not correct or assigned to another game.'
            ]);
        }
        $this->validateOnLevels($game, $item, $index);
    }

    private function getPriceRangeForSkill($skill, $item, $index)
    {
        $priceRange = $skill->prices
            ->where('max_level', '>=', $item['desired_level'])
            ->first();

        if (!$priceRange) {
            throw ValidationException::withMessages([
                "items.$index" => "Cannot detect price range for the item.",
            ]);
        }

        return $priceRange;
    }

    private function validateOnLevels(Game $game, array $item, int $index)
    {
        // validate on selecting levels.
        if ($item['current_level'] > $game->powerlevel->levels) {
            throw ValidationException::withMessages([
                "items.$index.current_level" => 'The current level field should not be greater than game levels.'
            ]);
        }

        if ($item['desired_level'] > $game->powerlevel->levels) {
            throw ValidationException::withMessages([
                "items.$index.desired_level" => 'The desired level field should not be greater than game levels.'
            ]);
        }

        if ($item['desired_level'] <= $item['current_level']) {
            throw ValidationException::withMessages([
                "items.$index.desired_level" => 'The desired level field should be greater than current level.'
            ]);
        }
    }
}
