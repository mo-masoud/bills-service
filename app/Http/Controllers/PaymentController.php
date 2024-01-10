<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::whereIsActive(true)->get();
        return response()->json($paymentMethods);
    }

    public function binanceCallback(Request $request)
    {
        Log::info('Binance callback', $request->all());

        try {
            $data = json_decode($request->get('data'), true);

            $order = Order::whereMerchantTradeNo($data['merchantTradeNo'])->firstOrFail();

            if ($request->get('bizStatus') !== 'PAY_SUCCESS') {
                $order->update(['status' => 'canceled', 'cancellation_reason' => 'Payment failed.']);
                return response()->json(['returnCode' => 'FAIL', 'returnMessage' => null]);
            }

            $order->update(['status' => 'completed']);

            return response()->json(['returnCode' => 'SUCCESS', 'returnMessage' => null]);
        } catch (Throwable $th) {
            Log::error($th);
            return response()->json(['returnCode' => 'FAIL', 'returnMessage' => null]);
        }

    }
}
