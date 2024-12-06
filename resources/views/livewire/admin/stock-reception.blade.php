<div class="container mx-auto p-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold mb-4">Оприходования товаров</h1>
        <button wire:click="openReceptionModal" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-plus"></i> Оприходование
        </button>
    </div>

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
                        <td class="p-2 border border-gray-200">{{ $reception->supplier->first_name }}</td>
                        <td class="p-2 border border-gray-200">{{ $reception->warehouse->name }}</td>
                        <td class="p-2 border border-gray-200">
                            {{ $reception->product->name }} ({{ $reception->quantity }} шт. @
                            {{ $reception->purchase_price }} m)
                        </td>
                        <td class="p-2 border border-gray-200">{{ $reception->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
    @if ($errors->any())
        <div class="text-red-500">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($showReceptionModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center"
            style="display: {{ $showReceptionModal ? 'flex' : 'none' }}">
            <div class="relative bg-white p-6 rounded-lg shadow-lg w-2/3">
                <button wire:click="closeReceptionModal"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                    &times;
                </button>
                <h2 class="text-xl font-bold mb-4">Новое оприходование</h2>

                <!-- Поле выбора поставщика -->
                <div class="mb-4">
                    <label class="block">Поставщик</label>
                    <select wire:model="supplierId" class="w-full border rounded">
                        <option value="">Выберите поставщика</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->first_name }}</option>
                        @endforeach
                    </select>
                    @error('supplierId')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block">Накладная №</label>
                    <div class="flex items-center">
                        <input type="text" class="invoice-number border rounded mr-4" placeholder="Введите номер" wire:model="invoiceString">
                    
                        <span class="invoice-date-label mr-4">от</span>
                        <input type="date" class="invoice-date border rounded" wire:model="invoiceDate" value="{{ now()->toDateString() }}">
                    </div>
                </div>
                
                <!-- Поле выбора склада -->
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
                <!-- Поле для поиска товаров -->
                <div class="mb-4">
                    <label class="block">Поиск по товарам</label>
                    <div class="">
                        <!-- Инпут с привязкой к переменной productSearch -->
                        <input type="text" wire:model.live="productSearch"
                            placeholder="Поиск по товарам..." class="w-full border rounded mb-2 p-2" />
                
                        <!-- Отображение списка товаров -->
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
                
                <div
                    class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 {{ $productModal ? 'flex' : 'hidden' }} items-center justify-center">
                    <div class="relative bg-white w-1/3 p-4 rounded shadow-lg">
                        <button wire:click="closeProductModal"
                            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
                            &times;
                        </button>
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
                <!-- Кнопки управления -->
                <div class="flex justify-end mt-4">
                    <button wire:click="closeReceptionModal"
                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Отмена</button>
                    <button wire:click="saveReception"
                        class="bg-green-500 text-white px-4 py-2 rounded">Сохранить</button>
                </div>
            </div>

        </div>
    @endif
    {{-- <button wire:click="saveReception" class="bg-green-500 text-white px-4 py-2 rounded mt-4">Сохранить</button> --}}
</div>
