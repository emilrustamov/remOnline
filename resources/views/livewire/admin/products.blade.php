<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление товарами</h1>
    @if (auth()->user()->hasPermission('view_users'))
        <button wire:click="createProduct" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-plus"></i> Добавить товар
        </button>
    @endif


    {{-- <table class="min-w-full bg-white shadow-md rounded mt-4" id="table">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Название</th>
                <th class="py-2 px-4 border-b">Артикул</th>
                <th class="py-2 px-4 border-b">Количество</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $product->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $product->sku }}</td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <button wire:click="editProduct({{ $product->id }})" class="text-yellow-500">
                            <i class="fas fa-edit"></i>
                        </button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
    <button id="columnsMenuButton" class="bg-gray-500 text-white px-4 py-2 rounded">
        Настроить колонки
    </button>

    <!-- Меню фильтров -->
    <div id="columnsMenu" class="hidden absolute bg-white shadow-md rounded p-4 z-10 mt-2">
        <h2 class="font-bold mb-2">Выберите колонки для отображения:</h2>
        @foreach ($columns as $column)
            <div class="mb-2">
                <label>
                    <input type="checkbox" class="column-toggle" data-column="{{ $column }}" checked>
                    {{ str_replace('_', ' ', $column) }}
                </label>
            </div>
        @endforeach
    </div>
    <div id="table-container" wire:ignore>
        <!-- Скелетон -->
        <div id="table-skeleton" class="animate-pulse">
            <!-- Шапка таблицы -->
            <div id="skeleton-header-row" class="grid grid-cols-{{ count($columns) }}">
                @foreach ($columns as $column)
                    <div class="p-2 h-6 bg-gray-300 rounded"></div>
                @endforeach
            </div>

            <!-- Тело таблицы -->
            @for ($i = 0; $i < 5; $i++) <!-- Генерируем 5 строк скелетона -->
                <div class="grid grid-cols-{{ count($columns) }} gap-4">
                    @foreach ($columns as $column)
                        <div class="p-2 h-6 bg-gray-200 rounded"></div>
                    @endforeach
                </div>
            @endfor
        </div>
        <div id="table" class="fade-in shadow w-full rounded-md overflow-hidden" >
            <div id="header-row" class="grid grid-flow-col auto-cols-auto">
                @foreach ($columns as $column)
                    <div class="p-2 cursor-move whitespace-nowrap" data-key="{{ $column }}">
                        {{ str_replace('_', ' ', $column) }}
                    </div>
                @endforeach
            </div>

            <div id="table-body">
                @foreach ($products as $product)
                    <div class="grid grid-flow-col auto-cols-auto" wire:click="editProduct({{ $product->id }})">
                        @foreach ($columns as $column)
                            <div class="p-2 whitespace-nowrap" data-key="{{ $column }}">
                                    {{ $product->$column }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <div id="modalBackground" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40"
        style="display: {{ $showForm ? 'block' : 'none' }};">
        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};">
            <h2 class="text-xl font-bold mb-4">{{ $productId ? 'Редактировать' : 'Создать' }} товар</h2>
            @if ($showForm)
                <div class="flex items-center space-x-2">
                    <select wire:model="category_id" class="w-full p-2 border rounded">
                        <option value="">Выберите категорию</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Название</label>
                    <input type="text" wire:model="name" placeholder="Название" class="w-full p-2 border rounded">
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1">Описание</label>
                    <input type="text" wire:model="description" placeholder="Описание"
                        class="w-full p-2 border rounded">
                    @error('description')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1">Артикул</label>
                    <input type="text" wire:model="sku" placeholder="Артикул" class="w-full p-2 border rounded">
                    @error('sku')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div>
                    <label class="block mb-1">Количество</label>
                    <input type="number" wire:model="stock_quantity" placeholder="Количество"
                        class="w-full p-2 border rounded">
                    @error('stock_quantity')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div> --}}
                <div>
                    <label>Текущие изображения:</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($images as $index => $image)
                            <div class="relative w-20 h-20">
                                <img src="{{ asset('storage/' . $image) }}" alt="Фото"
                                    class="object-cover w-full h-full rounded">
                                <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1 py-0.5 rounded">
                                    Удалить
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label>Добавить новые изображения:</label>
                    <input type="file" wire:model="newImages" multiple class="w-full p-2 border rounded">
                    @if ($newImages)
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($newImages as $image)
                                <img src="{{ $image->temporaryUrl() }}" alt="Новое изображение"
                                    class="object-cover w-20 h-20 rounded">
                            @endforeach
                        </div>
                    @endif
                    @error('newImages.*')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>


                <div>
                    <label class="block mb-1">Штрих-код (EAN-13)</label>
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model="barcode" readonly
                            class="w-full p-2 border rounded bg-gray-100">
                        <button wire:click="generateBarcodeManually" class="bg-blue-500 text-white px-4 py-2 rounded">
                            <i class="fas fa-barcode"></i> Сгенерировать
                        </button>
                    </div>
                    @if ($barcode)
                        <p class="text-sm text-gray-500">Сгенерирован автоматически.</p>
                    @endif
                </div>

                <div>
                    <input type="checkbox" wire:model="status" class="ml-2">
                    <label for="status">Активный</label>
                </div>


                <div class="mt-4 flex justify-start space-x-2">
                    <button wire:click="saveProduct" class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <button wire:click="resetForm" class="bg-red-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i> Отмена
                    </button>
                    @if ($productId && auth()->user()->hasPermission('view_clients'))
                        <button onclick="confirmDelete({{ $product->id }})"
                            class="bg-red-500 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash-alt"></i> Удалить
                        </button>
                        <div id="deleteConfirmationModal"
                            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
                            style="display: none;">
                            <div class="bg-white w-1/3 p-6 rounded-lg shadow-lg">
                                <h2 class="text-xl font-bold mb-4">Вы уверены, что хотите удалить?</h2>
                                <p>Это действие нельзя отменить.</p>
                                <div class="mt-4 flex justify-end space-x-2">

                                    <button wire:click="deleteProduct({{ $product->id }})" id="confirmDeleteButton"
                                        class="bg-red-500 text-white px-4 py-2 rounded">Да</button>
                                    <button onclick="cancelDelete()"
                                        class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <!-- Модальное окно подтверждения -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;">
        <div class="bg-white w-1/3 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Вы уверены, что хотите закрыть?</h2>
            <p>Все несохранённые данные будут потеряны.</p>
            <div class="mt-4 flex justify-end space-x-2">
                <button id="confirmClose" wire:click="resetForm"
                    class="bg-red-500 text-white px-4 py-2 rounded">Да</button>
                <button id="cancelClose" class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(productId) {
        document.getElementById('deleteConfirmationModal').style.display = 'flex';
    }

    function cancelDelete() {
        document.getElementById('deleteConfirmationModal').style.display = 'none';
    }
</script>

@push('scripts')
    @vite('resources/js/modal.js');
    @vite('resources/js/dragdroptable.js');
    @vite('resources/js/sortcols.js');
    @vite('resources/js/cogs.js');
@endpush
