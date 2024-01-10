<?php

namespace App\Services\Payments;

interface Payment
{
    public function createPayment(array $data);
}
