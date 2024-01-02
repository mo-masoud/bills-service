<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PlaceOrderService
{
    /**
     * @throws ValidationException
     */
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

        $items = collect($items);

        try {
            DB::beginTransaction();

            $order = request()->user()->orders()->create([
                'original_price' => $originalPrice,
                'discount_price' => $discountPrice,
                'total_price' => $originalPrice - $discountPrice,
                'status' => 'pending',
            ]);

            if ($items->where('type', 'quest')->count()) {
                $order->questItems()->createMany(
                    $items->where('type', 'quest')->toArray()
                );
            }

            if ($items->where('type', 'powerlevel')->count()) {
                $order->powerlevelItems()->createMany(
                    $items->where('type', 'powerlevel')->toArray()
                );
            }

            if ($items->where('type', 'service')->count()) {
                $order->serviceItems()->createMany(
                    $items->where('type', 'service')->toArray()
                );
            }

            DB::commit();

            return $order;
        } catch (Throwable $th) {
            DB::rollBack();

            throw ValidationException::withMessages([
                "items" => $th->getMessage(),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function handleOrderItems($items): array
    {
        $data = [];

        foreach ($items as $index => $item) {
            // check on type of item.
            if ($item['type'] === 'powerlevel') {
                $data[] = $this->handlePowerlevelItem($item, $index);
            } elseif ($item['type'] === 'quest') {
                $data[] = $this->handleQuestItem($item, $index);
            } elseif ($item['type'] === 'service') {
                $data[] = $this->handleServiceItem($item, $index);
            }
        }

        return $data;
    }

    /**
     * @throws ValidationException
     */
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
        } catch (Throwable $th) {
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

    /**
     * @throws ValidationException
     */
    private function validateGameAndSkills($game, $item, $index): void
    {
        if (!isset($game->skills[0]->bootMethods[0])) {
            throw ValidationException::withMessages([
                "items.$index.boost_method_id" => 'The boost method id field is not correct or assigned to another game.'
            ]);
        }
        $this->validateOnLevels($game, $item, $index);
    }

    private function validateOnLevels(Game $game, array $item, int $index): void
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

    /**
     * @throws ValidationException
     */
    protected function handleQuestItem(array $item, int|string $index): array
    {
        $game = Game::with([
            'quests' => function ($q) use ($item) {
                $q->whereId($item['quest_id']);
            },
        ])
            ->whereHas('quests')
            ->findOrFail($item['game_id']);

        if (!isset($game->quests[0])) {
            throw ValidationException::withMessages([
                "items.$index.quest_id" => 'The quest id field is not correct or assigned to another game.'
            ]);
        }

        $item['price'] = $game->quests[0]->price;

        return $item;
    }

    /**
     * @throws ValidationException
     */
    protected function handleServiceItem(array $item, int|string $index): array
    {
        $game = Game::with([
            'services' => function ($q) use ($item) {
                $q->whereId($item['service_id']);
            },
        ])
            ->whereHas('services')
            ->findOrFail($item['game_id']);

        if (!isset($game->services[0])) {
            throw ValidationException::withMessages([
                "items.$index.service_id" => 'The service id field is not correct or assigned to another game.'
            ]);
        }

        $item['price'] = $game->services[0]->price;

        return $item;
    }
}
