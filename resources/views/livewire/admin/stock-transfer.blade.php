<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Перемещение товаров</h1>
    @if (session()->has('error'))
        <div class="text-red-500 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Формы выбора складов -->
    <div class="mb-4">
        <label>Склад-отправитель</label>
        <select wire:model.live="selectedWarehouseFrom" class="w-full border rounded">
            <option value="">Выберите склад</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label>Склад-получатель</label>
        <select wire:model.live="selectedWarehouseTo" class="w-full border rounded">
            <option value="">Выберите склад</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @if ($warehouse->id == $selectedWarehouseFrom) disabled @endif>
                    {{ $warehouse->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Выбор товаров -->
    <div class="mb-4">
        <label>Выберите товары</label>
        <div class="border rounded p-2 max-h-40 overflow-y-auto">
            @foreach ($products as $product)
                <button wire:click="addProduct({{ $product->product_id }})"
                    class="block text-left w-full text-blue-500">
                    {{ $product->product->name }} (Артикул: {{ $product->product->sku }})
                </button>
            @endforeach
        </div>
    </div>

    <!-- Модальное окно для количества -->
    <div style="display: {{ $productModal ? 'block' : 'none' }}"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Укажите количество</h2>
            <div>
                <label>Количество</label>
                <input type="number" wire:model="productQuantity" class="w-full border rounded">
                @error('productQuantity')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="mt-4 flex justify-end">
                <button wire:click="saveProductModal"
                    class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
                <button wire:click="$set('productModal', false)"
                    class="bg-gray-500 text-white px-4 py-2 rounded">Отмена</button>
            </div>
        </div>
    </div>

    <!-- Выбранные товары -->
    @if ($selectedProducts)
        <h3 class="text-lg font-bold mb-4">Выбранные товары</h3>
        <table class="w-full border rounded">
            <thead>
                <tr>
                    <th class="p-2">Товар</th>
                    <th class="p-2">Количество</th>
                    <th class="p-2">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($selectedProducts as $productId => $details)
                    <tr>
                        <td class="p-2">{{ $details['name'] }}</td>
                        <td class="p-2">{{ $details['quantity'] }}</td>
                        <td class="p-2">
                            <button wire:click="openProductModal({{ $productId }})"
                                class="text-blue-500">Редактировать</button>
                            <button wire:click="removeProduct({{ $productId }})"
                                class="text-red-500">Удалить</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Примечание -->
    <div class="mb-4">
        <label>Примечание</label>
        <textarea wire:model="note" class="w-full border rounded"></textarea>
    </div>

    <button wire:click="saveTransfer" class="bg-green-500 text-white px-4 py-2 rounded">Сохранить перемещение</button>

    <!-- Таблица всех перемещений -->
    <h3 class="text-xl font-bold mt-6">История перемещений</h3>
    <table class="w-full border rounded mt-4">
        <thead>
            <tr>
                <th class="p-2">Товар</th>
                <th class="p-2">Склад-отправитель</th>
                <th class="p-2">Склад-получатель</th>
                <th class="p-2">Количество</th>
                <th class="p-2">Примечание</th>
                <th class="p-2">Дата</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockMovements as $movement)
                <tr>
                    <td class="p-2">{{ $movement->product->name }}</td>
                    <td class="p-2">{{ $movement->warehouseFrom->name }}</td>
                    <td class="p-2">{{ $movement->warehouseTo->name }}</td>
                    <td class="p-2">{{ $movement->quantity }}</td>
                    <td class="p-2">{{ $movement->note }}</td>
                    <td class="p-2">{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
