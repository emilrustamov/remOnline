<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление услугами</h1>
    @if (auth()->user()->hasPermission('view_users'))
        <button wire:click="createService" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-plus"></i> Добавить услугу
        </button>
    @endif

    <table class="min-w-full bg-white shadow-md rounded mt-4" id="table">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Название</th>
                <th class="py-2 px-4 border-b">Категория</th>
                <th class="py-2 px-4 border-b">Артикул</th>
                <th class="py-2 px-4 border-b">Статус</th>
                <th class="py-2 px-4 border-b">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $service->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->category->name ?? 'Без категории' }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->articul }}</td>
                    <td class="py-2 px-4 border-b">
                        {{ $service->status ? 'Активна' : 'Неактивна' }}
                    </td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <button wire:click="editService({{ $service->id }})" class="text-yellow-500">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if (auth()->user()->hasPermission('delete_services'))
                            <button onclick="confirmDelete({{ $service->id }})" class="text-red-500">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="modalBackground" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40"
        style="display: {{ $showForm ? 'block' : 'none' }};">
        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};">
            <h2 class="text-xl font-bold mb-4">{{ $serviceId ? 'Редактировать' : 'Создать' }} услугу</h2>
            @if ($showForm)
                <div class="flex items-center space-x-2">
                    <select wire:model="category_id" class="w-full p-2 border rounded">
                        <option value="">Выберите категорию</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
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
                    <textarea wire:model="description" placeholder="Описание" class="w-full p-2 border rounded"></textarea>
                    @error('description')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1">Артикул</label>
                    <input type="text" wire:model="articul" placeholder="Артикул" class="w-full p-2 border rounded">
                    @error('articul')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <input type="checkbox" wire:model="status" class="ml-2">
                    <label for="status">Активна</label>
                </div>

                <div class="mt-4 flex justify-start space-x-2">
                    <button wire:click="saveService" class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <button wire:click="resetForm" class="bg-red-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i> Отмена
                    </button>
                    @if ($serviceId && auth()->user()->hasPermission('delete_services'))
                        <button onclick="confirmDelete({{ $service->id }})" class="bg-red-500 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash-alt"></i> Удалить
                        </button>
                    @endif
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
    function confirmDelete(serviceId) {
        document.getElementById('deleteConfirmationModal').style.display = 'flex';
    }

    function cancelDelete() {
        document.getElementById('deleteConfirmationModal').style.display = 'none';
    }
</script>

@push('scripts')
    @vite('resources/js/dragdroptable.js');
    @vite('resources/js/modal.js');
    @vite('resources/js/cogs.js');
@endpush

