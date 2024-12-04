<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление складами</h1>
    <button wire:click="createWarehouse" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        <i class="fas fa-plus"></i> Добавить склад
    </button>

    <table class="min-w-full bg-white shadow-md rounded">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Название</th>
                <th class="py-2 px-4 border-b">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($warehouses as $warehouse)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $warehouse->name }}</td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <button wire:click="editWarehouse({{ $warehouse->id }})" class="text-yellow-500">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="deleteWarehouse({{ $warehouse->id }})" class="text-red-500">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($showForm)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold">{{ $warehouseId ? 'Редактировать' : 'Создать' }} склад</h2>
                <div>
                    <label>Название</label>
                    <input type="text" wire:model="name" class="w-full p-2 border rounded">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mt-4">
                    <label>Назначить пользователей</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($users as $user)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="accessUsers" value="{{ $user->id }}">
                                <span>{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 flex space-x-2">
                    <button wire:click="saveWarehouse" class="bg-green-500 text-white px-4 py-2 rounded">Сохранить</button>
                    <button wire:click="resetForm" class="bg-gray-500 text-white px-4 py-2 rounded">Отмена</button>
                </div>
            </div>
        </div>
    @endif
</div>
