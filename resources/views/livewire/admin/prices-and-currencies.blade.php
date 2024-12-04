<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление валютами</h1>

    {{-- Сообщение об успешном обновлении --}}
    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Таблица всех валют --}}
    <table class="min-w-full bg-white shadow-md rounded mt-4">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Код валюты</th>
                <th class="py-2 px-4 border-b">Название</th>
                <th class="py-2 px-4 border-b">Символ</th>
                <th class="py-2 px-4 border-b">Курс</th>
                <th class="py-2 px-4 border-b">По умолчанию</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $currency->currency_code }}</td>
                    <td class="py-2 px-4 border-b">{{ $currency->currency_name }}</td>
                    <td class="py-2 px-4 border-b">{{ $currency->symbol }}</td>
                    <td class="py-2 px-4 border-b">
                        <input 
                            type="number" 
                            wire:model.defer="exchangeRates.{{ $currency->id }}" 
                            wire:change="updateExchangeRate({{ $currency->id }})" 
                            step="0.000001" 
                            class="w-full p-2 border rounded" />
                    </td>
                    <td class="py-2 px-4 border-b">
                        <input 
                            type="radio" 
                            wire:click="setDefaultCurrency({{ $currency->id }})" 
                            {{ $currency->is_default ? 'checked' : '' }} />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
