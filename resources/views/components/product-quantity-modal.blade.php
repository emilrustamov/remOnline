<div
    class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 transition-opacity duration-500 {{ $productModal ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }} flex items-center justify-center">
    <div class="relative bg-white w-2/3 p-4 rounded shadow-lg transform transition-transform duration-500 ease-in-out"
        style="transform: {{ $productModal ? 'translateY(0)' : 'translateY(100%)' }};">
        <button wire:click="closeProductModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"
            style="right: 1rem;">
            &times;
        </button>
        <h2 class="text-xl font-bold mb-4">Укажите количество</h2>
        <div class="mb-4">
            <label>Количество</label>
            <input type="number" wire:model="productQuantity" class="w-full border rounded">
        </div>
        <div>
            <button wire:click="saveProductModal" class="bg-green-500 text-white px-4 py-2 rounded"><i
                    class="fas fa-save"></i></button>
        </div>
    </div>
</div>
