<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrdersController extends Controller
{
    public function placeOrder(PlaceOrderRequest $request)
    {
        // handle items
        $items = $this->handleOrderItems($request->items);

        $originalPrice = 0;
        foreach ($items as $item) {
            $originalPrice += $item['price'];
        }

        $discountPrice = 0;

        try {
            DB::beginTransaction();

            $order = $request->user()->orders()->create([
                'original_price' => $originalPrice,
                'discount_price' => $discountPrice,
                'total_price' => $originalPrice - $discountPrice,
                'status' => 'pending',
            ]);

            $order->powerlevelItems()->createMany($items);

            DB::commit();

            return response()->json([
                'message' => 'Order has been placed successfully',
                'order' => new OrderResource($order),
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw ValidationException::withMessages([
                "items" => $th->getMessage(),
            ]);
        }
    }

    private function handleOrderItems($items)
    {
        $data = [];

        foreach ($items as $index => $item) {
            try {
                // arrange
                $game = Game::with([
                    'powerlevel',
                    'skills' => fn ($q) => $q->whereId($item['skill_id']),
                    'skills.prices',
                    'skills.bootMethods' => fn ($q) => $q->where([
                        'skill_id' => $item['skill_id'],
                        'id' => $item['boost_method_id']
                    ]),
                ])
                    ->whereHas('powerlevel')
                    ->whereHas('skills')
                    ->findOrFail($item['game_id']);

                // validate
                if (!isset($game->skills[0]->bootMethods[0])) {
                    throw ValidationException::withMessages([
                        "items.$index.boost_method_id" => 'The boost method id field not correct or assigned to another game, don\'t play with me!.'
                    ]);
                }
                $this->validateOnLevels($game, $item, $index);

                $skill = $game->skills->first();
                $priceRange = $skill->prices
                    ->where('max_level', '>=', $item['desired_level'])
                    ->first();
                if (!$priceRange) {
                    throw ValidationException::withMessages([
                        "items.$index" => "can't detect price range.",
                    ]);
                }

                $levels = abs($item['desired_level'] - $item['current_level']);
                $item['price'] = $priceRange->price * $levels;

                $item['boot_method_id'] = $item['boost_method_id'];
                unset($item['boost_method_id']);

                $data[] = $item;
            } catch (\Throwable $th) {
                throw ValidationException::withMessages([
                    "items" => $th->getMessage(),
                ]);
            }
        }

        return $data;
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
