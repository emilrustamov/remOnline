<div class="container mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Управление клиентами</h1>
    @if (auth()->user()->hasPermission('create_clients'))
        <button wire:click="createClient" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-user-plus"></i> <!-- Иконка добавления клиента -->
        </button>
    @endif
    <div class="relative">
        <button id="settingsButton" class="bg-gray-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-cog"></i>
        </button>

        <div id="columnsMenu" class="hidden bg-white shadow-md rounded p-4 absolute z-10">

            <h2 class="font-bold mb-2">Выберите колонки для отображения:</h2>
            <label><input type="checkbox" class="column-toggle" data-column="Имя" checked> Имя</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="Контактное лицо" checked> Контактное
                лицо</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="Тип" checked> Тип</label><br>
            <label><input type="checkbox" class="column-toggle" data-column="Действия" checked> Действия</label><br>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="flex space-x-2 mb-4">
        <button wire:click="filterClients('all')"
            class="px-4 py-2 rounded {{ $clientTypeFilter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
            Все
        </button>
        <button wire:click="filterClients('individual')"
            class="px-4 py-2 rounded {{ $clientTypeFilter === 'individual' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
            Физ. лица
        </button>
        <button wire:click="filterClients('company')"
            class="px-4 py-2 rounded {{ $clientTypeFilter === 'company' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
            Компании
        </button>
        <button wire:click="filterClients('suppliers')"
            class="px-4 py-2 rounded {{ $supplierFilter === 'suppliers' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
            Поставщики
        </button>
        <button wire:click="filterClients('clients')"
            class="px-4 py-2 rounded {{ $supplierFilter === 'clients' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">
            Покупатели
        </button>
    </div>

    <table class="min-w-full bg-white shadow-md rounded mt-4" id="table" >
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ФИО/Название компании</th>
                <th class="py-2 px-4 border-b">Телефоны</th>
                <th class="py-2 px-4 border-b">Email</th>
                <th class="py-2 px-4 border-b">Контактное лицо</th>
                <th class="py-2 px-4 border-b">Тип</th>
                <th class="py-2 px-4 border-b">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td class="py-2 px-4 border-b">
                        @if ($client->client_type === 'individual')
                            <i class="fa fa-user" aria-hidden="true"></i>
                        @else
                            <i class="fa-solid fa-briefcase"></i>
                        @endif
                        @if ($client->is_conflict)
                            <i class="fa-solid fa-face-angry"></i>
                        @endif
                        @if ($client->is_supplier)
                            <i class="fas fa-truck"></i>
                        @endif

                        {{ $client->first_name }} {{ $client->last_name }}
                    </td>

                    <td class="py-2 px-4 border-b">
                        @foreach ($client->phones as $phone)
                            <a href="tel:{{ $phone->phone }}">{{ $phone->phone }}</a>
                            @if ($phone->is_sms)
                                <i class="fas fa-sms"></i>
                            @endif
                            @if (!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </td>

                    <td class="py-2 px-4 border-b">
                        @foreach ($client->emails as $email)
                            <a href="mailto:{{ $email->email }}">{{ $email->email }}</a>
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            @if (!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </td>


                    <td class="py-2 px-4 border-b">
                        {{ $client->contact_person ? $client->contact_person : 'Нет контактного лица' }}</td>
                    <td class="py-2 px-4 border-b">{{ $client->client_type }}</td>
                    <td class="py-2 px-4 border-b  space-x-2">
                        @if (auth()->user()->hasPermission('edit_clients'))
                            <button wire:click="editClient({{ $client->id }})" class="text-yellow-500">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                        @if (auth()->user()->hasPermission('delete_clients'))
                            <button onclick="confirmDelete({{ $client->id }})" class="text-red-500">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <!-- Модальное окно подтверждения для удаления -->
                            <div id="deleteConfirmationModal"
                                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
                                style="display: none;">
                                <div class="bg-white w-1/3 p-6 rounded-lg shadow-lg">
                                    <h2 class="text-xl font-bold mb-4">Вы уверены, что хотите удалить?</h2>
                                    <p>Это действие нельзя отменить.</p>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <!-- Кнопка "Да" вызывает метод Livewire напрямую -->
                                        <button wire:click="deleteClient({{ $client->id }})" id="confirmDeleteButton"
                                            class="bg-red-500 text-white px-4 py-2 rounded">Да</button>
                                        <button onclick="cancelDelete()"
                                            class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="modalBackground" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 "
        style="display: {{ $showForm ? 'block' : 'none' }};">

        <div id="form"
            class="fixed top-0 right-0 w-1/3 h-full bg-white shadow-lg transform transition-transform duration-500 ease-in-out z-50 container mx-auto px-4"
            style="transform: {{ $showForm ? 'translateX(0)' : 'translateX(100%)' }};">
            <h2 class="text-xl font-bold mb-4">{{ $clientId ? 'Редактировать' : 'Создать' }} клиента</h2>
            @if ($showForm)
                <!-- Поле выбора типа клиента -->
                <div>
                    <label class="block mb-1">Тип клиента:</label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="client_type" value="individual" name="client_type"
                            class="form-radio"> Индивидуальный
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" wire:model.live="client_type" value="company" name="client_type"
                            class="form-radio"> Компания
                    </label>
                </div>

                <!-- Поля флажков -->
                <input type="checkbox" wire:model="isConflict" class="ml-2" {{ $isConflict ? 'checked' : '' }}>
                Конфликтный
                <input type="checkbox" wire:model="isSupplier" class="ml-2" {{ $isSupplier ? 'checked' : '' }}>
                Поставщик

                @error('client_type')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <!-- Поле ввода имени -->
                <label class="block mb-1">Имя</label>
                <input type="text" wire:model.live="first_name" placeholder="Имя"
                    class="w-full p-2 mb-2 border rounded">
                @error('first_name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <!-- Поле ввода фамилии (только для индивидуальных клиентов) -->
                @if ($client_type === 'individual')
                    <label class="block mb-1">Фамилия</label>
                    <input type="text" wire:model="last_name" placeholder="Фамилия"
                        class="w-full p-2 mb-2 border rounded">
                    @error('last_name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                @endif

                <!-- Поле контактного лица (только для компаний) -->
                @if ($client_type === 'company')
                    <label class="block mb-1">Контактное лицо</label>
                    <input type="text" wire:model="contact_person" placeholder="Контактное лицо"
                        class="w-full p-2 mb-2 border rounded">
                    @error('contact_person')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                @endif

                <!-- Поле ввода адреса -->
                <label class="block mb-1">Адрес</label>
                <input type="text" wire:model="address" value="" placeholder="Адрес"
                    class="w-full p-2 mb-2 border rounded">
                @error('address')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <!-- Поля телефонов -->
                <label class="block mt-4">Телефоны:</label>
                @foreach ($phones as $index => $phone)
                    <div class="flex space-x-2 items-center mb-2">
                        <input type="text" wire:model="phones.{{ $index }}.number"
                            placeholder="Введите номер телефона" class="w-full p-2 border rounded">
                        <input type="checkbox" wire:model="phones.{{ $index }}.sms" class="ml-2"
                            {{ $phone['sms'] ? 'checked' : '' }}> SMS
                        <button type="button" wire:click="removePhone({{ $index }})"
                            class="text-red-500">Удалить</button>
                    </div>
                    @error("phones.{$index}.number")
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                @endforeach
                <button type="button" wire:click="addPhone" class="bg-blue-500 text-white px-2 py-1 rounded">
                    <i class="fas fa-plus"></i> Добавить телефон
                </button>

                <!-- Поля email -->
                <label class="block mt-4">Emails:</label>
                @foreach ($emails as $index => $email)
                    <div class="flex space-x-2 items-center mb-2">
                        <input type="text" wire:model="emails.{{ $index }}" placeholder="Введите email"
                            class="w-full p-2 border rounded">
                        <button type="button" wire:click="removeEmail({{ $index }})" class="text-red-500">
                            <i class="fas fa-minus-circle"></i> Удалить
                        </button>
                    </div>
                    @error("emails.{$index}")
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                @endforeach
                <button type="button" wire:click="addEmail" class="bg-blue-500 text-white px-2 py-1 rounded">
                    <i class="fas fa-plus"></i> Добавить email
                </button>

                <!-- Поле ввода адреса -->
                <label class="block mb-1">Заметки</label>
                <input type="text" wire:model="note" value="" placeholder="Заметки"
                    class="w-full p-2 mb-2 border rounded">
                @error('note')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror

                <!-- Кнопки управления -->
                <div class="mt-4 flex justify-start space-x-2">
                    <button wire:click="saveClient" class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                    <button wire:click="resetForm" class="bg-red-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i> Отмена
                    </button>

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
                <button id="cancelClose"
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
