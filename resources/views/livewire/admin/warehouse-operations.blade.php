<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold ">Управление стоком</h1>

    </div>

    <div class="flex items-center space-x-4 mb-6">
        <!-- Фильтр по складу -->
        <div>
            <select wire:model.live="selectedWarehouse" class="w-64 p-2 border rounded">
                <option value="">Выберите склад</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Фильтр по категории -->
        <div>
            <select wire:model.live="categoryFilter" class="w-64 p-2 border rounded">
                <option value="">Все категории</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex">
            <a class="text-green-500 text-lg mr-6" href="{{ route('admin.warehouse.reception') }}">Оприходования</a>
            <a class="text-red-500 text-lg mr-6" href="{{ route('admin.warehouse.write-offs') }}">Списания</a>
            <a class="text-blue-500 text-lg" href="{{ route('admin.warehouse.transfers') }}">Перемещения</a>
        </div>
    </div>

    <!-- Таблица стока -->
    <table class="min-w-full bg-white shadow-md rounded mb-6">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Артикул</th>
                <th class="py-2 px-4 border-b">Наименование</th>
                <th class="py-2 px-4 border-b">В наличии</th>
                <th class="py-2 px-4 border-b">Категория</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockData as $stock)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $stock['sku'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $stock['name'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $stock['stock'] }} шт</td>
                    <td class="py-2 px-4 border-b">{{ $stock['category'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
