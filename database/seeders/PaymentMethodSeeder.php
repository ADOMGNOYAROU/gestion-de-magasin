<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Espèces',
                'code' => 'cash',
                'description' => 'Paiement en espèces',
                'is_active' => true,
            ],
            [
                'name' => 'Carte bancaire',
                'code' => 'card',
                'description' => 'Paiement par carte de crédit/débit',
                'is_active' => true,
            ],
            [
                'name' => 'Chèque',
                'code' => 'check',
                'description' => 'Paiement par chèque',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Money',
                'code' => 'mobile',
                'description' => 'Paiement via mobile money (Orange Money, etc.)',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
