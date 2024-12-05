<aside class="w-64 bg-gray-800 text-white flex-shrink-0 transform transition-transform duration-300"
    :class="{ '-translate-x-full': !open, 'translate-x-0': open }">
    <!-- Logo -->
    <?php
    $logo = \App\Models\Setting::where('setting_name', 'company_logo')->value('setting_value');
    ?>

    <div class="shrink-0 flex items-center p-4 justify-center"> <!-- Добавил отступы здесь -->
        <a href="{{ route('dashboard') }}">
            @if ($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Company Logo" class="h-24 w-auto">
            @else
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            @endif
        </a>
    </div>

    <div class="p-4">
        <h2 class="text-lg font-semibold mb-4 p-2">
            {{ \App\Models\Setting::where('setting_name', 'company_name')->value('setting_value') ?? 'Laravel' }}</h2>
        <ul>
            <li class="mb-2">
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-users mr-2"></i> Пользователи
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.roles.index') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-user-shield mr-2"></i> Роли
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.products.index') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fa-solid fa-gifts mr-2"></i> Товары
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.categories.index') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fa fa-list-alt mr-2"></i> Категории
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.warehouse.operations') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fa-solid fa-warehouse mr-2"></i> Склады
                </a>
            </li>
            @if (auth()->user()->hasPermission('view_clients'))
                <li class="mb-2">
                    <a href="{{ route('admin.clients.index') }}"
                        class="flex items-center p-2 hover:bg-gray-700 rounded">
                        <i class="fas fa-user-friends mr-2"></i> Клиенты
                    </a>
                </li>
            @endif
            <li class="mb-2">
                <a href="{{ route('admin.settings.index') }}" class="flex items-center p-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-cogs mr-2"></i> Настройки
                </a>
            </li>
        </ul>
    </div>

</aside>
