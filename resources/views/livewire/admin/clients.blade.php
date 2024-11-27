<div class="container mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Управление клиентами</h1>
    @if (auth()->user()->hasPermission('create_clients'))
        <button wire:click="createClient" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
            <i class="fas fa-user-plus"></i> <!-- Иконка добавления клиента -->
        </button>
    @endif
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

    <!-- Фильтры -->
    <div class="flex space-x-2 mb-4">
        <button class="filter-button client-type px-4 py-2 rounded bg-blue-500 text-white" data-filter="all">
            Все
        </button>
        <button class="filter-button client-type px-4 py-2 rounded bg-gray-200" data-filter="individual">
            Физ. лица
        </button>
        <button class="filter-button client-type px-4 py-2 rounded bg-gray-200" data-filter="company">
            Компании
        </button>
        <button class="filter-button supplier-type px-4 py-2 rounded bg-gray-200" data-filter="supplier">
            Поставщики
        </button>
        <button class="filter-button supplier-type px-4 py-2 rounded bg-gray-200" data-filter="client">
            Покупатели
        </button>
    </div>



    {{-- @livewire('clients-table') --}}


    {{-- кастомная таблица --}}

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

        <!-- Таблица -->
        <div id="table" class="fade-in shadow w-full rounded-md overflow-hidden">
            <!-- Шапка таблицы -->
            <div id="header-row" class="grid grid-cols-{{ count($columns) }}">
                @foreach ($columns as $column)
                    <div class="p-2 uppercase cursor-move" data-key="{{ $column }}">
                        {{ str_replace('_', ' ', $column) }}
                    </div>
                @endforeach
            </div>

            <div id="table-body">
                @foreach ($clients as $client)
                    <div class="grid grid-cols-{{ count($columns) }}" data-client-type="{{ $client->client_type }}"
                        data-is-supplier="{{ $client->is_supplier ? 'supplier' : 'client' }}" wire:click="editClient({{ $client->id }})">
                        @foreach ($columns as $column)
                            <div class="p-2" data-key="{{ $column }}">
                                {{ $client->$column ?? '-' }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>


        </div>
    </div>





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
                <button id="cancelClose" class="bg-gray-500 text-white px-4 py-2 rounded">Нет</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
@push('scripts')
    @vite('resources/js/modal.js')
    @vite('resources/js/dragdroptable.js')
    @vite('resources/js/sortcols.js')
    @vite('resources/js/cogs.js')
@endpush

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
<script>
    function resetForm() {
        @this.resetForm(); // Вызов директивы Livewire
    }
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const filterButtons = document.querySelectorAll(".filter-button");
    const rows = document.querySelectorAll("#table-body > div");

    // Применение фильтров
    function applyFilters() {
        const clientTypeFilter = document.querySelector(
            ".filter-button.client-type.active"
        )?.dataset.filter || "all";
        const supplierFilter = document.querySelector(
            ".filter-button.supplier-type.active"
        )?.dataset.filter || "all";

        rows.forEach((row) => {
            const clientType = row.getAttribute("data-client-type");
            const isSupplier = row.getAttribute("data-is-supplier");

            // Условие видимости строки
            const matchesClientType =
                clientTypeFilter === "all" || clientType === clientTypeFilter;
            const matchesSupplierFilter =
                supplierFilter === "all" || isSupplier === supplierFilter;

            // Показываем или скрываем строку
            row.style.display =
                matchesClientType && matchesSupplierFilter ? "grid" : "none";
        });
    }

    // Обработчик клика для кнопок фильтрации
    filterButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const group = button.classList.contains("client-type")
                ? ".client-type"
                : ".supplier-type";

            // Убираем класс active у кнопок из текущей группы
            document.querySelectorAll(`.filter-button${group}`).forEach((btn) => {
                btn.classList.remove("bg-blue-500", "text-white", "active");
                btn.classList.add("bg-gray-200");
            });

            // Добавляем класс active к текущей кнопке
            button.classList.add("bg-blue-500", "text-white", "active");
            button.classList.remove("bg-gray-200");

            // Применяем фильтры
            applyFilters();
        });
    });

    // Применяем фильтры при загрузке страницы
    applyFilters();
});

</script>
