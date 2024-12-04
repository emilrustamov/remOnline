<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Оприходования товаров</h1>

    <!-- Таблица всех приходов -->
    <div class="mb-8">
        <table class="w-full border-collapse border border-gray-200 shadow-md rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border border-gray-200">Дата</th>
                    <th class="p-2 border border-gray-200">Поставщик</th>
                    <th class="p-2 border border-gray-200">Склад</th>
                    <th class="p-2 border border-gray-200">Товары</th>
                    <th class="p-2 border border-gray-200">Комментарий</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockReceptions as $reception)
                    <tr>
                        <td class="p-2 border border-gray-200">{{ $reception->created_at->format('d.m.Y') }}</td>
                        <td class="p-2 border border-gray-200">{{ $reception->supplier->name }}</td>
                        <td class="p-2 border border-gray-200">{{ $reception->warehouse->name }}</td>
                        <td class="p-2 border border-gray-200">
                            {{ $reception->product->name }} ({{ $reception->quantity }} шт. @ {{ $reception->purchase_price }} m)
                        </td>
                        <td class="p-2 border border-gray-200">{{ $reception->note }}</td>
                    </tr>
                @endforeach
            </tbody>
            

        </table>
    </div>

    <!-- Форма добавления нового прихода -->
    <h2 class="text-xl font-bold mb-4">Новое оприходование</h2>

    <div class="mb-4">
        <label class="block">Поставщик</label>
        <select wire:model="supplierId" class="w-full border rounded">
            <option value="">Выберите поставщика</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
        @error('supplierId')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block">Склад</label>
        <select wire:model.live="warehouseId" class="w-full border rounded">
            <option value="">Выберите склад</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
        </select>
        @error('warehouseId')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block">Выберите товары</label>
        <div class="border rounded p-2">
            <input type="text" wire:model="productSearch" placeholder="Поиск по товарам..."
                class="w-full border rounded mb-2 p-2">
            <div class="max-h-40 overflow-y-auto">
                @foreach ($products as $product)
                    <div>
                        <button wire:click="openProductModal({{ $product->id }})"
                            class="text-blue-500 w-full text-left">
                            {{ $product->name }} (Артикул: {{ $product->sku }})
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div style="display: {{ $productModal ? 'block' : 'none' }}"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-1/3 p-4 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">
                {{ $editingProductId ? 'Редактировать товар' : 'Добавить товар' }}
            </h2>
            <div class="mb-4">
                <label>Количество</label>
                <input type="number" wire:model="productQuantity" class="w-full border rounded">
                @error('productQuantity')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label>Закупочная цена</label>
                <input type="number" wire:model="productPrice" class="w-full border rounded">
                @error('productPrice')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end">
                <button wire:click="saveProductModal"
                    class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
            </div>
        </div>
    </div>




    <!-- Таблица выбранных товаров -->
    @if ($selectedProducts)
        <h3 class="text-lg font-bold mb-4">Выбранные товары</h3>
        <table class="w-full border-collapse border border-gray-200 shadow-md rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border border-gray-200">Товар</th>
                    <th class="p-2 border border-gray-200">Количество</th>
                    <th class="p-2 border border-gray-200">Цена</th>
                    <th class="p-2 border border-gray-200">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($selectedProducts as $productId => $details)
                    <tr>
                        <td class="p-2 border border-gray-200">{{ $details['name'] }}</td>
                        <td class="p-2 border border-gray-200">{{ $details['quantity'] }}</td>
                        <td class="p-2 border border-gray-200">{{ $details['price'] }}</td>
                        <td class="p-2 border border-gray-200">
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

    <button wire:click="saveReception" class="bg-green-500 text-white px-4 py-2 rounded mt-4">Сохранить</button>
</div>
