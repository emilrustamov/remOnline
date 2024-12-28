<div class="container mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Списания товаров</h1>
    <button wire:click="openForm" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        <i class="fas fa-plus"></i>
    </button>

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
                <tr wire:click="editWriteOff({{ $writeOff->id }})" class="cursor-pointer">
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
    <div id="modalBackground"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 transition-opacity duration-500 {{ $showForm ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}"
        wire:click="closeForm">
        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50 container mx-auto p-4"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};" wire:click.stop>
            <button wire:click="closeForm"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl" style="right: 1rem;">
                &times;
            </button>
            <h2 class="text-xl font-bold mb-4">{{ $writeOffId ? 'Редактировать списание' : 'Новое списание' }}</h2>

            @component('components.error-messages')
            @endcomponent

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

            <div class="mb-4">
                <label>Причина списания</label>
                <textarea wire:model="reason" class="w-full border rounded"></textarea>
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
                <button wire:click="saveWriteOff" class="bg-green-500 text-white px-4 py-2 rounded mr-2">
                    <i class="fas fa-save"></i>
                </button>
                <button wire:click="deleteWriteOff" class="bg-red-500 text-white px-4 py-2 rounded">
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
@endpush


<script>
    function resetForm() {
        @this.resetForm(); // Вызов директивы Livewire
    }
</script>
