<div class="container mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Перемещение товаров</h1>
    <button wire:click="openForm" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        <i class="fas fa-plus"></i>
    </button>


    <!-- Таблица всех перемещений -->
    <table class="w-full border-collapse border border-gray-200 shadow-md rounded mb-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border border-gray-200">Дата</th>
                <th class="p-2 border border-gray-200">Склад-отправитель</th>
                <th class="p-2 border border-gray-200">Склад-получатель</th>
                <th class="p-2 border border-gray-200">Товар</th>
                <th class="p-2 border border-gray-200">Количество</th>
                <th class="p-2 border border-gray-200">Примечание</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockMovements as $movement)
                <tr wire:click="editTransfer({{ $movement->id }})" class="cursor-pointer">
                    <td class="p-2 border border-gray-200">{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                    <td class="p-2 border border-gray-200">{{ $movement->warehouseFrom->name }}</td>
                    <td class="p-2 border border-gray-200">{{ $movement->warehouseTo->name }}</td>
                    <td class="p-2 border border-gray-200">{{ $movement->product->name }}</td>
                    <td class="p-2 border border-gray-200">{{ $movement->quantity }}</td>
                    <td class="p-2 border border-gray-200">{{ $movement->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Модальное окно для формы перемещения -->
    <div id="modalBackground"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 transition-opacity duration-500 {{ $showForm ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}"
        wire:click="closeForm">
        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50 container mx-auto p-4"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};" wire:click.stop>
            <button wire:click="closeForm"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"style="right: 1rem;">&times;</button>
            <h2 class="text-xl font-bold mb-4">Новое перемещение</h2>
            <!-- Вывод ошибок -->
            @component('components.error-messages')
            @endcomponent

            <!-- Формы выбора складов -->
            <div class="mb-4">
                <label>Склад-отправитель</label>
                <select wire:model.live="selectedWarehouseFrom" class="w-full border rounded"
                    @if ($selectedProducts) disabled @endif>
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


            <!-- Примечание -->
            <div class="mb-4">
                <label>Примечание</label>
                <textarea wire:model="note" class="w-full border rounded"></textarea>
            </div>



            <!-- Таблица выбранных товаров -->

            <h3 class="text-lg font-bold mb-4">Выбранные товары</h3>
            <table class="w-full border-collapse border border-gray-200 shadow-md rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border border-gray-200">Товар</th>
                        <th class="p-2 border border-gray-200">Количество</th>
                        <th class="p-2 border border-gray-200">Действия</th>
                    </tr>
                </thead>
                @if ($selectedProducts)
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
                @endif
            </table>

            <div class="flex justify-start mt-4">
                <button wire:click="saveTransfer" class="bg-green-500 text-white px-4 py-2 rounded mr-2">
                    <i class="fas fa-save"></i>
                </button>
                <button wire:click="deleteTransfer" class="bg-red-500 text-white px-4 py-2 rounded">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            @component('components.confirmation-modal', ['showConfirmationModal' => $showConfirmationModal])
            @endcomponent

            @component('components.product-quantity-modal', ['productModal' => $productModal, 'productQuantity' => $productQuantity])
            @endcomponent
            
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/modal.js')
    @vite('resources/js/dragdroptable.js')
    @vite('resources/js/sortcols.js')
    @vite('resources/js/cogs.js')
@endpush
