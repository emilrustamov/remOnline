{{-- resources/views/livewire/admin/users.blade.php --}}

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Список пользователей</h1>

    <div class="flex items-center mb-4">
        <button wire:click="createUser" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-4">
            <i class="fas fa-plus"></i> <!-- Иконка добавления -->
        </button>

        <div class="flex space-x-4">
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 font-bold rounded
                      {{ request()->routeIs('admin.users.index') ? 'bg-gray-500 text-white cursor-default pointer-events-none' : 'bg-blue-500 hover:bg-blue-600 text-white' }}">
                <i class="fas fa-users"></i> <!-- Иконка пользователей -->
            </a>
            <a href="{{ route('admin.roles.index') }}"
                class="px-4 py-2 font-bold rounded
                      {{ request()->routeIs('admin.roles.index') ? 'bg-gray-500 text-white cursor-default pointer-events-none' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                <i class="fas fa-user-shield"></i> <!-- Иконка ролей -->
            </a>
        </div>
    </div>

    <table class="table min-w-full bg-white shadow-md rounded mt-4" id="userTable">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Имя</th>
                <th class="py-2 px-4 border-b">Email</th>
                <th class="py-2 px-4 border-b">Должность</th>
                <th class="py-2 px-4 border-b">Дата приема на работу</th>
                <th class="py-2 px-4 border-b">Роли</th>
                <th class="py-2 px-4 border-b">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->position }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->hire_date }}</td>
                    <td class="py-2 px-4 border-b">{{ implode(', ', $user->roles->pluck('name')->toArray()) }}</td>
                    <td class="py-2 px-4 border-b flex space-x-2">
                        <!-- Иконка редактирования -->
                        <button wire:click="editUser({{ $user->id }})"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                            <i class="fas fa-edit"></i>
                        </button>

                        <!-- Иконка активации/деактивации -->
                        <button wire:click="toggleUserStatus({{ $user->id }})"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded">
                            <i class="{{ $user->is_active ? 'fas fa-user-slash' : 'fas fa-user-check' }}"></i>
                        </button>

                        <!-- Иконка удаления -->
                        @if (auth()->user()->hasPermission('delete_users'))
                            <button onclick="confirmDeletion({{ $user->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <script>
                                function confirmDeletion(userId) {
                                    if (confirm('Вы действительно хотите удалить пользователя?')) {
                                        @this.call('deleteUser', userId);
                                    }
                                }
                            </script>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($showForm)
        <div
            class="fixed top-0 right-0 w-1/3 h-full bg-gray-100 shadow-lg p-6 transform transition-transform duration-500 ease-in-out {{ $showForm ? 'translate-x-0' : 'translate-x-full' }}">
            <h2 class="text-xl font-bold mb-4">{{ $userId ? 'Редактировать' : 'Создать' }} пользователя</h2>
            <input type="text" wire:model="name" placeholder="Имя" class="w-full p-2 mb-4 border rounded">
            @error('name')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <input type="email" wire:model="email" placeholder="Email" class="w-full p-2 mb-4 border rounded">
            @error('email')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <input type="password" wire:model="password" placeholder="Пароль" class="w-full p-2 mb-4 border rounded">
            @error('password')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <input type="date" wire:model="hire_date" placeholder="Дата приема на работу"
                class="w-full p-2 mb-4 border rounded">
            @error('hire_date')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <input type="text" wire:model="position" placeholder="Должность" class="w-full p-2 mb-4 border rounded">
            @error('position')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <label>Роли:</label>
            @foreach ($roles as $role)
                <div>
                    <input type="radio" wire:model="roleId" value="{{ $role->id }}">
                    <label>{{ $role->name }}</label>
                </div>
            @endforeach
            @error('roleId')
                <span class="text-red-500">{{ $message }}</span>
            @enderror

            <button wire:click="saveUser" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 mt-4 rounded">
                <i class="fas fa-save"></i> <!-- Иконка сохранения -->
            </button>
            <button wire:click="resetForm" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 mt-4 rounded">
                <i class="fas fa-times"></i> <!-- Иконка отмены -->
            </button>
        </div>
    @endif


</div>
