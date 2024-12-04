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
    public $selectedProducts = [];
    public $supplierId;
    public $warehouseId;
    public $invoiceNumber;
    public $date;
    public $comments;
    public $priceInputModal = false;
    public $currentProductId;
    public $productQuantity = 1;
    public $productPrice;
    public $productModal = false;
    public $productSearch;
    public $editingProductId = null;

    public $products = []; // Доступные товары для выбранного склада

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function updatedWarehouseId()
    {
        $this->loadWarehouseProducts();
    }

    public function loadWarehouseProducts()
    {
        $this->products = $this->warehouseId
            ? WarehouseStock::where('warehouse_id', $this->warehouseId)->with('product')->get()->pluck('product')
            : [];
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

    public function saveReception()
    {
        $this->validate([
            'supplierId' => 'required|exists:clients,id',
            'warehouseId' => 'required|exists:warehouses,id',
            'invoiceNumber' => 'nullable|string|max:255',
            'selectedProducts' => 'required|array|min:1',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            ProductPurchase::create([
                'invoice' => $this->invoiceNumber,
                'supplier_id' => $this->supplierId,
                'warehouse_id' => $this->warehouseId,
                'product_id' => $productId,
                'note' => $this->comments,
                'purchase_price' => $details['price'],
                'quantity' => $details['quantity'],
            ]);

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
        $this->reset(['supplierId', 'warehouseId', 'selectedProducts', 'invoiceNumber', 'comments']);
    }

    public function render()
    {
        return view('livewire.admin.stock-reception', [
            'suppliers' => Client::where('is_supplier', true)->get(),
            'warehouses' => Warehouse::all(),
            'products' => $this->products,
            'stockReceptions' => ProductPurchase::with(['supplier', 'warehouse', 'product'])->latest()->get(),
        ]);
    }
}
