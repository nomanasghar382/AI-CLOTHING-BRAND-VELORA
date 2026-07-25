<?php

namespace Database\Factories;

use App\Models\EthicalPassport;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class EthicalPassportFactory extends Factory
{
    protected $model = EthicalPassport::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'status' => 'verified',
            'verification_reference' => 'CERT-'.fake()->unique()->bothify('????-####'),
            'verified_by' => fake()->company(),
            'verified_at' => now()->subDay(),
            'claims' => ['materials' => 'Verified organic cotton'],
        ];
    }
}
