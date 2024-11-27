<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление категориями</h1>

    {{-- @if (auth()->user()->hasPermission('create_categories')) --}}
    <button wire:click="createCategory" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        <i class="fas fa-plus"></i> Добавить категорию
    </button>
    {{-- @endif --}}

    {{-- <table class="min-w-full bg-white shadow-md rounded mt-4" id="table">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Название</th>
                <th class="py-2 px-4 border-b">Родительская категория</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td class="py-2 px-4 border-b">
                        @if (auth()->user()->hasPermission('view_clients'))
                            <a href="#" wire:click.prevent="editCategory({{ $category->id }})"
                                class="text-blue-500">
                                {{ $category->name }}
                            </a>
                        @else
                            {{ $category->name }}
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b">
                        {{ $category->parent ? $category->parent->name : 'Нет' }}
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
        <div id="table" class="fade-in shadow w-full rounded-md overflow-hidden">
            <div id="header-row" class="grid grid-flow-col auto-cols-auto">
                @foreach ($columns as $column)
                    <div class="p-2 cursor-move whitespace-nowrap" data-key="{{ $column }}">
                        {{ str_replace('_', ' ', $column) }}
                    </div>
                @endforeach
            </div>

            <div id="table-body">
                @foreach ($categories as $category)
                    <div class="grid grid-flow-col auto-cols-auto" wire:click="editCategory({{ $category->id }})">
                        @foreach ($columns as $column)
                            <div class="p-2 whitespace-nowrap" data-key="{{ $column }}">
                                @if ($column == 'parent')
                                    {{ $category->parent ? $category->parent->name : 'Нет' }}
                                @else
                                    {{ $category->$column }}
                                @endif
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
            <h2 class="text-xl font-bold mb-4">{{ $categoryId ? 'Редактировать' : 'Создать' }} категорию</h2>
            @if ($showForm)
                <div>
                    <label class="block mb-1">Название</label>
                    <input type="text" wire:model="name" placeholder="Название" class="w-full p-2 border rounded">
                    @error('name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1">Родительская категория</label>
                    <select wire:model="parent_id" class="w-full p-2 border rounded">
                        <option value="">Нет</option>
                        @foreach ($allCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mt-4 flex justify-start space-x-2">
                    <button wire:click="saveCategory" class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    @if ($categoryId && auth()->user()->hasPermission('view_clients'))
                        @if (!$this->hasProducts($categoryId))
                            <button onclick="confirmDelete({{ $category->id }})"
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
                                        <button wire:click="deleteCategory({{ $category->id }})"
                                            id="confirmDeleteButton"
                                            class="bg-red-500 text-white px-4 py-2 rounded">Да</button>
                                        <button onclick="cancelDelete()"
                                            class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-red-500 mt-2">Категория не может быть удалена, так как к ней привязаны
                                товары.</p>
                        @endif
                    @endif

                    <button wire:click="resetForm" class="bg-gray-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i> Отмена
                    </button>
                </div>

            @endif
        </div>
    </div>
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
    function confirmDelete(categoryId) {
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
