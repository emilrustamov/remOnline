<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Управление ролями</h1>
    <button wire:click="createRole" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        <i class="fas fa-plus"></i> Добавить роль
    </button>

    {{-- <table class="min-w-full bg-white shadow-md rounded mt-4">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Название роли</th>

                <th class="py-2 px-4 border-b">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $role->name }}</td>

                    <td class="py-2 px-4 border-b space-x-2">
                        <button wire:click="editRole({{ $role->id }})" class="text-yellow-500">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="deleteRole({{ $role->id }})" class="text-red-500">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
    @livewire('roles-table')
    <div id="modalBackground" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40"
        style="display: {{ $showForm ? 'block' : 'none' }};">
        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50 container mx-auto px-4"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};">
            <h2 class="text-xl font-bold mb-4">{{ $roleId ? 'Редактировать' : 'Создать' }} роль</h2>
            @if ($showForm)
                <!-- Поле ввода названия роли -->
                <label class="block mb-1">Название роли</label>
                <input type="text" wire:model="name" placeholder="Название роли"
                    class="w-full p-2 mb-2 border rounded">
                @error('name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <!-- Поле выбора пермишенов -->
                <label class="block mb-1">Пермишены</label>
                @foreach ($permissions as $permission)
                    <label class="flex items-center mb-1">
                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->id }}"
                            class="form-checkbox mr-2">
                        {{ $permission->name }}
                    </label>
                @endforeach

                @if ($roleId)
                    <span wire:click="deleteRole({{ $roleId }})" class="text-red-500 cursor-pointer">
                        Удалить
                    </span>
                @endif


                <!-- Кнопки управления -->
                <div class="mt-4 flex justify-start space-x-2">
                    <button wire:click="saveRole" class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <button wire:click="resetForm" class="bg-red-500 text-white px-4 py-2 rounded">
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
                <button id="cancelClose" wire:click="resetForm"
                    class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
            </div>
        </div>
    </div>
</div>


<script>
    //перезагрузка страницы
    document.addEventListener('DOMContentLoaded', () => {

        setTimeout(() => {
            Livewire.on('refreshPage', () => {
                location.reload();
            });
        }, 2000)
    });

    //закрытие модального окна
    function confirmDelete(clientId) {
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
<script>
    function resetForm() {
        @this.resetForm(); // Вызов директивы Livewire
    }
</script>
