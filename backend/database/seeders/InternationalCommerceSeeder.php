<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Language;
use Illuminate\Database\Seeder;

class InternationalCommerceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['USD', 'US Dollar', '$'], ['EUR', 'Euro', '€'], ['GBP', 'Pound Sterling', '£']] as [$code, $name, $symbol]) {
            $currency = Currency::query()->firstOrCreate(['code' => $code], ['name' => $name, 'symbol' => $symbol]);
            CurrencyRate::query()->firstOrCreate(['currency_id' => $currency->id, 'base_currency' => 'USD'], ['rate' => $code === 'USD' ? 1 : 1, 'source' => 'seed', 'effective_at' => now()]);
        }
        foreach ([['en', 'English'], ['fr', 'French'], ['de', 'German']] as [$code, $name]) {
            Language::query()->firstOrCreate(['code' => $code], ['name' => $name]);
        }
        foreach ([['US', 'United States', 'USD', 'en'], ['GB', 'United Kingdom', 'GBP', 'en'], ['DE', 'Germany', 'EUR', 'de']] as [$code, $name, $currency, $language]) {
            Country::query()->firstOrCreate(['code' => $code], ['name' => $name, 'default_currency' => $currency, 'default_language' => $language]);
        }
    }
}
