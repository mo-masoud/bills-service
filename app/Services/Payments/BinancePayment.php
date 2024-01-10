<?php

namespace App\Services\Payments;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BinancePayment implements Payment
{
    /**
     * @throws Exception
     */
    public function createPayment(array $data)
    {
        $apiKey = config('services.binance.api_key',
            'vgarecle20lbp9x8h2affbrqlpoevtgefwa2asqlijep0o4h8xsfwugvukvfcaeq');
        $apiSecret = config('services.binance.api_secret',
            '8e9zooqat92oatp0uqloxcgvqpyetrixzzqfaoksy3zwthk9eyl3ptauwjybcstg');

        $nonce = Str::random(32);
        $timestamp = round(microtime(true) * 1000);

        $goods = [];
        foreach ($data['items'] as $item) {
            if ($item['type'] === 'powerlevel') {
                $id = $item['skill_id'];
            } elseif ($item['type'] === 'quest') {
                $id = $item['quest_id'];
            } elseif ($item['type'] === 'service') {
                $id = $item['service_id'];
            } else {
                throw new Exception('Invalid item type');
            }

            $goods[] = [
                "goodsType" => "02",
                "goodsCategory" => "Z000",
                "referenceGoodsId" => $id,
                "goodsName" => "{$item['type']}_{$id}",
                "goodsDetail" => "{$item['type']}_{$id}"
            ];
        }

        $request = [
            "env" => [
                "terminalType" => "WEB"
            ],
            "merchantTradeNo" => $data['merchantTradeNo'],
            "orderAmount" => 0.00000001, // TODO:: remove this line and use totalPrice.
            "currency" => "USDT",
            'description' => 'BillService Payment',
            "goodsDetails" => $goods,
        ];


        $json_request = json_encode($request);
        $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
        $signature = strtoupper(hash_hmac('SHA512', $payload, $apiSecret));

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "BinancePay-Timestamp" => $timestamp,
            "BinancePay-Nonce" => $nonce,
            "BinancePay-Certificate-SN" => $apiKey,
            "BinancePay-Signature" => $signature
        ])->post("https://bpay.binanceapi.com/binancepay/openapi/v3/order", $request);

        Log::info('Binance payment', $response->json());
        if ($response->failed()) {
            throw new Exception('Binance payment failed');
        }

        if ($response->json('code') !== '000000') {
            throw new Exception('Binance payment failed');
        }

        if ($response->json('status') !== 'SUCCESS') {
            throw new Exception('Binance payment failed');
        }

        return $response->json('data.checkoutUrl');
    }
}
