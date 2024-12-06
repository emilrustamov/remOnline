<div class="container mx-auto p-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold mb-4">Списания товаров</h1>
        <button wire:click="openWriteOffModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-plus"></i> Списание
        </button>
    </div>

    <!-- Таблица всех списаний -->
    <table class="w-full border-collapse border border-gray-200 shadow-md rounded mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border border-gray-200">Дата</th>
                <th class="p-2 border border-gray-200">Склад</th>
                <th class="p-2 border border-gray-200">Товар</th>
                <th class="p-2 border border-gray-200">Количество</th>
                <th class="p-2 border border-gray-200">Причина</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stockWriteOffs as $writeOff)
                <tr>
                    <td class="p-2 border border-gray-200">{{ $writeOff->created_at->format('d.m.Y') }}</td>
                    <td class="p-2 border border-gray-200">{{ $writeOff->warehouse->name }}</td>
                    <td class="p-2 border border-gray-200">{{ $writeOff->product->name }}</td>
                    <td class="p-2 border border-gray-200">{{ $writeOff->quantity }} шт.</td>
                    <td class="p-2 border border-gray-200">{{ $writeOff->reason }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">Данные отсутствуют</td>
                </tr>
            @endforelse
        </tbody>
    </table>

   

    <!-- Модальное окно для формы списания -->
    <div
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 {{ $showWriteOffModal ? 'flex' : 'hidden' }} items-center justify-center">
        <div class="relative bg-white w-1/3 p-4 rounded shadow-lg">
            <button wire:click="closeWriteOffModal"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4">Новое списание</h2>

            <div class="mb-4">
                <label>Склад</label>
                <select wire:model.live="selectedWarehouse" class="w-full border rounded">
                    <option value="">Выберите склад</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block">Поиск по товарам</label>
                <div class="">
                    <!-- Инпут с привязкой к переменной productSearch -->
                    <input type="text" wire:model.live="productSearch" placeholder="Поиск по товарам..."
                        class="w-full border rounded mb-2 p-2" />

                    <!-- Отображение списка товаров -->
                    <div class="max-h-40 overflow-y-auto">
                        @foreach ($products as $product)
                            <div>
                                <button wire:click="addProduct({{ $product->product->id }})"
                                    class="text-blue-500 w-full text-left">
                                    {{ $product->product->name }} (Артикул: {{ $product->product->sku }})
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Модальное окно для указания количества -->
            <div
                class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 {{ $productModal ? 'flex' : 'hidden' }} items-center justify-center">
                <div class="relative bg-white w-1/3 p-4 rounded shadow-lg">
                    <button wire:click="closeProductModal"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                        &times;
                    </button>
                    <h2 class="text-xl font-bold mb-4">Укажите количество</h2>
                    <div class="mb-4">
                        <label>Количество</label>
                        <input type="number" wire:model="productQuantity" class="w-full border rounded">
                        @error('productQuantity')
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
                            <th class="p-2 border border-gray-200">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedProducts as $productId => $details)
                            <tr>
                                <td class="p-2 border border-gray-200">{{ $details['name'] }}</td>
                                <td class="p-2 border border-gray-200">{{ $details['quantity'] }}</td>
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

            <div class="mb-4">
                <label>Причина списания</label>
                <textarea wire:model="reason" class="w-full border rounded"></textarea>
            </div>

            <button wire:click="saveWriteOff" class="bg-red-500 text-white px-4 py-2 rounded">Сохранить
                списание</button>
        </div>
    </div>

</div>
