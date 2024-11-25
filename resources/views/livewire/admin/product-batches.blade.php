<div>
    <label>Продукт:</label>
    <select wire:model="productId" class="w-full p-2 border rounded">
        <option value="">Выберите продукт</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>

    <label>Номер партии:</label>
    <input type="text" wire:model="batchNumber" class="w-full p-2 border rounded">

    <label>Дата производства:</label>
    <input type="date" wire:model="manufactureDate" class="w-full p-2 border rounded">

    <label>Дата истечения срока:</label>
    <input type="date" wire:model="expireDate" class="w-full p-2 border rounded">

    <label>Количество:</label>
    <input type="number" wire:model="quantity" class="w-full p-2 border rounded">

    <button wire:click="createBatch" class="bg-blue-500 text-white px-4 py-2 rounded">
        Создать партию
    </button>
</div>
