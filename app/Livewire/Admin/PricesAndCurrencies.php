<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Currency;

class PricesAndCurrencies extends Component
{
    public $currencies;
    public $exchangeRates = [];

    public function mount()
    {
        $this->currencies = Currency::all();
        foreach ($this->currencies as $currency) {
            $this->exchangeRates[$currency->id] = $currency->exchange_rate;
        }
    }

    public function updateExchangeRate($currencyId)
    {
        $this->validate([
            "exchangeRates.$currencyId" => 'required|numeric|min:0.000001',
        ]);

        $currency = Currency::findOrFail($currencyId);
        $currency->exchange_rate = $this->exchangeRates[$currencyId];
        $currency->save();

        session()->flash('success', 'Курс обновлён.');
    }

    public function setDefaultCurrency($currencyId)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);

        $currency = Currency::findOrFail($currencyId);
        $currency->is_default = true;
        $currency->save();

        session()->flash('success', 'Валюта по умолчанию обновлена.');
    }

    public function render()
    {
        return view('livewire.admin.prices-and-currencies', [
            'currencies' => $this->currencies,
        ]);
    }
}
