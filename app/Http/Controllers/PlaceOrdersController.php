<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Services\PlaceOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PlaceOrdersController extends Controller
{

    public function __construct(protected readonly PlaceOrderService $placeOrderService)
    {
    }

    // validate the coupon code.
    public function validateCouponCode(): JsonResponse
    {
        $coupon = Coupon::validAndAvailable()->whereCode(request('coupon'))->first();

        if (!$coupon) {
            throw ValidationException::withMessages([
                'coupon' => 'The coupon code is invalid or expired.',
            ]);
        }

        return response()->json([
            'message' => 'The coupon code is valid.',
            'status' => 'valid',
            'discount' => $coupon->discount_percentage / 100,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function placeOrder(PlaceOrderRequest $request): JsonResponse
    {
        $placeOrder = $this->placeOrderService->place(
            $request->get('items'),
            $request->get('coupon'),
        );

        return response()->json([
            'message' => 'Order has been placed successfully',
            'status' => 'success',
            'order' => new OrderResource($placeOrder['order']),
        ], 201);
    }
}
