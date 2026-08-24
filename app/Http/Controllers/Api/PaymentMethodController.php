<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(
            PaymentMethod::active()->orderBy('name')->get()->map(fn (PaymentMethod $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
            ])
        );
    }
}
