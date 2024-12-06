<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Warehouse;
use App\Models\WarehouseProductReceipt;
use App\Models\WarehouseStock;

class WarehouseStockReception extends Component
{
    public $selectedWarehouse;
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
    public $invoiceString = ''; // Поле для строки
    public $invoiceDate;

    public $products = []; // Доступные товары для выбранного склада

    public $showReceptionModal = false; // Управление отображением модального окна

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        // $this->loadWriteOffs();
    }

    public function openReceptionModal()
    {
        $this->resetForm(); // Сбрасываем данные формы перед открытием
        $this->showReceptionModal = true;
    }

    public function closeReceptionModal()
    {
        $this->showReceptionModal = false;
    }

    public function updatedWarehouseId()
    {
        $this->loadWarehouseProducts();
    }

    // public function loadWarehouseProducts()
    // {
    //     $this->products = $this->warehouseId
    //         ? WarehouseStock::where('warehouse_id', $this->warehouseId)->with('product')->get()->pluck('product')
    //         : [];
    // }
    public function loadWarehouseProducts()
    {
        $query = WarehouseStock::where('warehouse_id', $this->warehouseId)
            ->with('product');

        // Фильтрация по имени или артикулу товара
        if ($this->productSearch != "") {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->productSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
            });
        }

        $this->products = $query->get()->pluck('product');
    }

    public function addProduct($productId)
    {
        $stock = WarehouseStock::where('warehouse_id', $this->selectedWarehouse)
            ->where('product_id', $productId)
            ->with('product') // Подгружаем товар
            ->first();

        if (!$stock) {
            session()->flash('error', 'Товар не найден на складе.');
            return;
        }

        $this->selectedProducts[$productId] = [
            'name' => $stock->product->name ?? 'Название недоступно',
            'quantity' => 1,
        ];

        $this->openProductModal($productId);
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

    public function saveReception()
    {
        $this->validate([
            'supplierId' => 'required|exists:clients,id',
            'warehouseId' =>
                'exists:warehouses,id',
            'invoiceNumber' => 'nullable|string|max:255',
            'selectedProducts' => 'required|array|min:1',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            WarehouseProductReceipt::create([
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
        $this->saveInvoice();
        $this->resetForm();
        $this->closeReceptionModal(); // Закрываем модальное окно после сохранения
    }

    public function saveInvoice()
    {
        $this->validate([
            'invoiceDate' => 'required|date',
            'invoiceString' => 'nullable|string',
        ]);

        $invoice = $this->invoiceDate;
        if (!empty($this->invoiceString)) {
            $invoice = $this->invoiceString . ' ' . $invoice;
        }

        // Для отладки
        \Log::info('Сохраняем накладную:', [
            'warehouse_id' => $this->warehouseId,
            'invoice' => $invoice,
        ]);

        // WarehouseProductReceipt::create([
        //     'warehouse_id' => $this->warehouseId,
        //     'invoice' => $invoice,
        // ]);

        $this->reset(['invoiceString', 'invoiceDate']);

        session()->flash('message', 'Накладная успешно сохранена!');
    }



    public function resetForm()
    {
        $this->supplierId = null;
        $this->warehouseId = null;
        $this->invoiceNumber = null;
        $this->selectedProducts = [];
        $this->comments = null;
    }

    public function render()
    {
        if ($this->supplierId != null && $this->supplierId != '' && $this->warehouseId != null && $this->warehouseId != '') {
            $this->updatedWarehouseId();

        }
        return view('livewire.admin.stock-reception', [
            'suppliers' => Client::where('is_supplier', true)->get(),
            'warehouses' => Warehouse::all(),
            'products' => $this->products,
            'stockReceptions' => WarehouseProductReceipt::with(['supplier', 'warehouse', 'product'])->latest()->get(),
        ]);
    }
}