<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Warehouse;
use App\Models\ProductPurchase;
use App\Models\WarehouseStock;

class StockReception extends Component
{
    public $selectedProducts = []; // Список выбранных товаров
    public $supplierId; // Выбранный поставщик
    public $warehouseId; // Склад
    public $invoiceNumber; // Номер накладной
    public $date; // Дата
    public $comments; // Комментарий
    public $priceInputModal = false; // Открытие окна для цены и количества
    public $currentProductId; // Текущий выбранный продукт для цены и количества
    public $productQuantity = 1; // Количество для текущего продукта
    public $productPrice; // Цена для текущего продукта
    public $productModal = false; // Управляет видимостью модального окна
    public $productSearch;
    public $editingProductId = null;



    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function addProduct($productId)
    {
        if (!isset($this->selectedProducts[$productId])) {
            $product = Product::findOrFail($productId);
            $this->selectedProducts[$productId] = [
                'name' => $product->name,
                'quantity' => 1,
                'price' => null,
            ];
        }
        $this->openPriceInput($productId);
    }

    public function openPriceInput($productId)
    {
        $this->currentProductId = $productId;
        $this->productQuantity = $this->selectedProducts[$productId]['quantity'];
        $this->productPrice = $this->selectedProducts[$productId]['price'];
        $this->priceInputModal = true;
    }

    public function savePriceInput()
    {
        $this->validate([
            'productQuantity' => 'required|integer|min:1',
            'productPrice' => 'required|numeric|min:0',
        ]);

        $this->selectedProducts[$this->currentProductId]['quantity'] = $this->productQuantity;
        $this->selectedProducts[$this->currentProductId]['price'] = $this->productPrice;

        $this->priceInputModal = false;
        $this->currentProductId = null;
    }

    public function removeProduct($productId)
    {
        unset($this->selectedProducts[$productId]);
    }

    public function saveReception()
    {
        $this->validate([
            'supplierId' => 'required|exists:clients,id',
            'warehouseId' => 'required|exists:warehouses,id',
            'invoiceNumber' => 'nullable|string|max:255',
            'selectedProducts' => 'required|array|min:1',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            // Сохранение закупки в таблицу product_purchases
            ProductPurchase::create([
                'invoice' => $this->invoiceNumber,
                'supplier_id' => $this->supplierId,
                'warehouse_id' => $this->warehouseId,
                'product_id' => $productId,
                'note' => $this->comments,
                'purchase_price' => $details['price'],
                'quantity' => $details['quantity'],
            ]);

            // Обновление остатков на складе в таблице warehouse_stocks
            WarehouseStock::updateOrCreate(
                [
                    'warehouse_id' => $this->warehouseId,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => \DB::raw('quantity + ' . $details['quantity']),
                ]
            );
        }

        session()->flash('success', 'Оприходование успешно сохранено.');
        $this->reset();
    }




    public function openProductModal($productId)
    {
        $this->currentProductId = $productId;
        $this->editingProductId = $productId;

        $product = Product::findOrFail($productId);

        $this->productQuantity = $this->selectedProducts[$productId]['quantity'] ?? 1;
        $this->productPrice = $this->selectedProducts[$productId]['price'] ?? null;

        $this->productModal = true;
    }


    public function closeProductModal()
    {
        $this->productModal = false;
        $this->currentProductId = null;
        $this->editingProductId = null; // Сбрасываем
    }

    public function saveProductModal()
    {
        $this->validate([
            'productQuantity' => 'required|integer|min:1',
            'productPrice' => 'required|numeric|min:0',
        ]);

        $this->selectedProducts[$this->currentProductId] = [
            'name' => Product::find($this->currentProductId)->name,
            'quantity' => $this->productQuantity,
            'price' => $this->productPrice,
        ];

        $this->closeProductModal();
    }




    public function render()
{
    return view('livewire.admin.stock-reception', [
        'suppliers' => Client::where('is_supplier', true)->get(),
        'warehouses' => Warehouse::all(),
        'products' => Product::all(),
        'stockReceptions' => ProductPurchase::with(['supplier', 'warehouse', 'product'])->latest()->get(),
    ]);
}

}
